<?php
class Admin_manages_users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('jquery_ext');
        $this->load->model('Rbac_users_model','users');
    }
    public function index()
    {
        
        $data['users_list']=$this->users->get_users_list();
        $user_panel=$this->load->view('users/user_list',$data,true);
        $left_menu=$this->load->view('left_menu','',true);
        $this->template->content->add($left_menu);
        $this->template->content->add($user_panel);
        $this->template->publish();
    }
}
?>
