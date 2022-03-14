<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('home_model');
	}
	
	
	public function index()
	{
		$data = array();
		$data['task'] = '<a class="float-right text-white" href="'.base_url('jscode').'"> Click here for Task-2</a>';
		
		if($this->input->post() && $this->input->post('finalDatabase') == NULL)
		{
			$dirname = strtolower($this->input->post('mobile')).rand(10,100);
			$filesDir = "assets/uploads/".$dirname;
				
				if(!is_dir($filesDir)){
					mkdir($filesDir, 0755);
				}
				
				$uploadImage = '';
				if(!empty($_FILES['image']['name']))
				{
					$_FILES['file']['name']     =  mt_rand().'-'.$_FILES['image']['name'];
					$_FILES['file']['type']     = $_FILES['image']['type'];
					$_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
					$_FILES['file']['error']     = $_FILES['image']['error'];
					$_FILES['file']['size']     = $_FILES['image']['size'];
					
					$config['upload_path'] = $filesDir.'/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif';
					$config['file_name'] = $_FILES['file']['name'];
					
					// Load and initialize upload library
					$this->load->library('upload', $config);
					$this->upload->initialize($config);						
					
					// Upload file to server
					if($this->upload->do_upload('file')){
						// Uploaded file data
						$fileData = $this->upload->data();
						$uploadImage = $filesDir.'/'.$fileData['file_name'];							
					}
				}
				
				$j = 0;
				if($this->session->userdata('userdetails') != NULL)
				{
					$userdetails = $this->session->userdata('userdetails');
					//$j = count($allUser) + 1;
				}
				$userdetails[] = array('name'		=> $this->input->post('name'),
								  'email' 		=> $this->input->post('email'),
								  'mobile'  	=> $this->input->post('mobile'),
								  'role' 		=> $this->input->post('role'),
								  'password' 	=> $this->input->post('password'),
								  'image'		=> $uploadImage,
								  'created_date'=> $this->input->post('created_date'),
								  'dirname'		=> $dirname
								);
			
			$this->session->set_userdata('userdetails', $userdetails);
			redirect(base_url());
		}
		
		if($this->session->userdata('userdetails') != NULL)
		{
			$data['userdetails'] = $this->session->userdata('userdetails');
		}
		
		
		/**
		* Finally submit to database
		*/
		$data['final_stage'] = '';
		if($this->input->post('finalDatabase') != NULL)
		{
			if($this->session->userdata('userdetails') != NULL)
			{
				$userdetails = $this->session->userdata('userdetails');
			}
			
			$insert = $this->home_model->insertToDatabase($userdetails);
			
			if($insert)
			{
				$final_stage = '<div class="alert alert-success" role="alert">
										  Finally inserted to database! <br> Removed from browser cache please check database.
										</div>';
				$this->session->unset_userdata('userdetails');
				$data['userdetails'] = '';
			}else{
				$final_stage = '<div class="alert alert-success" role="alert">
										  Somthing error! please try again.
										</div>';
				
			}
			$data['final_stage'] = $final_stage;
		}
		
		$view = 'home';
		module_architecture($view,$data);
	}
	
	public function update($i='')
	{
		$data = array();
		$data['task'] = '<a class="float-right text-white" href="'.base_url('jscode').'" > Click here for Task-2</a>';
		
		if($this->input->post())
		{
			$userdetails = $this->session->userdata('userdetails');
			
			$dirname = $userdetails[$i]['dirname'];
			$filesDir = "assets/uploads/".$dirname;
				
				if(!is_dir($filesDir)){
					mkdir($filesDir, 0755);
				}
				
				$uploadImage = $userdetails[$i]['image'];
				if(!empty($_FILES['image']['name']))
				{
					$_FILES['file']['name']     =  mt_rand().'-'.$_FILES['image']['name'];
					$_FILES['file']['type']     = $_FILES['image']['type'];
					$_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
					$_FILES['file']['error']     = $_FILES['image']['error'];
					$_FILES['file']['size']     = $_FILES['image']['size'];
					
					$config['upload_path'] = $filesDir.'/';
					$config['allowed_types'] = 'jpg|jpeg|png|gif';
					$config['file_name'] = $_FILES['file']['name'];
					
					// Load and initialize upload library
					$this->load->library('upload', $config);
					$this->upload->initialize($config);						
					
					// Upload file to server
					if($this->upload->do_upload('file')){
						// Uploaded file data
						$fileData = $this->upload->data();
						$uploadImage = $filesDir.'/'.$fileData['file_name'];							
					}
				}
				
				$userdetails[$i] = array('name'		=> $this->input->post('name'),
								  'email' 		=> $this->input->post('email'),
								  'mobile'  	=> $this->input->post('mobile'),
								  'role' 		=> $this->input->post('role'),
								  'password' 	=> $this->input->post('password'),
								  'image'		=> $uploadImage,
								  'created_date'=> $this->input->post('created_date'),
								  'dirname'		=> $dirname
								);
			
			$this->session->set_userdata('userdetails', $userdetails);
			redirect(base_url());
		}
		
		$data['id'] = $i;
		if($this->session->userdata('userdetails') != NULL)
		{
			$userdetails = $this->session->userdata('userdetails');
			$data['userdetails'] = $userdetails[$i];
		}
		//echo '<pre>'; print_r($data['userdetails']);
		$view = 'update';
		module_architecture($view,$data);
	}
	
	public function delete($i='')
	{
		$userdetails = $this->session->userdata('userdetails');			
		unset($userdetails[$i]);
		$this->session->set_userdata('userdetails', $userdetails);
		redirect(base_url());
	}
	
	
	
	/**
	* Task 2
	*/
	public function jscode()
	{
		$data = [];
		$data['task'] = '<a class="float-right text-white" href="'.base_url().'"> Click here for Task-1</a>';
		
		$default = [
						0 => [0 => 0, 1 => 0, 2 => 0],

						1 => [0 => 0, 1 => 0, 2 => 0],

						2 => [0 => 0, 1 => 0, 2 => 0]
				   ];
		
		
		
		if($this->input->post())
		{
			$error = '';
			$arr = $this->input->post();
			$newData = [];
			$j = 0;
			foreach($arr as $k=>$kValues){
				foreach($kValues as $m=>$mValues){
					
					$newData[$j][$m] = $mValues;
					if(!is_numeric($mValues) || $mValues < 0)
					{
						$error = 1;
					}
				}
				$j++;
			}
			$default = $newData;
			if($error){
				$this->session->set_flashdata('error', 'Number should not negative value.');
				//$default = $newData;
			}else{
				
				$insert = $this->home_model->insertToSlot(serialize($newData));
			
				if($insert)
				{					
					$this->session->set_flashdata('success', 'Successfully update to database<br> Removed from browser cache please check');
				}else{
					
					$this->session->set_flashdata('error', 'Somthing error! please try again.');
					//$default = $newData;
				}
			}
		}
		
		//print_r($default);
		$data['default'] = $default;
		$view = 'jscode';
		module_architecture($view,$data);
	}
	
	
	
}
