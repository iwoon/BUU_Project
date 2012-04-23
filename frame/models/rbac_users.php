<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');
class Rbac_users extends CI_Model{
    protected static $TABLE_NAME='rbac_users';
    public $user_id=-1;
    public $username=NULL;
    public $password=NULL;
    public $first_name=NULL;
    public $family_name=NULL;
    public $email=NULL;
    public $created=NULL;
    
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
            $this->user_id=$this->_attr['user_id']=($this->_required(array('user_id'),$arg))?$arg['user_id']:$this->user_id;
            $this->username=$this->_attr['username']=($this->_required(array('username'),$arg))?$arg['username']:$this->username;
            $this->password=$this->_attr['password']=($this->_required(array('password'),$arg))?$arg['password']:$this->password;
            $this->first_name=$this->_attr['first_name']=($this->_required(array('first_name'),$arg))?$arg['first_name']:$this->first_name;
            $this->family_name=$this->_attr['family_name']=($this->_required(array('family_name'),$arg))?$arg['family_name']:$this->family_name;
            $this->email=$this->_attr['email']=($this->_required(array('email'),$arg))?$arg['email']:$this->email;
            $this->created=$this->_attr['created']=($this->_required(array('created'),$arg))?$arg['created']:$this->created;
        }
    }
    public function add($arg=array()){
        $this->_prepare_param($arg);
        return $this->user_id=$this->db->insert(self::$TABLE_NAME,$this->_attr)->insert_id();
    }
    public function delete($arg=array())
    {
        $this->_prepare_param($arg);
        return $this->db->delete(self::$TABLE_NAME,$this->_attr);
    }
    public function update($arg=array()){
        $this->_prepare_param($arg);
        return $this->db->update(self::$TABLE_NAME,$this->_attr);
    }
    private function _get($arg=array())
    {
        $this->_prepare_param($arg);
        return $this->db->get_where(self::$TABLE_NAME,$this->_attr);
    }
    public function getdata($arg=array(),$result='object')
    {
        $ret=$this->_get($arg);
        if($result=='array'){
        return (($ret->num_rows()>1)?$ret->result_array():$ret->row_array());
        }return (($ret->num_rows()>1)?$ret->result():$ret->row());
    }
}

?>
