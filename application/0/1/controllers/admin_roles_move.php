<?php
class Admin_roles_move extends CI_Controller {
        protected $page='ย้ายบทบาท';        
	function __construct()
	{
 		parent::__construct();
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('Admin_roles_move_m');
	}	
	function index()
	{	
                $this->frame->nav->reset();
                $this->frame->nav->add(array('page'=>'จัดการบทบาท','link'=>site_url('admin_manages_roles')));
                $this->frame->nav->add(array('page'=>$this->page));
                
		$this->form_validation->set_rules('from_role', 'บทบาท', 'required|xss_clean|is_numeric');			
		$this->form_validation->set_rules('to_roles', 'ย้ายไปยัง', 'required|xss_clean|is_numeric');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->load->view('admin/roles/');
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			
			$form_data = array(
					       	'role_id' => set_value('from_role'),
					       	'parent_role_id' => set_value('to_roles')
						);
					
			// run insert model to write data to db
		
			if ($this->Admin_roles_move_m->SaveForm($form_data) == TRUE) // the information has therefore been successfully saved in the db
			{
				redirect('Admin_roles_move/success');   // or whatever logic needs to occur
			}
			else
			{
			$str='เกิดความผิดพลาดระหว่างเพิ่มข้อมูลกรุณาลองใหม่อีกครั้ง';
                        $this->template->content->add($str);
                        $this->template->publish();
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
