<?php
class Rbac_role_permission extends CI_Model{
    protected $table;
    public function __construct()
    {
        parent::__construct();
        $this->table=strtolower(get_class($this));
    }
    public function revoke($role_id,$permission_id)
    {
        if(!$this->frame->users()->checkaccess('permissions_management','revoke')->delete())return false;
        return $this->delete($this->table)->where(array('role_id'=>$role_id,'permission_id'=>$permission_id))->affected_rows();
    }
    public function assign($role_id,$permission_id)
    {
        $data=array(
          'role_id'=>$role_id,
            'permission_id'=>$permission_id
        );
        $is_exists=$this->db->select('role_id')->from($this->table)->where($data)->get()->num_rows();
        if($is_exists<1){
            return $this->db->insert($this->table,$data);
        }
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
