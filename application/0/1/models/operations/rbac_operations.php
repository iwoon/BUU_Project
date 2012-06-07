<?php
class Rbac_operations extends CI_Model
{
    protected $table;
    public function __construct()
    {
        parent::__construct();
        $this->table=strtolower(get_class($this));
        $this->load->database();
    }
    public function get_operation_by_permise_id($permise_id)
    {
        $sql="select op.* from ".$this->db->dbprefix."rbac_permissions p inner join ".$this->db->dbprefix.$this->table." op
            on(p.operation_id=op.operation_id) where p.operation_id=?)";
        $binding=array(
            $permise_id
            );
        return $this->db->query($sql,$binding)->row();
    }
    public function get_operation_id_by_name($operation_name=array())
    {
        if(is_array($operation_name))
        {
            $operation_name=implode('_',$operation_name);   
        }
        return $this->db->select('operation_id')->from($this->table)->where('name',$operation_name)->get()->row();
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
	 
		return $this->db->insert_id();
        }
}
?>
