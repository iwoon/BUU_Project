<?php if(!defined('BASEPATH')) exit('No direct access script allowed');
class Frame{
        private $_ci;
        private $_app=NULL;
        private $_user=NULL;
        private $_nav=NULL;
        private $_data;
        private $_frame_permise_obj=NULL;
        private $app_id;
        protected $rules_data=NULL;
	public function __construct(){
		$this->_ci=&get_instance();
		$this->_ci->load->library('session');
                $this->_ci->config->load('frame');
		$this->app_id=$this->_ci->config->item('app_id');
                $this->_app=new App();
                $this->_app->set_App_id($this->app_id);
                $this->_app->initialize();
	}
	/* PARAM
	 * @array {'rule'=>'child app rule'} ex.{'rule'=>array{'ADMIN'=>array{'SUB-ADMIN-1'=>array{'SUB}}} <-- not implement
	 * UM/ADMIN <-- fine 555
	 * @var
	 */
        public function initialize() // not developers api clone and delete this methode
        {
            $this->_user=$this->users();
            $this->_app=$this->app();
            $this->_nav=$this->nav(); 
            $this->_session=new Usersession($this->_user);
            if(is_null($this->_frame_permise_obj)) $this->_frame_permise_obj=new Permissions();
            log_message('debug','Frame Application has initialized');
        }
        public function logout()
        {
            if($this->users()->logout()){
                unset($this->_app);
                unset($this->_user);
                unset($this->_nav);
                return true;
            }
            return false;
        }
        public function users(){return (($this->_user!=NULL)?$this->_user:new Users());}
        public function app(){return (($this->_app!=NULL)?$this->_app:new App());}
        public function nav(){return (($this->_nav!=NULL)?$this->_nav:new Navigation());}
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
            //if(array_key_exists($properties,$this->_data))
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
                    else{$this->_app=new App();}
                    break;
                case 'nav':
                    if($this->_nav!=NULL){$this->app()->$properties=$value;}
                    else{$this->_nav=new Navigation();}
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
                case 'nav':
                    return $this->nav();
                    break;
                default:
                    $this->_data[$properties]=$value;
            }
        }
        
}
class Session{ // not api
    protected $_ci;
    public function __construct()
    {
        $this->_ci=&get_instance();
        $this->_ci->load->library('session');
        $this->_ci->load->database();
    }
    
}
class Rolesession extends Session{
 protected static $TABLE_NAME='rbac_session_role';
 protected $role_node=array();
    public function __construct($role_node)
    {
        parent::__construct();
        $this->role_node=$role_node;
        $this->init();
    }
    public function init()
    {
     $this->_active_session();
     log_message('debug',"Load session Session complete.");
     
    }
    private function _active_session()
    {
        $data=array();
        foreach($this->role_node as $role_id){
            $data[]=array('session_id'=>$this->_ci->session->userdata('session_id'),'role_id'=>$role_id);
        }
        //$this->_ci->db->set($data)->insert(self::$TABLE_NAME);
        $this->_ci->db->insert_batch(self::$TABLE_NAME, $data); 
        log_message('debug',"Load user's active session");
    }
    public function revoke_session() //database constriant implemented
    {
        $this->_ci->db->where('session_id',$this->_ci->session->userdata('session_id'))->delete(self::$TABLE_NAME);
    }
}
class Usersession extends Session{ // not api
    protected static $TABLE_NAME='rbac_sessions';
    private $_userobj;
    public function __construct(&$userobj)
    {
        parent::__construct();
        $this->_userobj=&$userobj;
        $this->init();
    }
    public function init()
    {
     $this->_active_session();
     log_message('debug',"Load user's session complete.");
     
    }
    private function _active_session()
    {
        $data=array('session_id'=>$this->_ci->session->userdata('session_id'),'user_id'=>$this->_userobj->get_user_id());
        $this->_ci->db->set($data)->insert(self::$TABLE_NAME);
        log_message('debug',"Load user's active session");
    }
    public function revoke_session() //database constriant implemented
    {
        $this->_ci->db->where('session_id',$this->_ci->session->userdata('session_id'))->delete(self::$TABLE_NAME);
    }
}
Abstract class Permissions
{
    public $permission=NULL;
    public $object=NULL;
    public $operation=NULL;
}
class Permissionsx{
    protected $_ci;
    private $_permise_data;
    public function __construct()
    {
        $this->_ci->load->library('session');
    }
    public function hasPermise($permission=null)
    {
        return $this->multi_array_key_exists($permission,$this->_permise_data);
    }
    public function set_app_rule($data){$this->_permise_data=$data;}
        /** 
     * multi_array_key_exists function. 
     * 
     * @param mixed $needle The key you want to check for 
     * @param mixed $haystack The array you want to search 
     * @return bool 
     */ 
    protected function multi_array_key_exists( $needle, $haystack ) { 
        foreach ( $haystack as $key => $value ){  
            if ( $needle == $key ) return $key;
            if ( is_array( $value ) ){  
                 if (multi_array_key_exists( $needle, $value )) return $key;
            }
        }
        return false; 
    } 
}
class Users{
    private $_ci;
   // private $_data=array();
    private $_parent_sess='FRAME';
    protected static $user_sess='USERS';
    private $_user_sess_data;
    protected $permise_obj=NULL;
    public function __construct()
    {
        $this->_ci=&get_instance();
        $this->_ci->load->library('session');
        $this->_getSess();
    }
    public function hasPermise($permise)
    {
        if(is_null($this->permise_obj))$this->permise_obj=new Permissions();
        $this->permise_obj->hasPermise($permise);
    }
    /***
     * @return  int user's id 
     */
    public function get_user_id(){
        $this->_getSess();
        return $this->_user_sess_data['user_id'];
    }
    /***
     * @return  boolean user's login status
     */
    public function is_authen()
    {
        //var_dump($this->_user_sess_data);
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
    private function _getSess(){ 
        $usr=$this->_ci->session->userdata($this->_parent_sess);
        if(!empty($usr[self::$user_sess])){
            foreach($usr[self::$user_sess] as $key=>$value){
                if(empty($this->_user_sess_data))
                {
                    $this->_user_sess_data[$key]=$value;
                }elseif(!array_key_exists($key,$this->_user_sess_data))
                {$this->_user_sess_data[$key]=$value;}
            }
        }
    }
    public function save(){
        //$this->_ci->session->unset_userdata(self::$user_sess);
        $this->_getSess();
        $usr=$this->_ci->session->userdata($this->_parent_sess);
        $usr[self::$user_sess]=$this->_user_sess_data;
        $this->_ci->session->set_userdata($this->_parent_sess,$usr);
        //unset($this->_data);
        log_message('debug',"User's session data was updated.");
        }
    public function __get($properties)
    {
        //$this->_getSess();
        //var_dump($this->_user_sess_data);
        //$usr=$this->_ci->session->userdata($this->_parent_sess);
        //return $usr[$properties];
        return ((array_key_exists($properties,$this->_user_sess_data))?$this->_user_sess_data[$properties]:NULL);
    }
    public function  __set($properties,$value)
    {
        if(!empty($this->_user_sess_data)&&array_key_exists($properties,$this->_user_sess_data)){
            $app=$this->_ci->session->userdata($this->_parent_sess);
            log_message('debug','user attemp to change read-only properties [$properties]'.(!isset($app['APP'][1]['app_id']))?$app['APP'][1]['app_id']:'');
            show_error('user attemp to change read-only user properties');
        }
        $this->_user_sess_data[$properties]=$value;
    }
    
}
class App{ //not api
    private $_ci;
    private $_data=array();
    private $_app_sess_data;
    private $_parent_sess='FRAME';
    protected static $app_sess='APP';
    protected $app_id=NULL;
    protected $is_loaded=false;
    protected $rule_node=array();
    protected $rulesess_obj=NULL;
    public function __construct(){
        $this->_ci=&get_instance();
        $this->_ci->load->database();
        //$this->app_id=$app_id;
        //$this->initialize();
    }
    public function set_App_id($id)
    {
        if($this->app_id==$id){$this->is_loaded=true;}
        else{$this->app_id=$id;$this->is_loaded=false;}
    }
    public function initialize()
    {
        if(!$this->is_loaded){
            $this->_getSess();
            $this->_getRuleTree();
            $this->rulesess_obj=new Rolesession($this->app_id);
            $this->rulesess_obj->
            $this->LoadPermissionIntoSession();
        }
    }
    public function __set($properties,$value){$this->_data[$properties]=$value;}
    private function _getApp_rule_id()
    {
        $query=$this->_ci->db->select('app_rule_id')->from('app_installed')->where(array('app_id'=>$this->app_id))->get();
        if($query->num_rows()>0)return $query->row()->app_rule_id;
        return -1;
    }
    private function _getRuleTree()
    {
        $app_rule_id=$this->_getApp_rule_id();
        if($app_rule_id>-1){
            $query=$this->_ci->db->query("CALL `getRoleTrees`('$app_rule_id', 'parent', '0', '1');");
            foreach($query->result() as $row)
            {
                $this->rule_node[]=$row->role_id;
            }
        }
        log_message('debug','Gethering Application rule id complete');
    }
    private function _getPermissions()
    {
        return $this->_ci->db->select('p.name as permission,obj.name as object,o._create as create,o._read as read,o._update as update,o._delete as delete')
                ->from('rbac_roles r')->join('rbac_role_permission rp','rp.role_id=r.role_id')
                ->join('rbac_permissions p','rp.permission_id=p.permission_id')
                ->join('rbac_objects ob','ob.object_id=p.object_id')
                ->join('rbac_operations o','o.operation_id=p.operation_id')
                ->where_in('r.role_id',$this->rule_node)->get()->result();
    }
    public function LoadPermissionIntoSession()
    {
        $buffer=array();
        $data=$this->_getPermissions();
        //$operation=array('create','read','update','delete');
        $pa=$this->_ci->session->userdata($this->_parent_sess);
        $i=0;
        if(!empty($pa[self::$app_sess])){
            foreach($pa[self::$app_sess] as $item){
                if($i<1){$this->_data[]=$item;}
                else{break;}
                $i++;
            }
        }
        foreach($data as $p){
            $op=array('create'=>$p->create,'read'=>$p->read,'update'=>$p->update,'delete'=>$p->delete);
            if(!array_key_exists($p,$buffer))
            {
                $buffer[$p->permission]=array();
            }
            if(!array_key_exists($p->object,$buffer[$permission]))
            {
                $buffer[$p->permission][$p->object]=array();
            }
            $buffer[$p->permission][$p->object][$o]=$op;
           /* foreach($operation as $o){
                $buffer[$p->permission][$p->object][$o]=$p->{$o};
                
            }*/
        }
        //$nav=array(self::$_nav_sess=>$this->_data);
        $this->_data[]=$buffer;
        $pa[self::$app_sess]=$tthis->_data;
        $this->_ci->session->set_userdata($this->_parent_sess,$pa);
        log_message('debug','Loaded Permission into sessoin of application '.$this->_ci->config->item('app_name').'ID :[ '.$this->_ci->config->item('app_id').' ]');
    }
    public function reset(){
        unset($this->_data);
        $pa=$this->_ci->session->userdata($this->_parent_sess);
        unset($pa[self::$app_sess]);
        $this->_ci->session->set_userdata($this->_parent_sess,$pa);
    }
    private function _getSess(){
          $pa=$this->_ci->session->userdata($this->_parent_sess);
        if(!empty($pa[self::$app_sess])){foreach($pa[self::$app_sess] as $item){$this->_data[]=$item;}}
       /* if(!empty($app[self::$app_sess])){
            foreach($app[self::$app_sess] as $key=>$value){
                if(empty($this->_app_sess_data))
                {
                    $this->_app_sess_data[$key]=$value;
                }elseif(!array_key_exists($key,$this->_app_sess_data))
                {$this->_app_sess_data[$key]=$value;}
            }
        }*/
    }
    
}
class Navigation{
    private $_ci;
    private $_data=array();
    private $_parent_sess='FRAME';
    protected static $_nav_sess='NAV';
    public function __construct()
    {
        $this->_ci=&get_instance();
        $this->_data=array();
    }
    public function add($page,$link)
    {
        $pa=$this->_ci->session->userdata($this->_parent_sess);
        if(!empty($pa[self::$_nav_sess])){foreach($pa[self::$_nav_sess] as $item){$this->_data[]=$item;}}
        $this->_data[]=array('page'=>$page,'link'=>$link);
        //$nav=array(self::$_nav_sess=>$this->_data);
        $pa[self::$_nav_sess]=$this->_data;
        $this->_ci->session->set_userdata($this->_parent_sess,$pa);
    }
    public function get()
    {
        $nav=$this->_ci->session->userdata($this->_parent_sess);
        return $nav[self::$_nav_sess];
    }
    public function reset(){
        unset($this->_data);
        $pa=$this->_ci->session->userdata($this->_parent_sess);
        unset($pa[self::$_nav_sess]);
        $this->_ci->session->set_userdata($this->_parent_sess,$pa);
    }
    
}
?>