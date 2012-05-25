<?php
class Admin_roles_add extends CI_Controller {
        protected $page='เพิ่มบทบาท';     
	function __construct()
	{
 		parent::__construct();
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('Admin_roles_add_m');
	}	
	function index()
	{	 
                $this->frame->nav->reset();
                $this->frame->nav->add(array('page'=>'จัดการบทบาท','link'=>site_url('admin_manage_roles')));
                $this->frame->nav->add(array('page'=>$this->page));		
		$this->form_validation->set_rules('role_name', 'ชื่อบทบาท', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('alias_name', 'ชื่อแทน', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('parent_roles_id', 'ภายใต้', 'required|trim|xss_clean|is_numeric');			
		$this->form_validation->set_rules('description', 'คำอธิบาย', 'trim|xss_clean');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->load->view('add_roles_view');
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			
			$form_data = array(
					       	'role_name' => set_value('role_name'),
					       	'alias_name' => set_value('alias_name'),
					       	'parent_roles_id' => set_value('parent_roles_id'),
					       	'description' => set_value('description')
						);
					
			// run insert model to write data to db
		
			if ($this->Admin_roles_add_m->SaveForm($form_data) == TRUE) // the information has therefore been successfully saved in the db
			{
				redirect('Admin_roles_add/success');   // or whatever logic needs to occur
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
