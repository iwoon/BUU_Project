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
        return $this->db->select("r.role_id,r.name as role_name,r.description as role_description,
                        p.permission_id,p.permission_group_id,p.object_id,p.operation_id,p.name as permission_name,
                        pg.permission_group_id,pg.name as permission_group_name,pg.description as permission_group_description,
                        obj.name as object_name,
                        op._read as 'read',op._create as 'create',op._update as 'update',op._delete as 'delete'")
                ->from('rbac_roles r')
                ->join('rbac_role_permission rp','r.role_id=rp.role_id')
                ->join('rbac_permissions p','rp.permission_id=p.permission_id')
                ->join('rbac_permission_group pg','pg.permission_group_id=p.permission_group_id')
                ->join('rbac_operations op','op.operation_id=p.operation_id')
                ->join('rbac_objects obj','obj.object_id=pg.object_id')
                ->order_by('r.role_id','asc')->get()->result();
    }
    public function get_permission($condition=array())
    {
        $p=$this->db->select("r.role_id,r.name as rolename,r.description as roledescription,p.permission_id,p.name as permission_name,pg.id,pg.description,pg.object_id,obj.name as object_name,pg.operation_id,op._read as 'read',op._create as 'create',op._update as 'update',op._delete as 'delete'");
        $p->from('rbac_roles r')
                ->join('rbac_role_permission rp','r.role_id=rp.role_id')
                ->join('rbac_permissions p','rp.permission_id=p.permission_id')
                ->join('rbac_permission_group pg','p.permission_group_id=pg.permission_group_id')
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
        if($this->frame->users()->hasPermission('permisions_management')->object('permission')->delete())
        {
            $permission_id=implode(',',$permission_id);
            $sql="delete from ? p inner join ? rp on(p.permission_id=rp.permission_id)
                inner join ? r on(r.role_id=rp.role_id) where r.locked!=? and p.permission_id in(?);";
            $binding=array(
                $this->db->dbprefix.'rbac_permission',
                $this->db->dbprefix.'rbac_role_permission',
                $this->db->dbprefix.'rbac_roles',
                1,
                $permission_id
                );
            return $this->db->query($sql,$binding);
            //return $this->db->delete($this->table)->where_in('permission_id',$id);
        }
        return false;
    }
    public function get_permission_by_id($id)
    {
        return $this->db->select("rp.role_id,p.permission_id,
                    p.name as permission_name,p.description as permission_description,
                    pg.permission_group_id,pg.description as permission_group_description,
                    p.object_id,p.operation_id,
                    obj.name as object_name,
                    op._read as 'read',op._create as 'create',op._update as 'update',op._delete as 'delete'")
                ->from('rbac_permission p')
                ->join('rbac_permissions_group pg','p.permission_id=pg.permission_id')
                ->join('rbac_objects obj','o.object_id=p.object_id')
                ->join('rbac_operations op','op.operation_id=p.operation_id')
                ->join('rbac_role_permission rp','rp.permission_id=p.permission_id')
                ->where('p.permission_id',$id)->get()->row();
    }
    public function delete_by_id($id=null)
    {
        if($this->frame->users()->hasPermission('permissions_management')->object('permission')->delete())
        {
            $id=implode(',',$id);
            return $this->db->query('delete from prj_'.$this->table.' where id in ('.$id.')');
            //return $this->db->delete($this->table)->where_in('id',$id)->affected_rows();
        }
        return false;
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
                $this->db->update($this->table,array('operation_id'=>(int)$oper_id->operation_id),array('id'=>$item));
            }
            unset($oper_id);
        }return true;
    }
    public function createPermission($data=null)
    {
        if(!$this->frame->users()->checkaccess('permissions_management','permision')->create())return false;
        if($data!=null)
        {
            return $this->db->insert($this->table,$data);
        }return false;
    }
    public function updatePermission($data=null)
    {
        if(!$this->frame->users()->checkaccess('permissions_management','permision')->create())return false;
        if($data!=null)
        {
            $permission_id=$data['permission_id'];
            unset($data['permission_id']);
            return $this->db->update($this->table,$data)->where('permission_id',$permission_id);
        }return false;
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
