<?php if(!defined('BASEPATH')) exit('No direct access script allowed');
class Frame{
        private $_ci;
        private $_app=NULL;
        private $_user=NULL;
        private $_data;
        private $app_rule_id;
	public function __construct(){
		$this->_ci=&get_instance();
		$this->_ci->load->library('session');
		$this->app_rule_id=$this->_ci->config->item('app_rule_id');
	}
	/* PARAM
	 * @array {'rule'=>'child app rule'} ex.{'rule'=>array{'ADMIN'=>array{'SUB-ADMIN-1'=>array{'SUB}}} <-- not implement
	 * UM/ADMIN <-- fine 555
	 * @var
	 */
        public function sess(){return $this->_ci->session;}
	public function get_app_rule($rule=NULL)
	{
		if(!is_null($rule)){
			if(!is_array($rule)){
				$this->$ret= $this->_ci->session->userdata($this->_SESS_RULE_NAME);
                                return $this->$ret[$this->app_rule][$rule];
			}else{
				$sub_rule=explode('/',$rule);
				$last_sub_rule_index=sizeof($sub_rule)-1;
				if(empty($sub_rule[sizeof($last_sub_rule_index)])){unset($sub_rule[$last_sub_rule_index]);}
				$ret_sess=$this->_ci->session->userdata($this->_SESS_RULE_NAME);
				foreach($sub_rule as $value){$ret_sess=$ret_sess[$value];}
				return $ret_sess[$this->app_rule];
			}
		}else{
                   $this->$ret=$this->_ci->session->userdata($this->SESS_RULE_NAME);
                    return $this->$ret[$this->app_rule];
                    }
	}
        public function exists($properties,$section='data')
        { 
        }
        public function __set($properties,$value)
        {
            switch($properties)
            {
                case 'users':
                        /*if(array_key_exists($properties,$this->_user))
                        {
                            $this->_user[$properties]=$value;
                        }*/
                    if($this->_user!=NULL){$this->users()->$properties=$value;}
                    else{$this->_user=new Users();}
                    break;
                case 'app':
                    if($this->_app!=NULL){$this->app()->$properties=$value;}
                    break;
                default:
                    $this->_data[$properties]=$value;
            }
        }
        public function __get($properties)
        {
            switch($properties)
            {
                case 'users':
                        /*if(array_key_exists($properties,$this->_user))
                        {
                            return $this->_user[$properties];
                        }else{
                            return $this->users();
                        }*/
                    return $this->users();
                    break;
                case 'app':
                    return $this->app();
                    break;
                default:
                    $this->_data[$properties]=$value;
            }
        }
        public function users(){return (($this->_user!=NULL)?$this->_user:new Users());}
        public function app(){return (($this->_app!=NULL)?$this->_app:new App());}
}
class Users{
    private $_ci;
   // private $_data=array();
    protected static $user_sess='USERS';
    private $_user_sess_data;
    public function __construct()
    {
        $this->_ci=&get_instance();
        $this->_ci->load->library('session');
        $this->_user_sess_data=$this->_ci->session->userdata(self::$user_sess);
    }
    public function  __set($properties,$value)
    {
        $this->_user_sess_data[$properties]=$value;
        
    }
    public function save(){
        $this->_ci->session->unset_userdata(self::$user_sess);
        $this->_ci->session->set_userdata(self::$user_sess,$this->_user_sess_data);
        //unset($this->_data);
        log_message('debug',"User's session data was updated.");
        }
    public function __get($properties)
    {
        return ((array_key_exists($properties,$this->_user_sess_data))?$this->_user_sess_data[$properties]:NULL);
    }
    /***
     * @return  int user's id 
     */
    public function get_user_id(){return ((array_key_exists('user_id',$this->user_sess_data))?$this->_user_session_data['user_id']:-1);}
    /***
     * @return  boolean user's login status
     */
    public function is_authen()
    {
        return (!empty($this->_user_sess_data)&&($this->_user_sess_data['is_logedin']))?true:false;
    }
    /***
     * @return boolean unset and destroyed user's session
     */
    public function logout()
    {
        $this->_ci->session->unset_userdata(self::$user_sess);
        $this->_ci->session->sess_destroy();
        log_message('debug',"User's session was destroyed.");
           //unset($this->_data);
        unset($this->_user_sess_data);
        return ($this->_ci->session->userdata(self::$user_sess)===false)?true:false;
    }
    /**
     *
     * @return mixed[] user's session data
     */
    public function all_user_data(){return $this->_user_sess_data;}
}
class App{
    private $_ci;
    private $_data=array();
    private $_app_sess_data;
    protected static $_app_sess='APP';
    public function __construct(){
        
    }
}
?>