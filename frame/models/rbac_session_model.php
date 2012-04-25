<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');
class Rbac_session_model extends CI_Model{
    public static $TABLE_NAME='rbac_sessions';
    public $session_id;
    public $user_id='';
    public $name='';
    public $created;
    private $_attr=array();
    
    public function __construct(){
        parent::__construct();
        $this->created=time();
    }
    private function _required($required,$data){
        foreach($required as $field){
            return (isset($data[$field]));
        }
    }
    private function _default($defaults,$option){return array_merge($defaults,$option);}
    private function _prepare_param($arg=array()){
        if(is_array($arg)){
            $this->session_id=($this->_required(array('session_id'),$arg))?$arg['session_id']:$this->session_id;
            $this->user_id=($this->_required(array('user_id'),$arg))?$arg['user_id']:'';
            $this->name=($this->_required(array('name'),$arg))?$arg['name']:'';
            $this->_attr=array(
                'session_id'=>$this->session_id,
                'user_id'=>$this->user_id,
                'name'=>$this->name
            );
        }
    }
    public function add($arg=array()){
        $this->_prepare_param($arg);
        return $this->db->insert(self::$TABLE_NAME,$this->_attr);
    }
    public function update($arg=array()){
       $this->_prepare_param($arg);
       return $this->db->update(self::$TABLE_NAME,$this->_attr);
    }
    public function delete($arg=array()){
        $this->_prepare_param($arg);
        $this->db->delete(self::$TABLE_NAME,$this->_attr);
    }
    public function get($arg=array()){
        $this->_prepare_param($arg);
        return $this->db->get_where(self::$TABLE_NAME,$this->_attr);
    }
}
?>
