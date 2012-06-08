<?php

class Rbac_permissions_group extends CI_Model {
    protected $table;
	function __construct()
	{
		parent::__construct();
                $this->table=strtolower(get_class($this));
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
				$group_id=$this->db->select('permission_group_id')->from($this->table)->where('name',$data['name'])->get()->row();
                                return $group_id->permission_group_id;
			}
		}
		$this->db->insert($this->table,$data);
	 
		return $this->db->insert_id();
	 
	}
        public function get_permissions_group($condition=null)
        {
            $q=$this->db->select('*')->from($this->table);
            if($condition!=null)
            {
                if(array_key_exists('creater_id',$condition))
                {
                    $q->where('creater_id',$condition['creater_id']);
                }
            }
            return $q->get()->result();
        }
}
?>