<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');
class Logout extends CI_Controller{
    function __construct(){
        parent::__construct();
        }
    function index(){
        //$this->load->library('session');
        if($this->frame->users()->logout()){
            redirect(site_url());
        }
        
    }
}
?>
