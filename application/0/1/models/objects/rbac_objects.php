<?php
class Rbac_objects extends CI_Model
{
    protected $table;
    public function __construct()
    {
        parent::__construct();
        $this->table=strtolower(get_class($this));
        $this->load->database();
    }
    public function get_object_by_name($object_name)
    {
        return $this->db->select('*')->from($this->table)->where('name',$object_name)->get()->row();
    }
    public function get_object_by_id($object_id)
    {
        return $this->db->select('object_id')->from($this->table)->where('object_id',$object_id)->get()->row();
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
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
