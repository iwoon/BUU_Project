<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');
class Rbac_users extends CI_Model{
    protected $table;
    private $_data;
    public function __construct(){
        parent::__construct();
        $this->table=strtolower(get_class($this));
        $this->_data['username']=NULL;
        $this->_data['password']=NULL;
        $this->_data['user_id']=NULL;
    }
    public function set($properties,$value){$this->_data[$properties]=$value;}
    public function get($properties){return $this->_data[$properties];}
    /*public function save()
    {
        if($this->_get()->num_rows()>0){
            //data already exists
            $this->db->where('username',$this->_data['username'])->or_where('user_id',$this->_data['user_id'])->update($this->table,$this->_data);
        }
        $this->db->insert($this->table,$this->_data);
    }*/
    public function get_user_has_roles($user_id=null)
    {
        if($user_id==null)return false;
        return $this->db->select('u.user_id,u.firstname,u.lastname,r.role_id,r.name,r.description,r.parent_role_id')
                ->from('rbac_user_role ur')
                ->join('rbac_roles r','ur.role_id=r.role_id')
                ->join('rbac_users u','ur.user_id=u.user_id')
                ->where('ur.user_id',$user_id)->get()->result();
    }
    public function get_user_parent_roles($user_id=null)
    {
        return $this->db->select('u.user_id,u.firstname,u.lastname,r.role_id,r.name,r.description,r.parent_role_id')
                ->from('rbac_user_role ur')
                ->join('rbac_roles r','ur.role_id=r.role_id and r.parent_role_id is null')
                ->join('rbac_users u','ur.user_id=u.user_id')
                ->where('ur.user_id',$user_id)->get()->result();
    }
    public function get_users_permission($user_id=null)
    {
      if(!$this->frame->users()->checkaccess('users_management','permission')->read())return false;
        if($user_id!=null)
        {
            return $this->db->select("r.role_id,r.name as role_name,r.description as role_description,
                                pg.permission_group_id,pg.name as permission_group_name,
                                p.name as permission_name,p.object_id,obj.name as object_name,p.operation_id,
                                op.name as operation_name,op._read as 'read',op._create as 'create',op._update as 'update',op._delete as 'delete'")
                ->from('rbac_user_role ur')->join('rbac_roles r','ur.role_id=r.role_id')
                ->join('rbac_role_permission rp','r.role_id=rp.role_id')
                ->join('rbac_permissions p','rp.permission_id=p.permission_id')
                ->join('rbac_permission_group pg','pg.permission_group_id=p.permission_group_id')
                ->join('rbac_operations op','op.operation_id=p.operation_id')
                ->join('rbac_objects obj','obj.object_id=p.object_id')
                ->where('ur.user_id',$user_id)
                ->order_by('r.role_id','asc')->get()->result();
        }return false;
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
        //return $this->db->get_where($this->table,array('username'=>$this->_data['username']));
         $this->db->select('usr.*,at.*');
         $this->db->from($this->table.' usr');
         $this->db->join('authen_type at','usr.authen_id=at.authen_id');
         $this->db->where('usr.username',$this->_data['username']);
         $this->db->or_where('usr.user_id',$this->_data['user_id']);
         return $this->db->get();
    }
    public function get_users_list($condition=array())
    {
        $user=$this->db->select('usr.* ,at.*')->from($this->table.' usr')->join('authen_type at','usr.authen_id=at.authen_id')
                    ->where('user_id !=',0);
         if(array_key_exists('user_id',$condition))
            {
                $user->where_in('user_id',$users_id);
            }
         if(array_key_exists('creater_id',$condition))
         {
             $user->where('creater_id',$condition['creater_id']);
         }
         if(array_key_exists('limit',$condition))
            {
                $user->limit($condition['limit']['rowperpage'],$condition['limit']['begin']);
            }
            $query=$user->get();
            $this->_data['num_users']=$this->db->count_all($this->table)-1;
        return $query->result();
    }
    public function delete($user_id)
    {
        if($this->frame->users()->hasPermission('users_management')->object('users')->delete())
        {
            return $this->db->delete($this->table,array('user_id'=>$user_id))->affected_rows();
        }
        return false;
    }
    public function delete_by_id($user_id=array())
    {
        if($this->frame->users()->hasPermission('users_management')->object('users')->delete())
        {
            $user_id=implode(',',$user_id);
            return $this->db->query('delete from '.$this->db->dbprefix.$this->table.' where user_id in (?)',array($user_id));
        }
        return false;
    }
    public function get_num_users()
    {
        return $this->_data['num_users'];
    }
    public function getdata($result='object')
    {
        $ret=$this->_get();
        $this->_data=$ret->row();
        if($result=='array'){
        return (($ret->num_rows()>1)?$ret->result_array():$ret->row_array());
        }return (($ret->num_rows()>1)?$ret->result():$ret->row());
    }
     public function save($data,$tablename="")
	{
		if($tablename=="")
		{
			$tablename = $this->table;
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
			if($this->db->affected_rows()==1)
			{
				return $this->db->affected_rows();
			}
		}
		$this->db->insert($this->table,$data);
	 
		return $this->db->affected_rows();
	 
	}
}

?>
