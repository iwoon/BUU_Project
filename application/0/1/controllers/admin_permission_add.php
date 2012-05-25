<?php
class Admin_permission_add extends CI_Controller {
        protected $page='เพิ่มสิทธิ';       
	function __construct()
	{
 		parent::__construct();
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('Admin_permission_add');
	}	
	function index()
	{	
                $this->frame->nav->reset();
                $this->frame->nav->add(array('page'=>'จัดการสิทธิ','link'=>site_url('admin_manages_permissions')));
                $this->frame->nav->add(array('page'=>$this->page));
                
		$this->form_validation->set_rules('permission_name', 'ชื่อสิทธิ', 'required|trim|xss_clean|max_length[100]');			
		$this->form_validation->set_rules('alias_permision_name', 'ชื่อแทน', 'required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('role_id', 'ภายใต้', 'required|xss_clean|is_numeric');			
		$this->form_validation->set_rules('object_name', 'วัตถุ', 'required|trim|xss_clean');			
		$this->form_validation->set_rules('read', 'อ่าน/เข้าถึง', 'xss_clean|is_numeric');			
		$this->form_validation->set_rules('create', 'เขียน/เพิ่ม', 'xss_clean|is_numeric');			
		$this->form_validation->set_rules('edit', 'แก้ไข/ปรับปรุง', 'xss_clean|is_numeric');			
		$this->form_validation->set_rules('delete', 'ลบ', 'xss_clean|is_numeric');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn't been passed
		{
			$this->load->view('admin/permissions/admin_permission_add');
		}
		else // passed validation proceed to post success logic
		{
		 	// build array for the model
			
			$form_data = array(
					       	'name' => set_value('permission_name'),
					       	'alias_name' => set_value('alias_permision_name'),
					       	'role_id' => set_value('role_id'),
					       	'object_name' => set_value('object_name'),
					       	'_read' => set_value('read'),
					       	'_create' => set_value('create'),
					       	'_update' => set_value('edit'),
					       	'_delete' => set_value('delete')
						);
					
			// run insert model to write data to db
		
			if ($this->Admin_permission_add->SaveForm($form_data) == TRUE) // the information has therefore been successfully saved in the db
			{
				redirect('Admin_permission_add/success');   // or whatever logic needs to occur
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
