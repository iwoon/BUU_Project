<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Welcome extends CI_Controller
{
    protected $page='ระบบจัดการผู้ใช้';
    public function __construct()
    {
        parent::__construct();
        if(!$this->frame->users()->is_authen())
        {
            //redirect($this->frame->url);
            
            if(!empty($this->frame->url)){redirect($this->frame->url);}
            echo "กรุณาเข้าสู่ระบบก่อนที่ ".anchor('http://'.$_SERVER['SERVER_NAME'].'/frame/');
        }
    }
    public function index()
    {
        if(!$this->frame->users()->is_authen())redirect('http://'.$_SERVER['SERVER_NAME'].'/frame/');
        $this->frame->nav()->reset();
        $this->frame->nav()->add('หน้าหลัก',$this->frame->url);
        $this->frame->nav()->add($this->page);
        // create icon 
        $app_list=array();
        if($this->frame->users()->hasPermission('visible_admin_panel')->object('admin_panel')->read())
        {
            if($this->frame->users()->hasPermission('visible_admin_icon')->object('users_icon')->read()){
                $app_list[]=array(
                        'app_icon'=>image_path('icons/64/54.png'),
                        'app_name'=>'จัดการผู้ใช้',
                        'app_url'=>site_url('users/')
                );
            }
            if($this->frame->users()->hasPermission('visible_admin_icon')->object('roles_icon')->read())
            {
                $app_list[]=array(
                        'app_icon'=>image_path('icons/roles.png'),
                        'app_name'=>'จัดการบทบาท',
                        'app_url'=>site_url('roles/')
                    );
            }
            if($this->frame->users()->hasPermission('visible_admin_icon')->object('permissions_icon')->read())
            {
                $app_list[]=array(
                        'app_icon'=>image_path('icons/permissions.png'),
                        'app_name'=>'จัดการสิทธิ',
                        'app_url'=>site_url('permissions/')
                );
            }
            if($this->frame->users()->hasPermission('visible_admin_icon')->object('programs_icon')->read())
            {
                $app_list[]=array(
                        'app_icon'=>image_path('icons/install.jpg'),
                        'app_name'=>'จัดการโปรแกรมเสริม',
                        'app_url'=>site_url('programs/')
                );
            }
            if($this->frame->users()->hasPermission('visible_admin_icon')->object('add_users_icon')->read())
            {
                $app_list[]=array(
                        'app_icon'=>image_path('icons/add_users.png'),
                        'app_name'=>'เพิ่มผู้ใช้',
                        'app_url'=>site_url('users/users_main/add')
                );
            }
            if($this->frame->users()->hasPermission('visible_admin_icon')->object('add_roles_icon')->read())
            {
                $app_list[]=array(
                        'app_icon'=>image_path('icons/no-image.gif'),
                        'app_name'=>'เพิ่มบทบาท',
                        'app_url'=>site_url('roles/roles_main/add')
                );
            }
            if($this->frame->users()->hasPermission('visible_admin_icon')->object('add_permissions_icon')->read())
            {
                $app_list[]=array(
                        'app_icon'=>image_path('icons/no-image.gif'),
                        'app_name'=>'เพิ่มสิทธิ',
                        'app_url'=>site_url('permissions/permissions_main/add')
                );
            }
            if($this->frame->users()->hasPermission('visible_admin_icon')->object('add_programs_icon')->read())
            {
                $app_list[]=array(
                        'app_icon'=>image_path('icons/install-icon.jpg'),
                        'app_name'=>'ติดตั้งโปรแกรม',
                        'app_url'=>site_url('programs/programs_main/install')
                );
            }
        
        //$data['session']=$this->frame->users()->get_session_id();
        //$data['messages']=$this->load->view('welcome_message',$data['messages'],true);
        
        }
        $data['admin_icon_panel']=$this->load->view('admin_icon_panel',array('app_list'=>$app_list),true);
        $this->load->library('jquery_ext');
        $this->jquery_ext->add_css(css_path('frame.css'));
        $this->template->content->view('main',$data);
        $this->template->publish();
    }
}
?>
