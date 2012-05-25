<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Authentications
{
    protected $_ci;
    protected $user_id=NULL;
    protected $username=NULL;
    protected $password=NULL;
    protected $encryption='none';
    protected $is_authenticated=false;
    protected $authen_obj=NULL;
    public function __construct($autherizer=array())
    {
        $this->_ci=&get_instance();
        $this->_ci->load->database();
        //$this->_ci->load->library('Users');
        if(!empty($autherizer))
        {
            //$this->user_id=(array_key_exists('user_id',$arg))?$arg['user_id']:NULL;
            $this->username=(array_key_exists('username',$autherizer))?$autherizer['username']:NULL;
            $this->password=(array_key_exists('password',$autherizer))?$autherizer['password']:NULL;
        }elseif(empty($this->username)||empty($this->password)){ // prevent empty important key
            show_error('please set username & password before!');
        }
        $this->_init();
        
    }
    //public function set_authorizer($)
    private function _init()
    {
        $this->_authen_type();
        log_message('debug','Check user authen type');
    }
    public function login(){
        $this->is_authenticated=$this->authen_obj->login();
        return $this->authenticated();
    } //alias methode
    public function authenticated()
    {
        return $this->is_authenticated;
    }
    public function get_authen_obj()
    {
        /*$obj=new stdClass();
        $obj->user_id=$this->user_id;
        $obj->username=$this->username;
        $obj->password=$this->password;
        $obj->encryption=$this->encryption;
        $obj->is_authenticated=$this->is_authenticated;
        return $obj;*/
        return $this->authen_obj;
    }
    
    private function _authen_type(){
          $this->_ci->load->model('Rbac_users_model','users_model');
                $this->_ci->users_model->set('username',$this->username);
                    log_message('debug','Loaded Rbac_usres_model');
                    
                        switch($this->_ci->users_model->get_authen_type())
                        {
                            
                            case 'Ldap':
                                    //$this->load->library('Authen_Ldap',array('username'=>$this->username,'password'=>$this->input->post('password')));
                                    //$user=($this->authen_ldap->login())?$this->_ci->users_model->getdata():'';
                                    $this->authen_obj=new Authen_Ldap(array('username'=>$this->username,'password'=>$this->password));
                                break;
                            default:
                                    $this->authen_obj=new Authen_Internal(array('username'=>$this->username,'password'=>$this->password));
                                
                        }
			//return $user;
                }
}
Abstract class Abstract_Authen
{
    protected $_ci;
    protected $user_id=NULL;
    protected $username=NULL;
    protected $password=NULL;
    protected $encryption='none';
    protected $is_authenticated=false;
    public function __construct($autherizer=array())
    {
        $this->_ci=&get_instance();
        $this->_ci->load->database();
        //$this->_ci->load->library('Users');
        if(!empty($autherizer))
        {
            //$this->user_id=(array_key_exists('user_id',$arg))?$arg['user_id']:NULL;
            $this->username=(array_key_exists('username',$autherizer))?$autherizer['username']:NULL;
            $this->password=(array_key_exists('password',$autherizer))?$autherizer['password']:NULL;
        }elseif(empty($this->username)||empty($this->password)){ // prevent empty important key
            
            show_error('please set username & password before!'.$this->username.$this->password);
        }
        $this->_init();
        
    }
    private function _init()
    {
        $this->users_encryption_type();
        $this->encrypt_password();
        log_message('debug','Initialize authorizer complete');
    }
    public function set_username($value){$this->username=$value;}
    public function set_password($value){$this->password=$value;}
    public function set_user_id($value){$this->user_id;}
    public function get_user_id(){return $this->user_id;}
    public function get_username(){return $this->username;}
    protected function set_encryption($value){$this->encryption;}
    protected function login(){
        $this->is_authenticated=$this->authen_obj->login();
        return $this->authenticated();
    } //interface class
    protected function authenticated()
    {
        return $this->is_authenticated;
    }
    protected function users_encryption_type()
    {
        $query=$this->_ci->db->select('encryption_type')
                ->from('rbac_users')
                ->where(array('username'=>$this->username))
                ->or_where(array('user_id'=>$this->user_id))
                ->get();
        if($query->num_rows()>0){
            $this->encryption=$query->row()->encryption_type;
        }
        log_message('debug','Get Users Encryption password type');
        return $this->encryption;
    }
    protected function encrypt_password()
    {
        switch($this->encryption)
        {
            case 'md5':
                    $this->password=md5($this->password);
                
                break;
            case 'sha1':
                    $this->password=sha1($this->password);
                break;
            case 'sha256':
                    /* not implement */
                break;
            case 'etc':
                    /* for you implement any algorithms */
                break;
            default:
                /* not do anything */
        }
        log_message('debug','encryption password complete');
    }
}
class Authen_Ldap extends Abstract_Authen
{
    //private $_ci;
    /*public $username=NULL;
    public $password=NULL;
    protected $user_id=NULL;*/
    private $_ldap_config=array();
    private $_ldapconn;
  /*  public function __construct($authen=array())
    {
        $this->_ci=&get_instance();
        $this->username=$authen['username'];
        $this->password=$authen['password'];
        $this->_get_user_config();
        $this->login();
    }*/
    public function __construct($authorizer=array())
    {
        parent::__construct($authorizer);
    }
    private function _get_user_config()
    {
        /*$sql="select at.*,ac.* from prj_authen_type as at left join prj_authen_config as ac on ac.authen_id=at.authen_id inner join prj_rbac_users as usr on usr.authen_id=at.authen_id
where usr.username='".$this->username."'";*/
        $query=$this->_ci->db->select('at.*,ac.*')->from('authen_type at')
                ->join('authen_config ac','ac.authen_id=at.authen_id','left')
                ->join('rbac_users usr','usr.authen_id=at.authen_id')
                ->where('usr.username',$this->username)->get();
        //$query=$this->_ci->db->query($sql);
        if($query->num_rows()>0){
            foreach($query->result() as $config){
                $this->_ldap_config['host']=((!array_key_exists('host',$this->_ldap_config))?trim($config->authen_server):$this->_ldap_config['host']);
                $this->_ldap_config['port']=((!array_key_exists('port',$this->_ldap_config))?trim($config->authen_port):$this->_ldap_config['port']);
                $this->_ldap_config[trim($config->properties)]=trim($config->value);
            }
            log_message('debug','load ldap configuration to memory');
        }
    }
    private function _authenticate() {
        $needed_attrs = array('dn', 'cn', $this->_ldap_config['login_attribute']);
            $this->_ldapconn = @ldap_connect($this->_ldap_config['host'],$this->_ldap_config['port']);
            log_message('debug',"Connect to Ldap server");
        // At this point, $this->ldapconn should be set.  If not... DOOM
            //$this->_ldapconn=false; // test
 
        if(!$this->_ldapconn) {
            log_message('error', "Couldn't connect to any LDAP servers.  Bailing...");
            //show_error('Error connecting to your LDAP server(s).  Please check the connection and try again.');
            return false;
        }
        // We've connected, now we can attempt the login...
        if(((int)$this->_ldap_config['use_ad'])==1) {
            log_message('debug','bind ldap use ad');
            ldap_set_option($this->_ldapconn, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($this->_ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
            if(preg_match('/^(\w+\.)+\w{2,4}$/', $this->_ldap_config['ad_domain'])) {
                $binddn = $this->username.'@'.$this->_ldap_config['ad_domain'];
            }else {
                $binddn = $this->_ldap_config['ad_domain'].'\\'.$this->username;
            }
            $bind = (ldap_start_tls($this->_ldapconn))?@ldap_bind($this->_ldapconn, $binddn, $this->password):false;
            if(!$bind){
                log_message('error', 'Unable to perform anonymous/proxy bind');
                show_error('Unable to bind for user id lookup');
                return false;
            }
            return true;
            /*$filter = '('.$this->_ldap_config->properties['login_attribute'].'='.$this->username.')';  
            $search = ldap_search($this->_ldapconn, $this->_ldap_config->properties['basedn'], $filter, $needed_attrs);
            $entries = ldap_get_entries($this->_ldapconn, $search);
            if($entries['count'] == 0) {
                show_error('General ldap_search error: '.ldap_err2str(ldap_errno($this->ldapconn)));
            }
            */
        }else {
            // Find the DN of the user we are binding as
            // If proxy_user and proxy_pass are set, use those, else bind anonymously
            log_message('debug','bind ldap use proxy');
            if($this->_ldap_config['proxy_user']) {
                $bind = (ldap_start_tls($this->_ldapconn))?@ldap_bind($this->_ldapconn, $this->_ldap_config['proxy_user'], $this->_ldap_config['proxy_pass']):false;
            }else {
                $bind = (ldap_start_tls($this->_ldapconn))?@ldap_bind($this->_ldapconn):false;
            }

            if(!$bind){
                log_message('error', 'Unable to perform anonymous/proxy bind');
                show_error('Unable to bind for user id lookup');
                return false;
            }
            
            log_message('debug', 'Successfully bound to directory.  Performing dn lookup for '.$username);
            $filter = '('.$this->_ldap_config['login_attribute'].'='.$this->username.')';
            $search = ldap_search($this->_ldapconn, $this->_ldap_config['basedn'], $filter, array('dn', $this->_ldap_config['login_attribute'], 'cn'));
            $entries = ldap_get_entries($this->_ldapconn, $search);
            $binddn = $entries[0]['dn'];
            
            // Now actually try to bind as the user
            $bind = (ldap_start_tls($this->_ldapconn))?ldap_bind($this->_ldapconn, $binddn, $this->password):false;
            if(! $bind) {
                log_message('debug',"Failed login attempt: ".$this->username." from ".$_SERVER['REMOTE_ADDR']);
                return FALSE;
            }
            /*
            $cn = $entries[0]['cn'][0];
            $dn = stripslashes($entries[0]['dn']);
            $id = $entries[0][$this->login_attribute][0];
        
             */
        }
        @ldap_unbind($bind);
        return true;
        /*
        if($this->use_ad) {
            $get_role_arg = $dn;
        }else {
            $get_role_arg = $id;
        }
                       
        
        return array('cn' => $cn, 'dn' => $dn, 'id' => $id,
            'role' => $this->_get_role($get_role_arg));
         * 
         */
    }
    public function login() {
        /*
         * For now just pass this along to _authenticate.  We could do
         * something else here before hand in the future.
         */
        /*$user_info = $this->_authenticate($username,$password);
        if(empty($user_info['role'])) {
            log_message('info', $username." has no role to play.");
            show_error($username.' succssfully authenticated, but is not allowed because the username was not found in an allowed access group.');
        }
        // Record the login
        $this->_audit("Successful login: ".$user_info['cn']."(".$username.") from ".$this->ci->input->ip_address());

        // Set the session data
        $customdata = array('username' => $username,
                            'cn' => $user_info['cn'],
                            'role' => $user_info['role'],
                            'logged_in' => TRUE);
    */
        log_message('debug','Authenticated by '.get_class());
       if($this->_authenticate()){
           
           @ldap_close($this->_ldapconn);
           
           $query=$this->_ci->db->select('user_id')->from('rbac_users')
                ->where(array('username'=>$this->username))
                ->get();
        if($query->num_rows()>0){$this->user_id=$query->row()->user_id;}
        return true;
       }
       return false;
    }
    
    
    
}
class Authen_Internal extends Abstract_Authen
{
   /* public $username=NULL;
    public $password=NULL;
    protected $user_id=NULL;*/
    public function __construct($authorizer=array())
    {
        parent::__construct($authorizer);
    }
    public function login()
    {
        log_message('debug','Authenticated by '.get_class());
        //var_dump($this->username.' '.$this->password);
        return $this->_authenticate();
        
    }
    private function _authenticate()
    {
        $query=$this->_ci->db->select('user_id')->from('rbac_users')
                ->where(array('username'=>$this->username,'password'=>$this->password))
                ->get();
        if(($query->num_rows())>0)
        {
            $this->is_authenticated=true;
            $this->user_id=$query->row()->user_id;
            return true;
        }return false;
    }
}
?>
