<?php
class Rbac_user_role extends CI_Model
{
        protected $table;
        public function __construct()
        {
            $this->table=strtolower(get_class($this));
            $this->load->database();
        }
        public function assign_role($role_id,$user_id)
        {
            if(!$this->frame->users()->hasPermission('roles_management')->object('assigment'))return false;
            $data=array('role_id'=>$role_id,'user_id'=>$user_id);
            return $this->save($data);
        }
        public function revoke_role($role_id,$user_id)
        {
            if(!$this->frame->users()->hasPermission('roles_management')->object('revoke'))return false;
            if(is_array($role_id))
            {
                return $this->db->delete($this->table)->where_in('role_id',$role_id)->where('user_id',$user_id)->affected_rows();
            }
            $role_id=(int)$role_id;
            $user_id=(int)$user_id;
            return $this->db->delete($this->table,array('user_id'=>$user_id,'role_id'=>$role_id));
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
	public function get_role_members($condition=null)
        {
            if($condition==null)return false;
            if(!$this->frame->users()->checkaccess('roles_management','users')->read())return false;
            if(array_key_exists('role_id',$condition))
            {
                $role_id=$condition['role_id'];
            }
            $members=$this->db->select("u.user_id,u.username,u.firstname,u.lastname")->from($this->table.' ur')
                    ->join('rbac_users u','ur.user_id=u.user_id')->where('ur.role_id',$role_id);
            if(array_key_exists('limit',$condition))
            {
                $limit=$condition['limit'];
                
                $members->limit($limit['rowperpage'],$limit['begin']);
            }
            $members=$members->get();
            $data['num_members']=$members->num_rows();
            $data['members']=$members->result();
            return $data;
        }
        public function get_not_role_members($condition=null)
        {
            if($condition==null)return false;
            if(!$this->frame->users()->checkaccess('roles_management','users')->read())return false;
            if(array_key_exists('role_id',$condition))
            {
                $role_id=$condition['role_id'];
            }
            $sql="select * from prj_rbac_users where user_id not in 
                (select user_id from prj_rbac_user_role where role_id=".$role_id.") ";
            if(array_key_exists('limit',$condition))
            {
                $limit=$condition['limit'];
                
                $sql.=" limit ".$limit['begin'].",".$limit['rowperpage'];
            }
            $q=$this->db->query($sql);
            $data['num_members']=$q->num_rows();
            $data['members']=$q->result();
            return $data;
        }
	function search($conditions=NULL,$tablename="",$limit=500,$offset=0)
	{
		if($tablename=="")
		{
			$tablename = $this->table;
		}
		if($conditions != NULL)
			$this->db->where($conditions);
	 
		$query = $this->db->get($tablename,$limit,$offset=0);
		return $query->result();
	}
	 
	function insert($data,$tablename="")
	{
		if($tablename=="")
			$tablename = $this->table;
		$this->db->insert($tablename,$data);
		return $this->db->affected_rows();
	}
	 
	function update($data,$conditions,$tablename="")
	{
		if($tablename==""){
			$tablename = $this->table;}
		$this->db->where($conditions);
		$this->db->update($tablename,$data);
		return $this->db->affected_rows();
	
        }
}
?>