<?php
class Admin_add_users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $this->template->content->view('admin/users/admin_users_add');
        $this->template->publish();
    }
}
?>
