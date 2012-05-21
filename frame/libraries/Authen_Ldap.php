<?php if(!defined('BASEPATH')) exit('Not direct script access allowed.');
class Authen_Ldap
{
    private $_ci;
    public $username=NULL;
    public $password=NULL;
    private $_ldap_config=array();
    private $_ldapconn;
    public function __construct($authen=array())
    {
        $this->_ci=&get_instance();
        $this->username=$authen['username'];
        $this->password=$authen['password'];
        $this->_get_user_config();
        $this->login();
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
        
       if($this->_authenticate()){
           @ldap_close($this->_ldapconn);
           return true;
       }
       return false;
    }
    
    
    
}

?>
