<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');
class Rbac_permissions_model extends CI_Model
{
    public static $TABLE_NAME='rbac_permissions';
    public $permission_id;
    public $description;
    public $name;
    public $objects;
    public $operations;
    private $_attr=array();
    public function __construct($arg=array()){
        parent::__construct();
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
            $this->permission_id=$this->_attr['permission_id']=($this->_required(array('name'),$arg))?$arg['name']:$this->user_id;
            $this->name=$this->_attr['name']=($this->_required(array('name'),$arg))?$arg['name']:$this->name;
            $this->description=$this->_attr['description']=($this->_required(array('description'),$arg))?$arg['description']:$this->description;
            $this->objects=$this->_attr['objects']=($this->_required(array('objects'),$arg))?$arg['objects']:$this->objects;
            $this->operations=$this->_attr['operations']=($this->_required(array('operation_id'),$arg))?$arg['operation_id']:$this->operation_id;
        }
    }
    public function Grant($arg=array())
    {
        $this->_prepare_param($arg);
        $this->load->model('rbac_objects_model');
        if(!$this->db->get_where(Rbac_objects_model::$TABLE_NAME,$this->objects))
        {
            $this->objects=new Rbac_objects_model($this->objects);
        }
        $this->load->model('rbac_operatoins_model');
        if(!$this->db->get_where(Rbac_operations_model::$TABLE_NAME,$this->operations))
        {
            $this->operations=new Rbac_operations_model($this->operations);
        }
        $pid=$this->db->insert(self::$TABLE_NAME,$this->_attr);
        $this->_prepare_param(array('permission_id'=>$pid));
    }
    public function Revoke($arg=array())
    {
        $this->_prepare_param($arg);
        $this->db->delete(self::$TABLE_NAME,$this->_attr);
    }
    
    
}
?>
