<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Program extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        if(!$this->frame->users->is_authen())redirect('login');
    }
    public function index($program_id)
    {
      
    }
    private function _load_app_rule()
    {
        
    }
}
?>
