<?php
class User_edit_profile extends CI_Controller {
        protected $page='แก้ไขข้อมูลส่วนตัว';
	function __construct()
	{
 		parent::__construct();
                if(!$this->frame->users()->is_authen){redirect($this->frame->url);}
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('User_edit_profile_m');
	}	
	function index()
	{	
                $this->frame->nav->reset();
                $this->frame->nav->add(array('page'=>'ข้อมูลส่วนตัว','link'=>site_url('user_profile')));
                $this->frame->nav->add(array('page'=>$this->page));
		$this->form_validation->set_rules('firstname', 'ชื่อ', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('lastname', 'นามสกุล', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('current_password', 'รหัสผ่านเดิม', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('new_password', 'รหัสผ่านใหม่', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('confirm_new_password', 'ยืนยันรหัสผ่าน', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('email', 'อีเมล์', 'required|trim|xss_clean|valid_email|max_length[255]');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->load->view('users/profile');
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			
			$form_data = array(
					       	'firstname' => set_value('firstname'),
					       	'lastname' => set_value('lastname'),
					       	'current_password' => set_value('current_password'),
					       	'new_password' => set_value('new_password'),
					       	'confirm_new_password' => set_value('confirm_new_password'),
					       	'email' => set_value('email')
						);
					
			// run insert model to write data to db
		
			if ($this->Profile_m->SaveForm($form_data) == TRUE) // the information has therefore been successfully saved in the db
			{
				redirect('Profile/success');   // or whatever logic needs to occur
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
