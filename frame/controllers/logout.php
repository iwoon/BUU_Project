<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');
class Logout extends CI_Controller{
    function __construct(){
        parent::__construct();
        }
    function index(){
        //$this->load->library('session');
        //$this->load->library('rbac_session');
        if($this->session->Is_logout()){
            $sess=new Rbac_session();
            $sess->delete();
            $this->load->view('logout',array('time'=>3,'site'=>base_url()));
            //redirect('login/',100);
        }
    }
}
?>
