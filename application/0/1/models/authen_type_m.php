<?php
class Authen_type_m extends CI_Model
{
    protected static $TABLE='authen_type';
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function get_all_authen()
    {
        return $this->db->select('authen_id as id,authen_name')->from(self::$TABLE)->order_by('authen_name asc')->get()->result();
    }
    
}
?>
