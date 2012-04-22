<?php if(!defined('BASEPATH')) exit("No direct access script allowed");
class Login_Model extends CI_Model{
		public function __construct(){
			parent::__construct();
		}
                  
		public function CheckAuth($arg=NULL){
                   
                        return $this->db->get_where('rbac_users',$arg)->row();
                        
		}
}
?>