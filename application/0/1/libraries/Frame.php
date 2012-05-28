<?php if(!defined('BASEPATH')) exit('No direct access script allowed');
class Frame{
        private $_ci;
        private $_user=NULL;
        private $_nav=NULL; 
	public function __construct(){
		$this->_ci=&get_instance();
		$this->_ci->load->library('session');
                $this->_user=new Usersession();
                $this->_nav=new Navigation();
	}
        public function logout()
        {
            if($this->users()->logout()){
                unset($this->_user);
                unset($this->_nav);
                log_message('debug','User is loged out and destroyed frame instance');
                return true;
            }
            unset($this);
            log_message('debug','destroy frame instance');
            return true;
        }
        public function users(){
            $this->_user=(($this->_user!=NULL)?$this->_user:new Usersession());
            return $this->_user;
            }
        public function nav(){
            $this->_nav=(($this->_nav!=NULL)?$this->_nav:new Navigation());
            return $this->_nav;
            }
        public function sess(){return $this->_ci->session;}
        public function __set($properties,$value)
        {
            //if(array_key_exists($properties,$this->_data))
            switch($properties)
            {
                case 'users':
                        /*if(array_key_exists($properties,$this->_user))
                        {
                            $this->_user[$properties]=$value;
                        }*/
                    if($this->_user!=NULL){$this->users()->$properties=$value;}
                    else{$this->_user=new Usersession();}
                    break;
                case 'nav':
                    if($this->_nav!=NULL){$this->nav()->$properties=$value;}
                    else{$this->_nav=new Navigation();}
                    break;
                default: 
                    //$this->_data[$properties]=$value;
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
                case 'nav':
                    return $this->nav();
                    break;
                case 'url':
                        $data=$this->sess()->userdata('FRAME');
                        $url=$data['URL'];
                        unset($data);
                    return $url;
                    break;
                default:
                    //$this->_data[$properties];
            }
        }
        
}
class Session{ 
    protected $_ci;
    protected $_data=array();
    protected static $namespace='FRAME';
    public function __construct()
    {
        $this->_ci=&get_instance();
        $this->_ci->load->library('session');
        $this->_ci->load->database();
        $this->_data=$this->_ci->session->userdata(self::$namespace);
        //print_r($this->_data);
    }
    public function RemoveSession()
    {
        $this->_data=array();
        $this->_ci->session->sess_destroy();
    }
    public function get_session_id()
    {
        return $this->_ci->session->userdata('session_id');
    }
    /*
     * how to use this method
     * fetch(array('FLAM|APP'))
     * fetch();
     * @param array 
     */
    
    public function fetch($section=array())
    {
        if(!empty($section))
        {
            if(is_array($section)){$section=array($section);}
            switch(count($section))
            {
                case 0: $this->fetchAll();
                    break;
                case 1: $this->fetchSection($section);
                    break;
                case 'etc':
                        //do any thing
                    break;
                default:
                    show_error('Invalid parameter for fetch session');
            }
        }
    }
    public function exists($section){return (!empty($this->_data)&&array_key_exists($section,$this->_data))?true:false;}
    private function fetchSection($section)
    {
        if(empty($this->_data))return false;
        if(count($section)==1)
        {
            return $this->_data[$section];
            //return (($this->exists($section))?$this->_data[$section]:false);
        }else{
            $arr_sec=explode($section,'|');
            if(count($arr_sec)<1) return false;//return 'Not found data on session section '.$section;
                $buffer=$this->_data;
                foreach($arr_sec as $sec)
                {
                    $buffer=$buffer[$sec];
                }
            return $buffer;
        }
    }
    private function fetchAll()
    {
        return $this->_data;
    }
    public function save()
    {
        $this->_ci->session->set_userdata(self::$namespace,$this->_data);
    }
    public function update($section,$value)
    {
       if($this->multi_array_key_exists($section,$this->_data))
       {
           $this->update_multi_array_value($this->_data, $section, $value);
       }
       return 'Not found section';
    }
    protected function multi_array_key_exists( $needle, $haystack ) { 
        foreach ( $haystack as $key => $value ){  
            if ( $needle == $key ) return $key;
            if ( is_array( $value ) ){  
                 if (multi_array_key_exists( $needle, $value )) return $key;
            }
        }
        return false; 
    } 
    protected function update_multi_array_value($haystack,$needed,$newvalue)
    {
        foreach($haystack as $key=>$value)
        {
            if($key==$needed) $haystack[$key]=$newvalue;return $haystack;
            if(is_array($value)){
                update_multi_array_value($value,$needed,$newvalue);
            }
        }
        return $haystack;
    }
    protected function reload()
    {
        $this->_data=$this->_ci->session->userdata(self::$namespace);
    }
    public function __destruct()
    {
        unset($this);
    }
}
class Obj
{
    private $data=array();
    private $p=null;
    public function __construct($obj)
    {
            $this->data=$obj;  
    }
    public function object($name)
    {
        if(empty($this->data)||!array_key_exists($name,$this->data))
        {
            $o=new stdClass();
            $this->data=array($name=>$o);
            unset($o);
        }
        $this->p=new ObjOperations($this->data[$name]);
        return $this->p;
    }
    public function __destruct()
    {
        unset($this);
    }
}
class ObjOperations
{
    private $data;
    public function __construct($obj)
    {
        $this->data=$obj;
    }
    public function operation($operation)
    {
        //print_r($this->data);
        if(array_key_exists($operation,$this->data))
        {
            return $this->data->{$operation};
        }return false;
    }
    public function __call($operation,$param=null)
    {
        unset($param);
        $operation=strtolower($operation);
        switch($operation)
        {
            case 'create';
            case 'read';
            case 'update';
            case 'delete': return $this->operation($operation);
                break;
            default:return false;
        }
    }
    public function __destruct(){unset($this->data);}
}
class PermissionOnSession extends Session{ //reader
    private $data=array();
    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }
    private function _init()
    {
        parent::reload();
        //$this->data=$this->_data['APP'];
        if(!empty($this->_data['APP'])){
            $this->data=$this->_data['APP'];
            log_message('debug','Fetch App Permission on Session complete');
        }
    }
    public function permission($name)
    {
        if(empty($this->data)){
            $this->_init();
            }
        if(!empty($this->data) && array_key_exists($name,$this->data)){
            return new Obj($this->data[$name]);
        }
        return new Obj(array());
    }
    public function __destruct()
    {
        unset($this);
    }
}
class Usersession extends Session
{
    protected $data=array();
    protected static $namespace='USERS';
    private $p=NULL;
    public function __construct()
    {
        parent::__construct();
        $this->_init();
        $this->p=new PermissionOnSession();
        log_message('debug','Initialize User is session');
    }
    public function hasPermission($permission)
    {
        if(is_null($this->p)){$this->p=new PermissionOnSession();}
        return  $this->p->permission($permission);
    }
    public function checkaccess($permise,$object)
    {
        return $this->hasPermission($permise)->object($object);
    }
    private function _init()
    {
        parent::reload();
        if(!empty($this->_data) && array_key_exists(self::$namespace,$this->_data)){
            $this->data=$this->_data[self::$namespace];
        }else{
            $this->_data[self::$namespace]=array();
        }
        
    }
    public function get()
    {
        return $this->data;
    }
    public function reload()
    {
        parent::reload();
        $this->data=$this->_data[self::$namespace];
    }
    public function get_user_id()
    {
        return $this->data['user_id'];
    }
    public function is_authen()
    {
        return (!empty($this->data))?$this->data['is_logedin']:false;
    }
    public function logout()
    {
        $this->data=array();
        parent::RemoveSession();
        log_message('debug','User is loged out from system');
        return (empty($this->data))?true:false;
    }
    public function save()
    {
        $this->_data[self::$namespace]=$this->data;
        parent::save();
        log_message('debug',"User's session data was updated.");
    }
    public function __set($properties,$value)
    {
        if(!empty($this->data)&&array_key_exists($properties,$this->data)){
            log_message('debug','user attemp to change read-only properties ['.$properties.']');
            show_error('user attemp to change read-only user properties');
        }else{
            $this->data[$properties]=$value;
        }
    }
    public function __get($properties)
    {
        if(empty($this->data))return null;
        return $this->data[$properties];
    }
    public function __destruct(){unset($this);}
}
class Navigation extends Session
{
    protected $data=array();
    protected static $namespace='NAV';
    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }
    private function _init()
    {
        parent::reload();
        if(!empty($this->_data)&&array_key_exists(self::$namespace,$this->_data)){
            $this->data=$this->_data[self::$namespace];
        }
    }
    public function add($page,$controller='')
    {
            if(!empty($page)){
                $this->data[]=array(
                                'page'=>$page,
                                'link'=>$controller
                              );
            }
    }
    public function save()
    {
        //$this->_data[self::$namespace]=$this->data;
        //parent::save();
    }
    public function reset(){$this->data=array();}
    public function get()
    {
        if (!empty($this->data)){
            return $this->data;
        }return array();
    }
    public function __destruct(){unset($this);}
}
?>