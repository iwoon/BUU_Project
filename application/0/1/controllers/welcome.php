<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Welcome extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if(!$this->frame->users()->is_authen())
        {
            redirect($this->frame->url);
        }
    }
    public function index()
    {
        
        // create icon 
        $app_list=array();
        $app_list[]=array(
                'app_icon'=>'',
                'app_name'=>'จัดการผู้ใช้',
                'app_url'=>site_url('admin_manages_users')
        );
        $app_list[]=array(
                'app_icon'=>'',
                'app_name'=>'จัดการบทบาท',
                'app_url'=>site_url('admin_manages_roles')
            );
        
        $app_list[]=array(
                'app_icon'=>'',
                'app_name'=>'จัดการสิทธิ',
                'app_url'=>site_url('admin_manages_permissions')
        );
        $app_list[]=array(
                'app_icon'=>'',
                'app_name'=>'จัดการโปรแกรมเสริม',
                'app_url'=>site_url('admin_manages_programs')
        );
        $app_list[]=array(
                'app_icon'=>'',
                'app_name'=>'เพิ่มผู้ใช้',
                'app_url'=>site_url('admin_add_users')
        );
        $this->load->library('jquery_ext');
        //$data['session']=$this->frame->users()->get_session_id();
        //$data['messages']=$this->load->view('welcome_message',$data['messages'],true);
        $data['admin_icon_panel']=$this->load->view('admin_icon_panel',array('app_list'=>$app_list),true);
        
        $this->jquery_ext->add_css(css_path('frame.css'));
        $this->template->title->set('USERS MANAGEMENT');
        $this->template->content->view('main',$data);
        $this->template->publish();
    }
}
?>
