<?php
class Roles_main extends CI_Controller
{
    protected $page='จัดการบทบาท';
    public function __construct()
    {
        parent::__construct();
        $this->load->library('jquery_ext');
        $this->load->model('Rbac_users_model','users');
        $this->frame->nav()->add('หน้าหลัก',$this->frame->url);
        $this->frame->nav()->add('ระบบจัดการผู้ใช้',site_url());
        $this->frame->nav()->add($this->page);
    }
    public function index()
    {
        $this->template->content->add('การจัดการบทบาท');
        $this->template->publish();
    }
    public function page($page_id)
    {
        $rowperpage=20;
        $begin = ($page_id==1)?0:$page_id*$rowperpage;
        $condition=array(
            'limit'=>array('rowperpage'=>$rowperpage,'begin'=>$begin)
                );
        $data['users_list']=$this->users->get_users_list($condition);
        $data['num_users']=$this->users->get_num_users();
        $data['row_per_page']=$rowperpage;
        $user_panel=$this->load->view('users/user_list',$data,true);
        $left_menu=$this->load->view('left_menu','',true);
        $this->template->content->add($left_menu);
        $this->template->content->add('<div id="admin_panel" style="float:left;">');
        $this->template->content->widget('Users_menu');
        $this->template->content->add($user_panel);
        $this->template->content->add('</div>');
        $this->template->publish();
    }
    public function search()
    {
        
    }
    public function display()
    {
        
    }
    
}
?>
