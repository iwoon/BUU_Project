<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Welcome extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if(!$this->frame->users()->is_authen())
        {
            var_dump($this->frame->url);
        }
    }
    public function index()
    {
        $this->load->library('jquery_ext');
        $data['session']=$this->frame->users()->get_session_id();
        $this->template->content->view('welcome_message',$data);
        $this->template->publish();
    }
}
?>
