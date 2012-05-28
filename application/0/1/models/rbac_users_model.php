<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');
class Rbac_users_model extends CI_Model{
    public static $TABLE_NAME='rbac_users';
    private $_data;
    public function __construct(){
        parent::__construct();
        $this->_data['username']=NULL;
        $this->_data['password']=NULL;
        $this->_data['user_id']=NULL;
    }
    public function set($properties,$value){
         $this->_data[$properties]=$value;
    }
    public function get($properties){return $this->_data[$properties];}
    public function save()
    {
        if($this->_get()->num_rows()>0){
            //data already exists
            $this->db->where('username',$this->_data['username'])->or_where('user_id',$this->_data['user_id'])->update(self::$TABLE_NAME,$this->_data);
        }
        $this->db->insert(self::$TABLE_NAME,$this->_data);
    }
    public function get_authen_type(){
      $userdata=$this->_get();
      if($userdata->num_rows()>0 && $userdata->num_rows()<2){
          return (string) $userdata->row()->authen_type;
      }
      return ' ';
    }
    private function _get()
    {
        //return $this->db->get_where(self::$TABLE_NAME,array('username'=>$this->_data['username']));
         $this->db->select('usr.*,at.*');
         $this->db->from(self::$TABLE_NAME.' usr');
         $this->db->join('authen_type at','usr.authen_id=at.authen_id');
         $this->db->where('usr.username',$this->_data['username']);
         $this->db->or_where('usr.user_id',$this->_data['user_id']);
         return $this->db->get();
    }
    public function get_users_list($users=array())
    {
        if(empty($users))
        {
         $user=$this->db->select('usr.* ,at.*')->from(self::$TABLE_NAME.' usr')->join('authen_type at','usr.authen_id=at.authen_id'); 
        }else
        {
            $user=$this->db->select('usr.*,at.*')
                    ->from(self::$TABLE_NAME.' usr')
                    ->join('authen_type at','usr.authen_id=at.authen_id')->where_in('user_id',$users);
        }
        
        return $user->get()->result();
    }
    public function getdata($result='object')
    {
        $ret=$this->_get();
        $this->_data=$ret->row();
        if($result=='array'){
        return (($ret->num_rows()>1)?$ret->result_array():$ret->row_array());
        }return (($ret->num_rows()>1)?$ret->result():$ret->row());
    }
}

?>
