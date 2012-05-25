<?php if(!defined('BASEPATH')) exit('No direct access script allowed');
class Frame{
        private $_ci;
        private $_app=NULL; //not api
        private $_session=NULL; //not api
        private $_user=NULL;
        private $_nav=NULL; 
        private $app_id;
        private $data=array();
	public function __construct(){
		$this->_ci=&get_instance();
		$this->_ci->load->library('session');
                $this->_ci->config->load('frame'); // not api
                $this->_user=new Usersession();
                $this->_nav=new Navigation();
                $this->_app=new Appsession(); //not api
	}

        public function initialize() // not developers api clone and delete this methode
        {
           // $this->_app=$this->app();
            $this->data['sess']=$this->_ci->session->userdata('FRAME');
                if(!empty($this->data['sess'])){
                    $this->data['sess']['URL']=$this->_ci->config->item('frame_url');
                }else{$this->data['sess']=array('URL'=>$this->_ci->config->item('frame_url'));}
                $this->_ci->session->set_userdata('FRAME',$this->data['sess']);
                log_message('debug','FRAME session id:'.$this->_ci->session->userdata('session_id'));
                
            $this->app_id=$this->_ci->config->item('app_id');
            $this->_app->set_app_id($this->app_id);
            $this->_session=new Active_Usersession($this->_user); //hot api
            log_message('debug','Initialize Frame library App ID:'.$this->app_id);
            $this->_app->initialize(); // initial after user has loged in
            //log_message('debug','Frame Application has initialized');
            
        }
        public function logout()
        {
            if($this->users()->logout()){
                unset($this->_app);
                unset($this->_user);
                unset($this->_nav);
                unset($this->_session);
                return true;
            }
            return false;
        }
        public function users(){
            return (($this->_user!=NULL)?$this->_user:new Usersession());
            }
        public function app(){
            return (($this->_app!=NULL)?$this->_app:new Appsession());
            }
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
                case 'app':
                    if($this->_app!=NULL){$this->app()->$properties=$value;}
                    else{$this->_app=new Appsession();}
                    break;
                case 'nav':
                    if($this->_nav!=NULL){$this->nav()->$properties=$value;}
                    else{$this->_nav=new Navigation();}
                    break;
                default: $this->data[$properties]=$value;
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
                case 'app': // not api
                    return $this->app();
                    break;
                case 'nav':
                    return $this->nav();
                    break;
                default: return $this->data[$properties];
                    //$this->_data[$properties];
            }
        }
        
}
class Session{ // not api
    protected $_ci;
    protected $_data=array();
    protected static $namespace='FRAME';
    public function __construct()
    {
        $this->_ci=&get_instance();
        $this->_ci->load->library('session');
        log_message('debug','Session id :'.$this->_ci->session->userdata('session_id'));
        $this->_ci->load->database();
        $this->_data=$this->_ci->session->userdata(self::$namespace);
        //print_r($this->_data);
    }
    public function RemoveSession()
    {
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
    private function get_db_session() //not api
    {
        $sess=$this->_ci->db->select('user_data')->from('ci_session')->where('session_id',$this->get_session_id())->get();
        if($sess->num_rows()>0)
        {
            $sess_data=unserialize($sess->row()->user_data);
            return $sess_data[self::$namespace];
        }
        return null;
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
class Active_Rolesession extends Session{ // not api
 protected static $TABLE_NAME='rbac_session_role';
 protected $role_node=array();
    public function __construct($role_node)
    {
        parent::__construct();
        $this->_userhasrole($role_node);
        $this->init();
    }
    public function init()
    {
     $this->_active_session();
     log_message('debug',"Load session Session complete.");
     
    }
    public function get_role_node(){return $this->role_node;}
    private function _userhasrole($role_node)
    {
        $roleid=array();
        parent::reload();
        $user=$this->_data['USERS'];
        foreach($role_node as $nodes=>$node){
            $roleid[]=$node->role_id;
        }
        if(empty($user)){
            show_error('Log in before');
            parent::RemoveSession();
        }
        $query=$this->_ci->db->select('role_id')->from('rbac_user_role')
                ->where('user_id',$user['user_id'])
                        ->where_in('role_id',$roleid)->get();
        if($query->num_rows()>0)
        {
            foreach($query->result() as $row){
                    foreach($role_node as $node=>$n)
                    {
                        if((int)$n->role_id==(int)$row->role_id)$this->role_node[]=$n;
                    }
            }
        }
        $query->free_result();
        unset($role_node);
        unset($roleid);
        unset($user);
    }
    private function _active_session()
    {
        //if(empty($this->role_node)) show_error('User not assigned to any roles Contact Administrator.');
        $data=array();
        foreach($this->role_node as $nodes=>$node){
            $q=$this->_ci->db->select('role_id')->from(self::$TABLE_NAME)
                    ->where(array('session_id'=>$this->get_session_id(),'role_id'=>$node->role_id))
                    ->get();
            if($q->num_rows()==0)
            {$data[]=array('session_id'=>$this->get_session_id(),'role_id'=>$node->role_id);}
            $q->free_result();
        }
        //$this->_ci->db->set($data)->insert(self::$TABLE_NAME);
        if(!empty($data)){
            $this->_ci->db->insert_batch(self::$TABLE_NAME, $data); 
        }
        log_message('debug',"Load session active role");
    }
    public function revoke_session() //database constriant implemented
    {
        $this->_ci->db->where('session_id',$this->_ci->session->userdata('session_id'))->delete(self::$TABLE_NAME);
    }
}
class Active_Usersession extends Session{ // not api
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
        $data=array('session_id'=>$this->get_session_id(),'user_id'=>$this->_userobj->get_user_id());
        $q=$this->_ci->db->select('user_id')->from(self::$TABLE_NAME)
                ->where(array('session_id'=>$this->get_session_id(),'user_id'=>$this->_userobj->get_user_id()))
                ->get();
        if($q->num_rows()<1)
        {$this->_ci->db->set($data)->insert(self::$TABLE_NAME);}
        $q->free_result();
        log_message('debug',"Load user's active session");
    }
    public function revoke_session() //database constriant implemented
    {
        $this->_ci->db->where('session_id',$this->_ci->session->userdata('session_id'))->delete(self::$TABLE_NAME);
    }
}
class Objects //not api
{
    protected $data;
    public function __construct()
    {
        $this->data['ci']=&get_instance();
        $this->data['ci']->load->database();
    }
    public function __get($properties){
        switch($properties)
        {
            case 'get': return $this->get();
                break;
            default:return $this->data[$properties];
        }
    }
    public function __set($properties,$value){$this->data[$properties]=$value;}
    public function get()
    {
        if(!array_key_exists('object_id',$this->data)) show_error('Properties Object Id not set');
        $query=$this->data['ci']->db->select('object_id,name')->from('rbac_objects')->where('object_id',$this->data['object_id'])->get();
        $this->data['data']=$query->row();
        $query->free_result();
        return $this->data['data'];
    }
    public function __destruct(){unset($this->data);}
}

class Operations //not api
{
    protected $data;
    public function __construct()
    {
        $this->data['ci']=&get_instance();
        $this->data['ci']->load->database();
    }
    public function __set($properties,$value){$this->data[$properties]=$value;}
    public function __get($properties){
        switch($properties)
        {
            case 'get':return $this->get();
                break;
            default:return $this->data[$properties];
        }
    }
    public function get(){
        if(!array_key_exists('operation_id',$this->data)) show_error('Properties Operation Id not set');
        $query=$this->data['ci']->db->select("_create as 'create',_read as 'read',_update as 'update',_delete as 'delete'")
                ->from('rbac_operations')->where('operation_id',$this->data['operation_id'])->get();
        $this->data['data']=$query->row();
        $query->free_result();
        return $this->data['data'];
    }
    public function __destruct() {
     unset($this->data);  
    }
}
class Permissions //not api
{
    //private $_permise_data;
    protected $data;
    public function __construct()
    {
        $this->data['ci']=&get_instance();
        $this->data['ci']->load->library('session');
        $this->data['ci']->load->database();
        $this->data['obj_instance']=new Objects();
        $this->data['operation_instance']=new Operations();
        $this->data['data']=array();
    }
    public function __destruct()
    {
        unset($this->data);
    }
    public function __set($properties,$value)
    {
        $this->data[$properties]=$value;
    }
    public function __get($properties)
    {
        return $this->data[$properties];
    }
    
    public function prepare()
    {
        $query=$this->data['ci']->db->select('name,object_id,operation_id')->from('rbac_permissions')
                ->where('permission_id',$this->data['permission_id'])->get();
        foreach($query->result() as $row)
        {
            $this->data['obj_instance']->object_id=$row->object_id;
            $this->data['operation_instance']->operation_id=$row->operation_id;
            //$this->data['data'][$row->name]=array($this->data['obj_instance']->get->name=>$this->data['operation_instance']->get());
            $this->data['data'][$this->data['obj_instance']->get->name]=$this->data['operation_instance']->get();  
        }
    }
    public function get(){return $this->data['data'];}
    /*
    public function hasPermise($permission=null)
    {
        return $this->multi_array_key_exists($permission,$this->_permise_data);
    }
     */
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
            $o->create=0;
            $o->read=0;
            $o->update=0;
            $o->delete=0;
            $this->data=array($name=>$o);
            log_message('debug','Create Temporary Object for return false invalid object');
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
        return $this->data->{$operation};
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
        //unset($this->data);
        $this->data=array();
        parent::RemoveSession();
        return (empty($this->data))?true:false;
        //return (!isset($this->data));
        return true;
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
class Appsession extends Session //not api reader
{
    private $app_id=-1;
    protected static $namespace='APP';
    protected $data=array();
    private $is_loaded=false;
    public function __construct()
    {
        parent::__construct();
    }
    public function initialize()
    {
        if(!$this->is_loaded){
            parent::reload();
            if(is_null($this->app_id)){show_error('properties not set for application id');}
            $this->reset();
            $this->createSession();
            $this->is_loaded=true;
            log_message('debug','Application ID: '.$this->app_id. ' is running.');
        }
    }
    public function set_app_id($id)
    {
        $id=(int) $id;
        if($this->app_id!=$id)
        {
            if($this->app_id!=-1){
                log_message('debug','Application ID: '.$this->app_id.' was stopped');
            }
            $this->app_id=$id;
            $this->is_loaded=false;
            log_message('debug','Set Application ID : '.$this->app_id);
        }
    }
    public function get_app_role_id()
    {
        log_message('debug','Gethering Rule ID of App Id '.$this->app_id);
        $query=$this->_ci->db->select('app_rule_id')->from('app_installed')->where('app_id',$this->app_id)->get();
        if($query->num_rows()>0){return (int)$query->row()->app_rule_id;}
        return -1;
    }
    public function reset()
    {
        //unset($this->data);
        $this->data=array();
    }
    public function get()
    {
        print_r($this->data);
    }
    public function createSession()
    {
        
        $role_obj=new Role();
        $role_obj->role_id=$this->get_app_role_id();
        $role_obj->getTreeroles();
        $rolesess=new Active_Rolesession($role_obj->rolenode);
        unset($role_obj);
        $rolenode=$rolesess->get_role_node();
        unset($rolesess);
        if(!empty($rolenode)){
            $r=new Rolepermission($rolenode);
        $this->data=$r->get();
        unset($r);
        }
        $this->_data[self::$namespace]=$this->data;
        parent::save();
            //print_r($this->_data);
        log_message('debug','Create session for application id : '.$this->app_id.' complete.');
    }
    public function __destruct(){unset($this);}
}
class Rolepermission
{
    protected $data;
    
    public function __construct($rolenode)
    {
        $this->data['ci']=&get_instance();
        $this->data['ci']->load->database();
        $this->data['rolenode']=$rolenode;
        $this->data['roleidlist']=array();
        if(!empty($this->data['rolenode'])){
            foreach($this->data['rolenode'] as $nodes=>$node){
                $this->data['roleidlist'][]=$node->role_id;
            }
        }
        $this->data['data']=array();
    }
    public function __destruct()
    {
        unset($this->data);
    }
    private function prepare()
    {
        //print_r($this->data['roleidlist']);
        $role=$this->data['ci']->db->select('rp.permission_id,p.name')->from('rbac_role_permission rp')
                ->join('rbac_permissions p','rp.permission_id=p.permission_id','left')
                ->where_in('rp.role_id',$this->data['roleidlist'])->get();
        foreach($role->result() as $row)
        {
            $pm=new Permissions();
            $pm->permission_id=$row->permission_id;
            $pm->prepare();
            $this->data['data'][$row->name]=$pm->get();
            unset($pm);  
        }           
    }
    public function get()
    {
        $this->prepare();
        return $this->data['data'];
    }
    public function __set($properties,$value)
    {
        $this->data[$properties]=$value;
    }
    public function __get($properties){return $this->data[$properties];}
}
class Role{
    protected $_ci;
    protected $data=array();
    public function __construct()
    {
        $this->_ci=&get_instance();
        $this->_ci->load->database();
    }
    public function __set($properties,$value)
    {
        $this->data[$properties]=$value;
    }
    public function __get($properties)
    {return $this->data[$properties];}
    public function hasRoles($role)
    {
            if(array_key_exists('rolenode',$this->data))
            {
               foreach($this->data['rolenode'] as $nodes=>$node)
               {
                   return (is_int($role))?(($node->role_id==$role)?true:false):(($node->name==$role)?true:false);
               }
            }
    }
    public function getTreeroles()
    {
        $rolenode=$this->getRole();
        if($rolenode)
        {
            $this->data['rolenode'][]=$rolenode;
            $this->_getParentRules($rolenode->role_id);
            log_message('debug','Gethering Tree rule ID: '.$rolenode->role_id.' complete');
        }
    }
    public function getRole()
    {
        $query=$this->_ci->db->select('role_id,parent_role_id,name')->from('rbac_roles')
                ->where('role_id',$this->data['role_id'])->get();
        if($query->num_rows()>0){
            return $query->row();
        }
        return false;
    }
    private function _getParentRules($role_id)
    {
        $query=$this->_ci->db->select('role_id,parent_role_id,name')->from('rbac_roles')->where('parent_role_id',$role_id)->get();
        if($query->num_rows()>0){
            foreach($query->result() as $row)
            {
                $this->data['rolenode'][]=$row;
                $this->_getParentRules($row->role_id);
            }
            $query->free_result();
        }
    }
    public function reset(){unset($this->data);}
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
        if(!array_key_exists($page,$this->data)){
            if(!empty($page)){
                $this->data[]=array(
                                'page'=>$page,
                                'link'=>$controller
                              );
            }
        }
    }
    public function save()
    {
        //$this->_data[self::$namespace]=$this->data;
        //parent::save();
    }
    public function reset(){unset($this->data);$this->data=array();}
    public function get()
    {
        if (!empty($this->data)){
            return $this->data;
        }return array();
    }
    public function __destruct(){unset($this);}
}
?>