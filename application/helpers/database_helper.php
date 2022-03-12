<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modifies the database based on specified SQL statements.
 * Useful for creating new tables and updating existing tables to a new structure.
 * @since 1.0.0
 * @param string|array $queries Optional. The query to run. Can be multiple queries
 *                              in an array, or a string of queries separated by
 *                              semicolons. Default empty.
 * @param bool         $execute Optional. Whether or not to execute the query right away.
 *                              Default true.
 * @return array Strings containing the results of the various update queries.
 */
if ( !function_exists('dbDelta')){
	function dbDelta( $queries = '', $execute = true ) {
		$CI =& get_instance();
		// Separate individual queries into an array
		if ( !is_array($queries) ) {
			$queries = explode( ';', $queries );
			$queries = array_filter( $queries );
		} 

		/**
		 * Filters the dbDelta SQL queries.
		 * @param array $queries An array of dbDelta SQL queries.
		 */
		// $queries = apply_filters( 'dbdelta_queries', $queries );

		$cqueries = array(); // Creation Queries
		$iqueries = array(); // Insertion Queries
		$for_update = array();

		// Create a tablename index for an array ($cqueries) of queries
		foreach ($queries as $qry) {
			if ( preg_match( "|CREATE TABLE ([^ ]*)|", $qry, $matches ) ) {
				$cqueries[ trim( $matches[1], '`' ) ] = $qry;
				$for_update[$matches[1]] = 'Created table '.$matches[1];
			} elseif ( preg_match( "|CREATE DATABASE ([^ ]*)|", $qry, $matches ) ) {
				array_unshift( $cqueries, $qry );
			} elseif ( preg_match( "|INSERT INTO ([^ ]*)|", $qry, $matches ) ) {
				$iqueries[] = $qry;
			} elseif ( preg_match( "|UPDATE ([^ ]*)|", $qry, $matches ) ) {
				$iqueries[] = $qry;
			} else {
				// Unrecognized query type
			}
		}
		
		/**
		 * Filters the dbDelta SQL queries for creating tables and/or databases.
		 *
		 * Queries filterable via this hook contain "CREATE TABLE" or "CREATE DATABASE".
		 *
		 * @since 3.3.0
		 *
		 * @param array $cqueries An array of dbDelta create SQL queries.
		 */
		// $cqueries = apply_filters( 'dbdelta_create_queries', $cqueries );

		$text_fields = array( 'tinytext', 'text', 'mediumtext', 'longtext' );
		$blob_fields = array( 'tinyblob', 'blob', 'mediumblob', 'longblob' );

		foreach ( $cqueries as $table => $qry ) {
			
			if ($CI->db->table_exists($table))
			{	
				$tablefields = $CI->db->query("DESCRIBE $table")->result();
			} else {
				continue;
			}			
			
			/* if ( ! $tablefields )
				continue; */

			// Clear the field and index arrays.
			$cfields = $indices = $indices_without_subparts = array();

			// Get all of the field names in the query from between the parentheses.
			preg_match("|\((.*)\)|ms", $qry, $match2);
			$qryline = trim($match2[1]);

			// Separate field lines into an array.
			$flds = explode("\n", $qryline);

			// For every field line specified in the query.
			foreach ( $flds as $fld ) {
				$fld = trim( $fld, " \t\n\r\0\x0B," ); // Default trim characters, plus ','.

				// Extract the field name.
				preg_match( '|^([^ ]*)|', $fld, $fvals );
				$fieldname = trim( $fvals[1], '`' );
				$fieldname_lowercased = strtolower( $fieldname );

				// Verify the found field name.
				$validfield = true;
				switch ( $fieldname_lowercased ) {
					case '':
					case 'primary':
					case 'index':
					case 'fulltext':
					case 'unique':
					case 'key':
					case 'spatial':
						$validfield = false;

						/*
						 * Normalize the index definition.
						 *
						 * This is done so the definition can be compared against the result of a
						 * `SHOW INDEX FROM $table_name` query which returns the current table
						 * index information.
						 */

						// Extract type, name and columns from the definition.
						preg_match(
							  '/^'
							.   '(?P<index_type>'             // 1) Type of the index.
							.       'PRIMARY\s+KEY|(?:UNIQUE|FULLTEXT|SPATIAL)\s+(?:KEY|INDEX)|KEY|INDEX'
							.   ')'
							.   '\s+'                         // Followed by at least one white space character.
							.   '(?:'                         // Name of the index. Optional if type is PRIMARY KEY.
							.       '`?'                      // Name can be escaped with a backtick.
							.           '(?P<index_name>'     // 2) Name of the index.
							.               '(?:[0-9a-zA-Z$_-]|[\xC2-\xDF][\x80-\xBF])+'
							.           ')'
							.       '`?'                      // Name can be escaped with a backtick.
							.       '\s+'                     // Followed by at least one white space character.
							.   ')*'
							.   '\('                          // Opening bracket for the columns.
							.       '(?P<index_columns>'
							.           '.+?'                 // 3) Column names, index prefixes, and orders.
							.       ')'
							.   '\)'                          // Closing bracket for the columns.
							. '$/im',
							$fld,
							$index_matches
						);
						
						// id doesn't exist option data row
						if(count($index_matches) < 1){ return false;}
						
						// Uppercase the index type and normalize space characters.
						$index_type = strtoupper( preg_replace( '/\s+/', ' ', trim( $index_matches['index_type'] ) ) );

						// 'INDEX' is a synonym for 'KEY', standardize on 'KEY'.
						$index_type = str_replace( 'INDEX', 'KEY', $index_type );

						// Escape the index name with backticks. An index for a primary key has no name.
						$index_name = ( 'PRIMARY KEY' === $index_type ) ? '' : '`' . strtolower( $index_matches['index_name'] ) . '`';

						// Parse the columns. Multiple columns are separated by a comma.
						$index_columns = $index_columns_without_subparts = array_map( 'trim', explode( ',', $index_matches['index_columns'] ) );

						// Normalize columns.
						foreach ( $index_columns as $id => &$index_column ) {
							// Extract column name and number of indexed characters (sub_part).
							preg_match(
								  '/'
								.   '`?'                      // Name can be escaped with a backtick.
								.       '(?P<column_name>'    // 1) Name of the column.
								.           '(?:[0-9a-zA-Z$_-]|[\xC2-\xDF][\x80-\xBF])+'
								.       ')'
								.   '`?'                      // Name can be escaped with a backtick.
								.   '(?:'                     // Optional sub part.
								.       '\s*'                 // Optional white space character between name and opening bracket.
								.       '\('                  // Opening bracket for the sub part.
								.           '\s*'             // Optional white space character after opening bracket.
								.           '(?P<sub_part>'
								.               '\d+'         // 2) Number of indexed characters.
								.           ')'
								.           '\s*'             // Optional white space character before closing bracket.
								.        '\)'                 // Closing bracket for the sub part.
								.   ')?'
								. '/',
								$index_column,
								$index_column_matches
							);

							// Escape the column name with backticks.
							$index_column = '`' . $index_column_matches['column_name'] . '`';

							// We don't need to add the subpart to $index_columns_without_subparts
							$index_columns_without_subparts[ $id ] = $index_column;

							// Append the optional sup part with the number of indexed characters.
							if ( isset( $index_column_matches['sub_part'] ) ) {
								$index_column .= '(' . $index_column_matches['sub_part'] . ')';
							}
						}

						// Build the normalized index definition and add it to the list of indices.
						$indices[] = "{$index_type} {$index_name} (" . implode( ',', $index_columns ) . ")";
						$indices_without_subparts[] = "{$index_type} {$index_name} (" . implode( ',', $index_columns_without_subparts ) . ")";

						// Destroy no longer needed variables.
						unset( $index_column, $index_column_matches, $index_matches, $index_type, $index_name, $index_columns, $index_columns_without_subparts );

						break;
				}

				// If it's a valid field, add it to the field array.
				if ( $validfield ) {
					$cfields[ $fieldname_lowercased ] = $fld;
				}
			}

			// For every field in the table.
			foreach ( $tablefields as $tablefield ) {
				$tablefield_field_lowercased = strtolower( $tablefield->Field );
				$tablefield_type_lowercased = strtolower( $tablefield->Type );

				// If the table field exists in the field array ...
				if ( array_key_exists( $tablefield_field_lowercased, $cfields ) ) {

					// Get the field type from the query.
					preg_match( '|`?' . $tablefield->Field . '`? ([^ ]*( unsigned)?)|i', $cfields[ $tablefield_field_lowercased ], $matches );
					$fieldtype = $matches[1];
					$fieldtype_lowercased = strtolower( $fieldtype );

					// Is actual field type different from the field type in query?
					if ($tablefield->Type != $fieldtype) {
						$do_change = true;
						if ( in_array( $fieldtype_lowercased, $text_fields ) && in_array( $tablefield_type_lowercased, $text_fields ) ) {
							if ( array_search( $fieldtype_lowercased, $text_fields ) < array_search( $tablefield_type_lowercased, $text_fields ) ) {
								$do_change = false;
							}
						}

						if ( in_array( $fieldtype_lowercased, $blob_fields ) && in_array( $tablefield_type_lowercased, $blob_fields ) ) {
							if ( array_search( $fieldtype_lowercased, $blob_fields ) < array_search( $tablefield_type_lowercased, $blob_fields ) ) {
								$do_change = false;
							}
						}

						if ( $do_change ) {
							// Add a query to change the column type.
							$cqueries[] = "ALTER TABLE {$table} CHANGE COLUMN `{$tablefield->Field}` " . $cfields[ $tablefield_field_lowercased ];
							$for_update[$table.'.'.$tablefield->Field] = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
						}
					}

					// Get the default value from the array.
					if ( preg_match( "| DEFAULT '(.*?)'|i", $cfields[ $tablefield_field_lowercased ], $matches ) ) {
						$default_value = $matches[1];
						if ($tablefield->Default != $default_value) {
							// Add a query to change the column's default value
							$cqueries[] = "ALTER TABLE {$table} ALTER COLUMN `{$tablefield->Field}` SET DEFAULT '{$default_value}'";
							$for_update[$table.'.'.$tablefield->Field] = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
						}
					}

					// Remove the field from the array (so it's not added).
					unset( $cfields[ $tablefield_field_lowercased ] );
				} else {
					// This field exists in the table, but not in the creation queries?
				}
			}

			// For every remaining field specified for the table.
			foreach ($cfields as $fieldname => $fielddef) {
				// Push a query line into $cqueries that adds the field to that table.
				$cqueries[] = "ALTER TABLE {$table} ADD COLUMN $fielddef";
				$for_update[$table.'.'.$fieldname] = 'Added column '.$table.'.'.$fieldname;
			}

			// Index stuff goes here. Fetch the table index structure from the database.
			$tableindices = $CI->db->query("SHOW INDEX FROM {$table};")->result();

			if ($tableindices) {
				// Clear the index array.
				$index_ary = array();

				// For every index in the table.
				foreach ($tableindices as $tableindex) {

					// Add the index to the index data array.
					$keyname = strtolower( $tableindex->Key_name );
					$index_ary[$keyname]['columns'][] = array('fieldname' => $tableindex->Column_name, 'subpart' => $tableindex->Sub_part);
					$index_ary[$keyname]['unique'] = ($tableindex->Non_unique == 0)?true:false;
					$index_ary[$keyname]['index_type'] = $tableindex->Index_type;
				}

				// For each actual index in the index array.
				foreach ($index_ary as $index_name => $index_data) {

					// Build a create string to compare to the query.
					$index_string = '';
					if ($index_name == 'primary') {
						$index_string .= 'PRIMARY ';
					} elseif ( $index_data['unique'] ) {
						$index_string .= 'UNIQUE ';
					}
					if ( 'FULLTEXT' === strtoupper( $index_data['index_type'] ) ) {
						$index_string .= 'FULLTEXT ';
					}
					if ( 'SPATIAL' === strtoupper( $index_data['index_type'] ) ) {
						$index_string .= 'SPATIAL ';
					}
					$index_string .= 'KEY ';
					if ( 'primary' !== $index_name  ) {
						$index_string .= '`' . $index_name . '`';
					}
					$index_columns = '';

					// For each column in the index.
					foreach ($index_data['columns'] as $column_data) {
						if ( $index_columns != '' ) {
							$index_columns .= ',';
						}

						// Add the field to the column list string.
						$index_columns .= '`' . $column_data['fieldname'] . '`';
					}

					// Add the column list to the index create string.
					$index_string .= " ($index_columns)";

					// Check if the index definition exists, ignoring subparts.
					if ( ! ( ( $aindex = array_search( $index_string, $indices_without_subparts ) ) === false ) ) {
						// If the index already exists (even with different subparts), we don't need to create it.
						unset( $indices_without_subparts[ $aindex ] );
						unset( $indices[ $aindex ] );
					}
				}
			}

			// For every remaining index specified for the table.
			foreach ( (array) $indices as $index ) {
				// Push a query line into $cqueries that adds the index to that table.
				$cqueries[] = "ALTER TABLE {$table} ADD $index";
				$for_update[] = 'Added index ' . $table . ' ' . $index;
			}
			
			// Remove the original table creation query from processing.
			unset( $cqueries[ $table ], $for_update[ $table ] );
		}

		$allqueries = array_merge($cqueries, $iqueries);
		if ($execute) {
			foreach ($allqueries as $query) {
				$CI->db->query($query);
			}
		}
		return $for_update;
	}
}

/**
 * Get option value using option name.
 */
if ( !function_exists('getOptionValue')){
	function getOptionValue($db_opt_name = ''){
		if(empty($db_opt_name)){
			return false;
		}
		$table_name = "options";
		$CI =& get_instance();
		
		if(!$CI->db->table_exists($table_name)){
			return false;
		}
			
		$CI->db->select("o.option_value");
		$result = $CI->db->get_where("$table_name as o", array("o.option_name" =>$db_opt_name) );
		if($result->num_rows() > 0){
			return $result->row();
		}else{
			return false;
		}
	}
}

/**
 * Update option value based on option name.
 */
if ( !function_exists('updateOptionValue')){
	function updateOptionValue($db_opt_name = '', $db_opt_value = ''){
		if(empty($db_opt_name) || empty($db_opt_value) ){
			return;
		}
		$table_name = "options";
		$CI =& get_instance();
		// Check option name availble or not.
		$CI->db->where(array("option_name" =>$db_opt_name));
		$isData = $CI->db->count_all_results($table_name);
		
		if($isData > 0){
			$data = array('option_value' => $db_opt_value);
			$CI->db->where(array("option_name" =>$db_opt_name));
			$result = $CI->db->update($table_name,  $data);
		}else{
			$data = array('option_name' => $db_opt_name, 'option_value' => $db_opt_value);
			$result = $CI->db->insert($table_name,  $data);
		}		
		
		if($result){
			return true;
		}else{
			return false;
		}
	}
}

/**
* Module array list.
*/
if( !function_exists('module_array') ){
	function module_array(){
		// key is slug, value is name.
		$module_list = array(
							"generator" 			=> "Generators",
							"users-management" 		=> "User Management",
							"role-management" 		=> "Role Management",
							"generator-group" 		=> "Generators Group",
							"tables-create" 		=> "Create/Update Tables",
							"module" 				=> "Module Management",
							"assign-generator" 		=> "Assign Generator",
							"generator-settings" 	=> "Generator Settings",
							"notification" 			=> "Notifications",	
							"declared-capacities" 	=> "Declared Capacities",
							"generator-scheduling" 	=> "Generator Scheduling",
							"day-ahead-scheduling" 	=> "Day ahead Scheduling",	
							"view-schedule" 		=> "View Schedule",
							"menu-groups" 			=> "Menu Groups",
							"declared-capacities" 	=> "Declared Capacities", 
							"generator-scheduling"	=> "Generator Scheduling",
							"transmission-losses"	=> "Transmission Losses",
							"srldc-web-api"			=> "SRLDC Web API",
							"view-discom"			=> "View Discom",
							"assign-discom"			=> "Assign Discom",
							"discom-requisitions"	=> "Discom Requisitions",
							"discom-requisitions-availability"=>"Discom Requisitions Availability",
							"transmission-bus-details"=>"Transmission Bus Details",
							"generator-bus-link"	=>"Generator Bus Link",							
							"transformer-details"	=>"Transformer Details",
							"transmission-line-details"	=>"Transmission Line Details",
							"state-settings"	=>"State Settings"
						);

		return $module_list;		
		
	}
}

/**
* Roles array list.
*/
if( !function_exists('roles_array') ){
	function roles_array(){
		// key is role_name, value is description.
		$roles_list = array( "System Admin" => "Admin" );
		return $roles_list;
	}
}

/**
* Get data from table.
*/
if( !function_exists('get_data_by_table') ){
	function get_data_by_table($tblName='', $condition='', $colName=''){
		$CI = & get_instance();
		if( !empty($tblName) ){
			if( !empty($colName) ){
				$CI->db->select($colName);
			}
			if( !empty($condition) ){
				$CI->db->where($condition);
			}
			$result = $CI->db->get($tblName);
			if($result->num_rows() > 0){
				if( ($result->num_rows() == 1) && !empty($colName) ){
					return $result->row();
				}
				else {
					return $result->result_array();
				}
				
			}else{
				return false;
			}
		}else{
			return false;
		}
		
	}
}



