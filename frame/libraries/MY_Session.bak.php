<?php if(!defined('BASEPATH')) exit('No direct access script allowed');

class My_Session extends CI_Session{
	protected static $_SESS_RULE_NAME='RULE';
	protected static $_SESS_USER_NAME='USER';
	protected $app_rule;
        private $ret;
	public function __construct(){
		parent::__construct();
		$CI=&get_instance();
		$CI->load->library('session');
		$this->app_rule=$CI->config->item('application_parent_rule');
	}
	public function set_app_rule($rule) // set parrent rule name 
	{
			$this->app_rule=$rule;
	}
	/* PARAM
	 * @array {'rule'=>'child app rule'} ex.{'rule'=>array{'ADMIN'=>array{'SUB-ADMIN-1'=>array{'SUB}}} <-- not implement
	 * UM/ADMIN <-- fine 555
	 * @var
	 */
	public function get_app_rule($rule=NULL)
	{
		if(!is_null($rule)){
			if(!is_array($rule)){
				$this->$ret= $CI->session->userdata($this->_SESS_RULE_NAME);
                                return $this->$ret[$this->app_rule][$rule];
			}else{
				$sub_rule=explode('/',$rule);
				$last_sub_rule_index=sizeof($sub_rule)-1;
				if(empty($sub_rule[sizeof($last_sub_rule_index)])){unset($sub_rule[$last_sub_rule_index]);}
				$ret_sess=$CI->session->userdata($this->_SESS_RULE_NAME);
				foreach($sub_rule as $value){$ret_sess=$ret_sess[$value];}
				return $ret_sess[$this->app_rule];
			}
		}else{
                   $this->$ret=$CI->session->userdata($this->SESS_RULE_NAME);
                    return $this->$ret[$this->app_rule];
                    }
	}
	public function get_user_id(){
		$ret= $CI->session->userdata($this->SESS_USER_NAME);
                return $this->$ret['user_id'];
	}
	public function get_user_sess(){return $CI->session->userdata($this->_SESS_USER_NAME);}
	public function Is_logged_in(){
		return ((!empty($CI->session->userdata($this->_SESS_USER_NAME)))?true:false);
	}
	public function Is_logout(){
		 $usr_sess=array('USER'=>array('user_id'=>'','is_logged_in'=>''));
		 $CI->session->unset_userdate($usr_sess);
		 $CI->session->sess_destroy();
		 return ((!$this->Is_logged_in())?true:false);
	}
}
?>