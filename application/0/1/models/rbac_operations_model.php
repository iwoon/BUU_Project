<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');
class Rbac_operations_model extends CI_Model
{
    public static $TABLE_NAME='rbac_operations';
    public $operation_id=-1;
    public $name=NULL;
    public $_create=NULL;
    public $_read=NULL;
    public $_update=NULL;
    public $_delete=NULL;
    public $locked=0;
    private $_attr=array();
    public function __construct($arg=array())
    {
        parent::__construct();
        
    }
    private function _required($required,$data){
        foreach($required as $field){
            return (isset($data[$field]));
        }
    }
    private function _default($defaults,$option){return array_merge($defaults,$option);}
    private function _prepare_param($arg=array()){
        if(is_array($arg)){
            $this->object_id=$this->_attr['object_id']=($this->_required(array('object_id'),$arg))?$arg['object_id']:$this->object_id;
            $this->name=$this->_attr['name']=($this->_required(array('name'),$arg))?$arg['name']:$this->name;
            $this->loced=$this->_attr['locked']=($this->_required(array('locked'),$arg))?$arg['locked']:$this->locked;
         }
    }
    public function Add($arg=array())
    {
        $this->_prepare_param($arg);
        $id=$this->db->insert(self::$TABLE_NAME,$this->attr);
        return $this->_prepare_param(array('operation_id'=>$id));
    }
    public function Remove($arg=array())
    {
        $this->_prepare_param($arg);
        return $this->db->delete(self::$TABLE_NAME,$this->operatoin_id);
    }
    public function get($arg=array())
    {
        $this->_prepare_param($arg);
        $ret=$this->db->get_where(self::$TABLE_NAME,$this->_attr);
        return ($ret->num_rows()>1)?$ret->result():$ret->row();
    }
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
