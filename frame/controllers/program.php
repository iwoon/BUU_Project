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
        $this->_gotoApp();
    }
    private function _gotoApp()
    {
        $this->load->model('application');
        $app_id=$this->uri->segment(2,-1);
        $app=$this->application->get_app_values(2);
        $app_path=(string)$app->app_path;
        $pattern=array('htt','www');
        
        if(!empty($app_path)){
            if(ereg($pattern,substr($app_path,3))){ //localpath
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
