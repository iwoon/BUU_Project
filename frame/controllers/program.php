<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Program extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        if(!$this->frame->users->is_authen())redirect('login');
        
    }
    public function index($program_id)
    {
        $this->frame->app()->set_app_id($program_id);
        $this->frame->app()->initialize();
        //$this->frame->app()->get();exit;
        $this->_gotoApp();
    }
    private function _gotoApp()
    {
        $this->load->model('application');
        $app_id=$this->uri->segment(2,-1);
        $app=$this->application->get_app_values(2);
        $app_path=(string)$app->app_path;
        
        $domain=(string)$_SERVER['SERVER_NAME'];
        if(!empty($app_path)){
            if(stristr($app_path,$domain)){ //localpath
                redirect($app_path);
            }
            //other subdomain
            $this->load->config('frame');
            $app_location=$this->config->item('app_location');
            redirect(base_url($app_location.$app_path));
        }else{ 
            $this->template->content->add('โปรแกรมที่คุณเรียกไม่สามารถโหลดได้');
            $this->template->publish();
            }
        
    }
    
}
?>
