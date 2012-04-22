<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');

class Rbac_session{
    protected static $TABLE_NAME='rbac_sessions';
    public $session_id;
    public $user_id;
    public $name;
    private $_ci;
    private $_attr=array();
    private $rbac_sess_m;
    
    public function __construct($arg=NULL){
        $this->_ci=&get_instance();
        $this->_ci->load->library('session');
        $this->session_id=$this->_ci->session->userdata('session_id');
        $this->user_id=$this->_ci->session->get_user_id();
        $this->_ci->load->model('Rbac_session_model','rbac_sess_m');
        if(is_array($arg)){
            $this->add($arg);
        }
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
            $this->name=($this->_required(array('name'),$arg)?$arg['name']:'');
            $this->_attr=array(
              'session_id'=>$this->session_id,
                'user_id'=>$this->user_id,
                'name'=>$this->name
            );
        }
    }
    public function add($arg=array()){
        $this->_prepare_param($arg);
        return $this->_ci->rbac_sess_m->add($this->_attr);
    }
    public function update($arg=array()){
        $this->_prepare_param($arg);
        $this->_ci->rbac_sess_m->set($this->attr);
        return $this->_ci->rbac_sess_m->update();
    }
    public function delete($arg=array()){
        $this->_prepare_param($arg);
        return $this->_ci->rbac_sess_m->delete($arg);
    }
    private function _get($arg=array()){
        $this->_prepare_param($arg);
        return $this->_ci->rbac_sess_m->get($this->_attr);
    }
    public function getdata($arg=array(),$result='object'){
        $ret=$this->_get($arg);
        if($result=='array'){
        return (($ret->num_rows()>1)?$ret->result_array():$ret->row_array());
        }return (($ret->num_rows()>1)?$ret->result():$ret->row());
    }
}

?>
