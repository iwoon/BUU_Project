<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');
class Rbac_users_model extends CI_Model{
    public static $TABLE_NAME='rbac_users';
    private $_data;
    public function __construct(){
        parent::__construct();
        $this->table=self::$TABLE_NAME;
        $this->_data['username']=NULL;
        $this->_data['password']=NULL;
        $this->_data['user_id']=NULL;
    }
    public function set($properties,$value){
         $this->_data[$properties]=$value;
    }
    public function get($properties){return $this->_data[$properties];}
    /*public function save()
    {
        if($this->_get()->num_rows()>0){
            //data already exists
            $this->db->where('username',$this->_data['username'])->or_where('user_id',$this->_data['user_id'])->update(self::$TABLE_NAME,$this->_data);
        }
        $this->db->insert(self::$TABLE_NAME,$this->_data);
    }*/
    public function get_user_has_roles($user_id=null)
    {
        if($user_id==null)return false;
        return $this->db->select('u.user_id,u.firstname,u.lastname,r.role_id,r.name,r.description,r.parent_role_id')
                ->from('rbac_user_role ur')
                ->join('rbac_roles r','ur.role_id=r.role_id')
                ->join('rbac_users u','ur.user_id=u.user_id')
                ->where('ur.user_id',$user_id)->get()->result();
    }
    public function get_user_parent_roles($user_id=null)
    {
        return $this->db->select('u.user_id,u.firstname,u.lastname,r.role_id,r.name,r.description,r.parent_role_id')
                ->from('rbac_user_role ur')
                ->join('rbac_roles r','ur.role_id=r.role_id and r.parent_role_id is null')
                ->join('rbac_users u','ur.user_id=u.user_id')
                ->where('ur.user_id',$user_id)->get()->result();
    }
    public function get_users_permission($user_id=null)
    {
        if($user_id!=null)
        {
            return $this->db->select("r.role_id,r.name as rolename,r.description as roledescription,p.permission_id,p.name,po.description,po.object_id,po.operation_id,op._read as 'read',op._create as 'create',op._update as 'update',op._delete as 'delete'")
                ->from('rbac_user_role ur')->join('rbac_roles r','ur.role_id=r.role_id')
                ->join('rbac_role_permission rp','r.role_id=rp.role_id')
                ->join('rbac_permissions p','rp.permission_id=p.permission_id')
                ->join('rbac_permission_object po','po.permission_id=p.permission_id')
                ->join('rbac_operations op','op.operation_id=po.operation_id')
                ->where('ur.user_id',$user_id)->get()->result();
        }return false;
    }
    public function get_authen_type(){
      $userdata=$this->_get();
      if($userdata->num_rows()>0 && $userdata->num_rows()<2){
          return (string) $userdata->row()->authen_type;
      }
      return ' ';
    }
    private function _get()
    {
        //return $this->db->get_where(self::$TABLE_NAME,array('username'=>$this->_data['username']));
         $this->db->select('usr.*,at.*');
         $this->db->from(self::$TABLE_NAME.' usr');
         $this->db->join('authen_type at','usr.authen_id=at.authen_id');
         $this->db->where('usr.username',$this->_data['username']);
         $this->db->or_where('usr.user_id',$this->_data['user_id']);
         return $this->db->get();
    }
    public function get_users_list($condition=array())
    {
         if(array_key_exists('user_id',$condition))
            {
                $user=$this->db->select('usr.*,at.*')
                        ->from(self::$TABLE_NAME.' usr')
                        ->join('authen_type at','usr.authen_id=at.authen_id')->where_in('user_id',$users_id);
            }else{
                $user=$this->db->select('usr.* ,at.*')->from(self::$TABLE_NAME.' usr')->join('authen_type at','usr.authen_id=at.authen_id'); 
            }
         if(array_key_exists('limit',$condition))
            {
                $user->limit($condition['limit']['rowperpage'],$condition['limit']['begin']);
            }
            $query=$user->get();
            $this->_data['num_users']=$this->db->count_all(self::$TABLE_NAME)-1;
        return $query->result();
    }
    public function delete($user_id)
    {
        if($this->frame->users()->hasPermission('users_management')->object('users')->delete())
        {
            return $this->db->delete(self::$TABLE_NAME,array('user_id'=>$user_id))->affected_rows();
        }
        return false;
    }
    public function delete_by_id($id=array())
    {
        if($this->frame->users()->hasPermission('users_management')->object('users')->delete())
        {
            $id=implode(',',$id);
                $query=$this->db->query('delete from prj_'.self::$TABLE_NAME.' where user_id in ('.$id.')');
                return true;
            //return $this->db->delete(self::$TABLE_NAME)->where_in('user_id',$id)->affected_rows();
        }
        return false;
    }
    public function get_num_users()
    {
        return $this->_data['num_users'];
    }
    public function getdata($result='object')
    {
        $ret=$this->_get();
        $this->_data=$ret->row();
        if($result=='array'){
        return (($ret->num_rows()>1)?$ret->result_array():$ret->row_array());
        }return (($ret->num_rows()>1)?$ret->result():$ret->row());
    }
}

?>
