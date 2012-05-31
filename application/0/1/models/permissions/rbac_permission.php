<?php
class Rbac_permission extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_all_permission()
    {
        return $this->db->select("r.role_id,r.name as rolename,r.description as roledescription,p.permission_id,p.name,po.description,po.object_id,po.operation_id,op._read as 'read',op._create as 'create',op._update as 'update',op._delete as 'delete'")
                ->from('rbac_roles r')
                ->join('rbac_role_permission rp','r.role_id=rp.role_id')
                ->join('rbac_permissions p','rp.permission_id=p.permission_id')
                ->join('rbac_permission_object po','po.permission_id=p.permission_id')
                ->join('rbac_operations op','op.operation_id=po.operation_id')->get()->result();
    }
    public function get_permission($condition=array())
    {
        $p=$this->db->select("p.permission_id,p.name,po.description,po.object_id,po.operation_id,op._read as 'read',op._create as 'create',op._update as 'update',op._delete as 'delete'");
        $p->from('rbac_roles r')
                ->join('rbac_role_permission rp','r.role_id=rp.role_id')
                ->join('rbac_permissions p','rp.permission_id=p.permission_id')
                ->join('rbac_permission_object po','p.permission_id=po.permission_id')
                ->join('rbac_operations op','po.operation_id=op.operation_id');
        if(!empty($condition))
        {
            if(array_key_exists('role_id',$condition))
            {
                $p->where('r.role_id',$condition['role_id']);
            }
            if(array_key_exists('user_id',$condition))
            {
                //
            }
        }
        return $p->get()->result();
    }
}
?>
