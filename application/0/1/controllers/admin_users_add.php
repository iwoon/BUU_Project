<?php
class Admin_users_add extends CI_Controller {
        protected $page='เพิ่มผู้ใช้';      
	function __construct()
	{
 		parent::__construct();
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('Admin_users_add_m');
	}	
	function index()
	{	
                $this->frame->nav->reset();
                $this->frame->nav->add(array('page'=>'จัดการผู้ใช้','link'=>site_url('admin_manage_users')));
                $this->frame->nav->add(array('page'=>$this->page));
                
		$this->form_validation->set_rules('firstname', 'ชื่อ', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('lastname', 'นามสกุล', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('username', 'ชื่อผู้ใช้', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('password', 'รหัสผ่าน', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('auto_generate', 'auto generate', 'max_length[255]');			
		$this->form_validation->set_rules('email', 'อีเมล์', 'required|trim|xss_clean|valid_email|max_length[255]');			
		$this->form_validation->set_rules('authen_type', 'ประเภท', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('server', 'เซิฟเวอร์', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('port', 'พอร์ต', 'trim|is_numeric');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->load->view('add_users_view');
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			
			$form_data = array(
					       	'firstname' => set_value('firstname'),
					       	'lastname' => set_value('lastname'),
					       	'username' => set_value('username'),
					       	'password' => set_value('password'),
					       	'auto_generate' => set_value('auto_generate'),
					       	'email' => set_value('email'),
					       	'authen_type' => set_value('authen_type'),
					       	'server' => set_value('server'),
					       	'port' => set_value('port')
						);
					
			// run insert model to write data to db
		
			if ($this->Admin_user_add_m->SaveForm($form_data) == TRUE) // the information has therefore been successfully saved in the db
			{
				redirect('Admin_user_add/success');   // or whatever logic needs to occur
			}
			else
			{
			$str='เกิดความผิดพลาดระหว่างเพิ่มข้อมูลกรุณาลองใหม่อีกครั้ง';
                        $this->template->content->add($str);
                        $this->template->publish();
			// Or whatever error handling is necessary
			}
		}
	}
	function success()
	{
			$this->frame->nav()->add(array('page'=>$this->page));
			$this->template->content->add('บันทึกข้อมูลเรียบร้อยแล้ว');
                        $this->template->publish();
	}
}
?>
