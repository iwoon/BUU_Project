<?php
class Admin_users_add_m extends CI_Model
{
        protected static $TABLE='rbac_users';
	function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------

      /** 
       * function SaveForm()
       *
       * insert form data
       * @param $form_data - array
       * @return Bool - TRUE or FALSE
       */
        
	function SaveForm($form_data)
	{
		/*$this->db->insert('rbac_users', $form_data);
		
		if ($this->db->affected_rows() == '1')
		{
			return TRUE;
		}
		
		return FALSE;
                */
            return $this->save($form_data);
        }

        public function save($data,$tablename="")
	{
		if($tablename=="")
		{
			$tablename = self::$TABLE;
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
	 
		$this->db->insert(self::$TABLE,$data);
	 
		return $this->db->affected_rows();
	 
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