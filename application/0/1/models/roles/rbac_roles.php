<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Rbac_roles extends CI_Model
{
    protected $table;
    protected $data=array();
    public function __construct()
    {
        parent::__construct();
        $this->table=strtolower(get_class($this));
        $this->load->database();
    }
    public function get_roles($id=NULL)
    {
        $roles=$this->db->select('*')->from($this->table);
        if($id!=null)
        {
            if(is_array($id))
            {
                $roles->where_in('role_id',$id);
            }
            $roles->where('role_id',$id);
        }
        return $roles->get()->result();
    }
    public function get_not_assigned_roles($user_id=null)
    {
        $sql='select role_id,name,description 
            from prj_rbac_roles  where role_id not in 
            (select role_id from prj_rbac_user_role where user_id='.$user_id.')';
        return $this->db->query($sql)->result();
    }
    public function get_all_roles()
    {
        return $this->db->select('*')->from($this->table)->get()->result();
    }
    public function get_all_roles_condition($condition=array())
    {
        if(empty($condition))
        {
            return $this->get_all_roles();
        }
        $role=$this->db->select('*')->from($this->table);
        
        if(array_key_exists('limit',$condition))
            {
                $limit=$condition['limit'];
                //
            }
        if(is_array($condition))
        {
            if(isset($limit)){
                unset($condition['limit']);
            }
            $role->where($condition);
            if(isset($limit)){
                $role->limit($limit['rowperpage'],$limit['begin']);
            }
        }
        $data=array();$role=$role->get();
        $data['num_roles']=$role->num_rows();
        $data['rolelist']=$role->result();
        return $data;
    }
    public function get_child_roles($parent=null)
    {
         if($parent==null)return false;
            return $this->db->select('*')->from($this->table)->where('parent_role_id',$parent)->get()->result();
    }
    public function get_parent_roles($id=null)
    {
        $roles=$this->db->select('*')->from($this->table)->where('parent_role_id is null','',false);
        if($id!=null)
        {
            $roles->where('role_id',$id);
        }
        return $roles->get()->result();
    }
    public function get_child_to_parent($child)
    {
        if(!empty($child)){$this->data['roleidlist'][]=$child;}
        $q=$this->db->select('parent_role_id')->from($this->table)->where('role_id',$child)->get()->row();
        if(empty($q))return $this->data['roleidlist'];
        return $this->get_child_to_parent($q->parent_role_id);
    }
    public function getTreeroles($role_id)
    {
        $rolenode=$this->getRole($role_id);
        if($rolenode)
        {
            $this->data['rolenode'][]=$rolenode;
            $this->_getParentRules($rolenode->role_id);
        }
        return $this->data['rolenode'];
    }
    private function getRole($role_id)
    {
        $query=$this->db->select('role_id,parent_role_id,name,description')->from('rbac_roles')
                ->where('role_id',$role_id)->get();
        if($query->num_rows()>0){
            return $query->row();
        }
        return false;
    }
    private function _getParentRules($role_id)
    {
        $query=$this->db->select('role_id,parent_role_id,name,description')->from('rbac_roles')->where('parent_role_id',$role_id)->get();
        if($query->num_rows()>0){
            foreach($query->result() as $row)
            {
                $this->data['rolenode'][]=$row;
                $this->_getParentRules($row->role_id);
            }
            $query->free_result();
        }
    }
    public function delete_by_id($id=null)
    {
        if($this->frame->users()->hasPermission('roles_management')->object('roles')->delete())
        {
            $id=implode(',',$id);
                $query=$this->db->query('delete from prj_'.$this->table.' where role_id in ('.$id.')');
                return true;
            //return $this->db->delete(self::$TABLE_NAME)->where_in('user_id',$id)->affected_rows();
        }
        return false;
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
