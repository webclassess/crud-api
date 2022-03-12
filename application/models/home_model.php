<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_model extends CI_Model {

	public $user_table = 'user';
	public $slot_table = 'slot';
		 
	 
	public function insertToDatabase($userdetails='')
	{
		$arr = [];
		foreach($userdetails as $k=>$kValues)
		{
			$arr[] = array('name'		=> $kValues['name'],
						  'email' 		=> $kValues['email'],
						  'mobile'  	=> $kValues['mobile'],
						  'role' 		=> $kValues['role'],
						  'password' 	=> $kValues['password'],
						  'image'		=> $kValues['image'],
						  'created_date'=> $kValues['created_date']
						);
		}
		if($this->db->insert_batch($this->user_table, $arr))
		{
			return true;
		}else{
			return false;
		}
	}
	
	public function insertToSlot($data='')
	{
		$arr = array('slot'		=> $data);
		
		if($this->db->insert($this->slot_table, $arr))
		{
			return true;
		}else{
			return false;
		}
	}
	
}
