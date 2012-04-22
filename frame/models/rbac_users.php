<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');
class Rbac_users extends CI_Model{
    protected static $TABLE_NAME='rbac_users';
    public $user_id=-1;
    public $username='';
    public $password='';
    public $first_name='';
    public $family_name='';
    public $email='';
    public $created='';
    
    public function __construct($arg=array()){
        parent::__construct();
        $this->created=time();
        $this->_prepare_param($arg);
        }
    private function _required($required,$data){
        foreach($required as $field){
            return (isset($data[$field]));
        }
    }
    private function _default($defaults,$option){return array_merge($defaults,$option);}
    private function _prepare_param($arg=array()){
        if(is_array($arg)){
            $this->user_id=($this->_required(array('user_id'),$arg))?$arg['user_id']:$this->user_id;
            $this->username=($this->_required(array('username'),$arg))?$arg['username']:$this->username;
            $this->password=($this->_required(array('password'),$arg))?$arg['password']:$this->password;
            $this->first_name=($this->_required(array('first_name'),$arg))?$arg['first_name']:$this->first_name;
            $this->family_name=($this->_required(array('family_name'),$arg))?$arg['family_name']:$this->family_name;
            $this->email=($this->_required(array('email'),$arg))?$arg['email']:$this->email;
            $this->created=($this->_required(array('created'),$arg))?$arg['created']:$this->created;
            $this->_attr=array(
                'user_id'=>$this->user_id,
                'username'=>$this->username,
                'password'=>$this->password,
                'first_name'=>$this->first_name,
                'family_name'=>$this->family_name,
                'email'=>$this->email,
                'created'=>$this->created
            );
        }
    }
    public function addUser(){
        
    }
}

?>
