<?php
class Rbac_permissions extends CI_Model
{
    protected $table;
    public function __construct()
    {
        parent::__construct();
        $this->table=strtolower(get_class($this));
        $this->load->model('operations/rbac_operations','operations');
    }
    public function get_all_permission()
    {
        return $this->db->select("r.role_id,r.name as role_name,r.locked as role_locked,r.description as role_description,
                        p.permission_id,p.permission_group_id,p.object_id,p.operation_id,p.name as permission_name,p.locked,
                        pg.permission_group_id,pg.name as permission_group_name,pg.description as permission_group_description,
                        obj.name as object_name,
                        op._read as 'read',op._create as 'create',op._update as 'update',op._delete as 'delete'")
                ->from('rbac_roles r')
                ->join('rbac_role_permission rp','r.role_id=rp.role_id')
                ->join('rbac_permissions p','rp.permission_id=p.permission_id')
                ->join('rbac_permissions_group pg','pg.permission_group_id=p.permission_group_id')
                ->join('rbac_operations op','op.operation_id=p.operation_id')
                ->join('rbac_objects obj','obj.object_id=p.object_id')
                ->order_by('r.role_id','asc')->get()->result();
    }
    public function get_permission($condition=array())
    {
        $p=$this->db->select("r.role_id,r.name as role_name,r.locked as role_locked,r.description as role_description,
                        p.permission_id,p.name as permission_name,p.locked,
                        pg.permission_group_id,pg.name as permission_group_name,pg.description permission_group_description,
                        p.object_id,obj.name as object_name,p.operation_id,
                        op._read as 'read',op._create as 'create',op._update as 'update',op._delete as 'delete'");
        $p->from('rbac_roles r')
                ->join('rbac_role_permission rp','r.role_id=rp.role_id')
                ->join('rbac_permissions p','rp.permission_id=p.permission_id')
                ->join('rbac_permissions_group pg','p.permission_group_id=pg.permission_group_id')
                ->join('rbac_operations op','p.operation_id=op.operation_id')
                ->join('rbac_objects obj','obj.object_id=p.object_id');
        if(!empty($condition))
        {
            if(array_key_exists('role_id',$condition))
            {
                $p->where('r.role_id',$condition['role_id']);
            }
            if(array_key_exists('creater_id',$condition))
            {
                $p->where('r.creater_id',$condition['creater_id']);
            }
        }
        return $p->order_by('p.permission_id','asc')->get()->result();
    }
    public function delete_by_id($permission_id=array())
    {
        if($this->frame->users()->hasPermission('permissions_management')->object('permission')->delete())
        {
            $permission_id=implode(',',$permission_id);
            /*$sql="delete from ".$this->db->dbprefix."rbac_permissions p inner join ".$this->db->dbprefix."rbac_role_permission rp on(p.permission_id=rp.permission_id)
                inner join ".$this->db->dbprefix."rbac_roles r on(r.role_id=rp.role_id) where r.locked!=? and p.permission_id in(?);";*/
            $sql="delete from ".$this->db->dbprefix.$this->table." where locked!= ? and permission_id in (?)";
            $binding=array(
                1,
                $permission_id
                );
            return $this->db->query($sql,$binding);
        } 
        return false;
    }
    public function get_permission_by_id($id)
    {
        return $this->db->select("rp.role_id,p.permission_id,
                    p.name as permission_name,
                    pg.permission_group_id,pg.name as permission_group_name,pg.description as permission_group_description,
                    p.object_id,p.operation_id,
                    obj.name as object_name,
                    op._read as 'read',op._create as 'create',op._update as 'update',op._delete as 'delete'")
                ->from('rbac_permissions p')
                ->join('rbac_permissions_group pg','pg.permission_group_id=p.permission_group_id')
                ->join('rbac_objects obj','obj.object_id=p.object_id')
                ->join('rbac_operations op','op.operation_id=p.operation_id')
                ->join('rbac_role_permission rp','rp.permission_id=p.permission_id')
                ->where('p.permission_id',$id)->get()->row();
    }
    public function check_permission_by_value($group_id,$object_id,$operation_id)
    {
        $data=array(
            'permission_group_id'=>$group_id,
            'object_id'=>$object_id,
            'operation_id'=>$operation_id
        );
        return $this->db->select('*')->from($this->table)->where($data)->get()->row();
    }
    public function delete($id=null)
    {
        if($this->frame->users()->hasPermission('permissions_management')->object('permission')->delete())
        {
            
            return $this->db->delete($this->table)->where('permission_id',$id);
        }
        return false;
    }
    public function update_permission_operation($data)
    {
        foreach($data as $item=>$oper)
        {
            $oper_id=$this->operations->get_operation_id_by_name($oper);
            if(isset($oper_id->operation_id)){
                $this->db->update($this->table,array('operation_id'=>(int)$oper_id->operation_id),array('permission_id'=>$item));
            }
            unset($oper_id);
        }return true;
    }
    public function createPermission($data=null)
    {
        if(!$this->frame->users()->checkaccess('permissions_management','permission')->create())return false;
        if($data!=null)
        {
            $this->db->insert($this->table,$data);
            return $this->db->insert_id();
        }return false;
    }
    public function updatePermission($data=null)
    {
        if(!$this->frame->users()->checkaccess('permissions_management','permission')->update())return false;
        if($data!=null)
        {
            $permission_id=$data['permission_id'];
            unset($data['permission_id']);
             $this->db->where('permission_id',$permission_id);
             $this->db->update($this->table,$data);
             $ret=$this->db->select('permission_id')->from($this->table)->where('permission_id',$permission_id)->get()->row();
             return $ret->permission_id;
        }return false;
    }
    public function is_lock($permission_id)
    {
        $ret=$this->db->select('locked')->from($this->table)->where('permission_id',$permission_id)->get()->row();
        return $ret->locked;
        
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
				return 0;
			}
		}
		$this->db->insert($this->table,$data);
	 
		return $this->db->insert_id();
	 
	}
}
?>
