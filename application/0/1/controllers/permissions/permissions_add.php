<?php
class Permissions_add extends CI_Controller
{
    protected $page='เพิ่มสิทธิ';
    public function __construct()
    {
        parent::__construct();
        $this->load->library('jquery_ext');
        $this->frame->nav()->add('หน้าหลัก',site_url($this->frame->url));
        $this->frame->nav()->add('ระบบจัดการผู้ใช้',site_url('welcome/'));
        $this->frame->nav()->add('จัดการสิทธิ',site_url('permissions/'));
        $this->load->model('permissions/rbac_permissions','permise');
        $this->load->model('roles/rbac_role_permission','role_permise');
        $this->load->model('roles/rbac_roles','roles');
        $this->load->model('operations/rbac_operations','operations');
        $this->load->model('objects/rbac_objects','objects');
        $this->load->model('permissions/rbac_permissions_group','permise_group');
    }
    public function index()
    {
        $p=$this->frame->users();
        if(!$p->checkaccess('permissions_management','permission')->read())redirect('permissions/');
        
        $this->permission();
    }
    public function permission($id=null)
    {
        $p=$this->frame->users();
        if(!$p->checkaccess('permissions_management','permission')->read())redirect('permissions/');
        $this->frame->nav->add((($id==null)?$this->page:'แก้ไขสิทธิ'));
        $left_menu=$this->load->view('left_menu','',true);
          $this->template->content->add($left_menu);
         $this->template->content->add('<div id="admin_panel" style="float:right;width:780px;">');
        $this->template->content->add('<div id="roles_panel" style="float:left;">'.$this->gen_form(array('id'=>$id)).'</div>');
        $this->template->content->add('</div>');
        $this->jquery_ext->add_css(css_path('table.css'));
        $this->template->publish();
    }
    private function gen_form($condition=null)
    {
        $data=array();
        $condition['creater_id']=$this->frame->users()->get_user_id();
        if($this->frame->users()->get_user_id()==0)
          {
            unset($condition['creater_id']);
          }
        if($condition!=null)
        {
            if(array_key_exists('id',$condition)&& $condition['id']!=null){
            $role_detail=$this->permise->get_permission_by_id($condition['id']);
            $data['permise']=$role_detail;
            $data['role_id']=$role_detail->role_id;
            }
            if(array_key_exists('role_id',$condition)&&$condition['role_id']!=null)
            {
            $data['role_id']=$condition['role_id'];
            }
        }
        $role_data=$this->roles->get_all_roles($condition);
        $condition=array('creater_id'=>$this->frame->users()->get_user_id());
        if($this->frame->users()->get_user_id()==0)
        {
            unset($condition['creater_id']);
        }
        $group=$this->permise_group->get_permissions_group($condition);
        $data['group_data'][]='เลือกกลุ่มสิทธิ';
        $this->jquery_ext->add_script("
                $('select[name=\"permise_group\"]').change(function(){
                    if($(this).val()!=0){
                        $('input[name=\"group_name\"]').attr('disabled','disabled');
                        $('textarea[name=\"group_description\"]').attr('disabled','disabled');
                    }else{ 
                        $('input[name=\"group_name\"]').removeAttr('disabled');
                        $('textarea[name=\"group_description\"]').removeAttr('disabled');
                    }
                });
            ");
        foreach($group as $item)
        {
            $data['group_data'][$item->permission_group_id]=$item->name.' '.$item->description;
        }
        $data['select_role']=$role_data['rolelist'];
        return $this->load->view('permissions/permission_form',$data,true);
        
    }
    public function role($role_id)
    {
        $p=$this->frame->users();
        $data=array('role_id'=>$role_id);
        if(!$p->checkaccess('permissions_management','permission')->read()){redirect('permissions/');}
        $this->frame->nav->add($this->page);
        $left_menu=$this->load->view('left_menu','',true);
          $this->template->content->add($left_menu);
         $this->template->content->add('<div id="admin_panel" style="float:right;width:780px;">');
        $this->template->content->add('<div id="roles_panel" style="float:left;">'.$this->gen_form($data).'</div>');
        $this->template->content->add('</div>');
        //$this->jquery_ext->add_library(js_path('jquery.collapes.js'));
        $this->jquery_ext->add_css(css_path('table.css'));
        $this->template->publish();
    }
    public function save()
    {
        $p=$this->frame->users();
        if($p->checkaccess('permissions_management','permission')->create_update()){
                $input=$this->input->post();
                //permission group
                $group_data=array();
                if(array_key_exists('group_name',$input))
                {
                    if(!empty($input['group_name']))
                    {
                        $group_data['name']=$input['group_name'];
                    }
                }
                if(array_key_exists('group_description',$input))
                {
                    if(!empty($input['group_description']))
                    {
                        $group_data['description']=$input['group_description'];
                    }
                }
                $group_data['creater_id']=$this->frame->users()->get_user_id();
                if($input['permise_group']!=0){
                    $group_data['permission_group_id']=$input['permise_group'];
                    
                }
                if(array_key_exists('description',$group_data)||array_key_exists('name',$group_data))
                {
                    $group_id=$this->permise_group->save($group_data);
                }else{$group_id=$input['permise_group'];}
                $object_is_exists=$this->objects->get_object_by_name(trim($input['object_name']));
                if(empty($object_is_exists))
                {
                    $object_id=$this->objects->save(array('name'=>$input['object_name']));
                }else{
                    $object_id=$object_is_exists->object_id;
                }
                $operation_id=$this->operations->get_operation_id_by_name($input['operation']);
                $operation_id=(int)$operation_id->operation_id;
                if(!array_key_exists('permission_id',$input))
                {
                    $permise_data=array(
                        'creater_id'=>$this->frame->users()->get_user_id(),
                        'permission_group_id'=>$group_id,
                        'operation_id'=>$operation_id,
                        'object_id'=>$object_id,
                        'name'=>$input['name']
                    );
                    $permise_id=$this->permise->check_permission_by_value($group_id,$object_id,$operation_id);  
                    if(empty($permise_id)){
                        $permise_id=$this->permise->createPermission($permise_data);
                    }else{$permise_id=$permise_id->permission_id;}
                }else{
                    $permise_data=array(
                        'permission_id'=>$input['permission_id'],
                        'permission_group_id'=>$group_id,
                        'operation_id'=>$operation_id,
                        'object_id'=>$object_id,
                        'name'=>$input['name']
                    );
                    $permise_id=$this->permise->updatePermission($permise_data);
                }
                if(array_key_exists('base_on',$input))
                    {
                        $input['role_id']=$input['base_on'][0];
                    }
                  
                  $this->role_permise->assign($input['role_id'],$permise_id);
                  unset($permise_id);
            }redirect('permissions/'); 
    }
}
?>
