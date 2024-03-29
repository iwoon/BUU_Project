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
    public function save($data,$tablename="")
	{
		if($tablename=="")
		{
			$tablename = self::$TABLE_NAME;
		}
		$op = 'update';
		$keyExists = FALSE;
		$fields = $this->db->field_data($tablename);
		foreach ($fields as $field)
		{
			if($field->primary_key==1)
			{
				$keyExists = TRUE;

				if(isset($data[$field->name]))
				{
					$this->db->where($field->name, $data[$field->name]);
				}
				else
				{
					$op = 'insert';
				}
			}
		}
		if($keyExists && $op=='update')
		{
			$this->db->set($data);
			$this->db->update($tablename);
                        echo $this->db->affected_rows();
			if($this->db->affected_rows()==1)
			{
                            return $this->db->affected_rows();
			}
		}
		$this->db->insert(self::$TABLE_NAME,$data);
	 
		return $this->db->affected_rows();
	 
	}
    public function save1()
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
    public function getdata($result='object')
    {
        $ret=$this->_get();
        $this->_data=$ret->row();
        if($result=='array'){
        return (($ret->num_rows()>1)?$ret->result_array():$ret->row_array());
        }return (($ret->num_rows()>1)?$ret->result():$ret->row());
    }
    public function checkauthen($user_id,$password)
    {
        return $this->db->select('user_id')->from(self::$TABLE_NAME)->where(array('user_id'=>$user_id,'password'=>$password))
                ->get()->num_rows();
    }
}

?>
