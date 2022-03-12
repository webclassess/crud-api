<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|-------------------------------
| Dynamic Config database
|-------------------------------
*/
/* $CI =& get_instance();
$db =$CI->load->database();
$CI->db->query("CREATE DATABASE IF NOT EXISTS crud_api");
$sql = "CREATE TABLE IF NOT EXISTS tbl_user (
				  `id` bigint(20) NOT NULL PRIMARY KEY AUTO_INCREMENT,
				  `name` varchar(191) NOT NULL,
				  `email` varchar(255) NOT NULL,
				  `mobile` varchar(255) NOT NULL,
				  `role` int(11) COMMENT 1= admin and 2= user,
				  `password` varchar(255) NOT NULL,
				  `image` text NOT NULL,
				  `created_date` date DEFAULT NULL,
				  `modified_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
				) ENGINE=InnoDB DEFAULT CHARSET=utf8";  
*/


/**
* Call page
*/
if ( !function_exists('module_architecture')){
	function module_architecture($view = 'home', $data = array()){
		
		$CI =& get_instance();
		
		$CI->load->view('layout/header.php', $data);
		$CI->load->view($view, $data);
		$CI->load->view('layout/footer.php', $data);
	}
}


