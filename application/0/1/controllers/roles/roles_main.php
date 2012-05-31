<?php
class Roles_main extends CI_Controller
{
    protected $page='จัดการบทบาท';
    public function __construct()
    {
        parent::__construct();
        $this->load->library('jquery_ext');
        $this->load->model('Rbac_users_model','users');
        $this->load->model('roles/rbac_roles','roles');
        $this->load->model('users/rbac_user_role','user_role');
        $this->frame->nav()->add('หน้าหลัก',$this->frame->url);
        $this->frame->nav()->add('ระบบจัดการผู้ใช้',site_url('welcome'));
        
    }
    public function index($page=null)
    {
        $frame=$this->frame;
        if($frame->users()->checkaccess('roles_management','roles')->read())
        {
            $this->frame->nav()->add($this->page);
            if($page==null){$page=1;}
            $this->page($page);
            
        }else{
            $this->template->content->add('คุณไม่ได้รับอนุญาติให้ใช้งานหน้าจัดการบทบาท');
            $this->template->publish();
        }
    }
    public function add()
    {
        if($this->frame->users()->hasPermission('roles_management')->object('roles')->create())
        {
            $input=$this->input->post();
            if(empty($input))redirect('roles/');
            $parent_role_id=$input['base_on'][0];
            $data=array(
                'name'=>$input['role_name'],
                'description'=>$input['description'],
                'creater_id'=>$this->frame->users()->get_user_id()
            );
            if($input['base_on'][0]>0){$data['parent_role_id']=$parent_role_id;}
            $this->roles->save($data);
            redirect('roles/');
        }
    }
    public function page($page_id)
    {
        if(!$this->frame->users()->checkaccess('roles_management','roles')->read())redirect('welcome');
        $rowperpage=20;
        $begin = ($page_id==1)?0:$page_id*$rowperpage;
        $condition=array(
            'creater_id'=>$this->frame->users()->get_user_id(),
            'limit'=>array('rowperpage'=>$rowperpage,'begin'=>$begin)
                );
        if($this->frame->users()->checkaccess('manage_all_roles','all_roles')->read())
        {
            unset($condition['creater_id']);
        }
        $role=$this->roles->get_all_roles_condition($condition);
        $data['roles_list']=$role['rolelist'];
        $data['num_roles']=$role['num_roles'];
        $data['row_per_page']=$rowperpage;
        $role_panel=$this->load->view('roles/roles_list',$data,true);
        $left_menu=$this->load->view('left_menu','',true);
        $this->template->content->add($left_menu);
        $this->template->content->add('<div id="admin_panel" style="float:right;width:780px;">');
        $this->template->content->widget('Roles_menu');
        $this->jquery_ext->add_script("
                $('.delete').click(function(){
                    jConfirm('ยืนยันการลบข้อมูล','คุณแน่ใจที่จะลบข้อมูล',function(r){
                        if(r==true){
                            var data = { 'role_id[]' : []};
                            $('input:checked').each(function() {
                              data['role_id[]'].push($(this).val());
                            });
                            $.post('roles/roles_main/delete', data,function(r){
                                window.location.href='".site_url('roles/')."';
                            });
                        }
                    });
                });
            ");
        $this->jquery_ext->add_library(js_path('jquery.alerts.js'));
        $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
        $this->jquery_ext->add_css(css_path('table.css'));
        $this->template->content->add($role_panel);
        $this->template->content->add('<div id="roles_form">'.$this->gen_form().'</div>');
        $this->template->content->add('</div>');
        $this->jquery_ext->add_script("
                $('.add_subroles').click(function(){
                    var link_url=$(this).attr('href').split('#');
                    var role_id=link_url[1];
                    $('#role_name').focus();
                    $('#base_on').val(role_id);
                });
            ");
        $this->template->publish();
    }
    private function gen_form()
    {
        $this->load->library('form');
        $condition=array(
                'creater_id'=>$this->frame->users()->get_user_id(),
            );
        
        if($this->frame->users()->checkaccess('visible_all_roles','all_roles')->read())
        {
                unset($condition['creater_id']);
        }
        $roles_data=$this->roles->get_all_roles_condition($condition);
        $select_data=array('บทบาทหลัก');
        foreach($roles_data['rolelist'] as $role)
        {
            $select_data[$role->role_id]=$role->name.' '.$role->description;
        }
        $form=$this->form->fieldset('เพิ่มบทบาท')->open('roles/roles_main/add')
                ->label('ชื่อบทบาทใหม่')->text('role_name|role_name','','trim|alpha_numberic|xss_clean')
                ->label('ภายใต้บทบาท')->select('base_on',$select_data,0)
                ->label('รายระเอียดบทบาท')->br()->textarea('description|description','','trim|xss_clean')->margin(90)
                ->submit('เพิ่ม')->margin(90)->get();
        return $form;
    }
    public function delete()
    {
        //$this->load->model('rbac_users_model','user');
            if($this->frame->users()->hasPermission('roles_management')->object('roles')->delete())
            {
                $role_id=$this->input->post('role_id');
                if(is_array($role_id))
                {
                    $this->roles->delete_by_id($role_id);
                    //echo "<script>window.location.herf='/index.php/users';</script>";
                    redirect('roles/');
                }else{
               // $this->load->model('rbac_users_model','user');
                    if($this->roles->delete($user_id)!=false){
                        $this->jquery_ext->add_script("jAlert('success','ลบข้อมูลเรียบร้อยแล้ว')");
                        $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
                    }
                }
            }
    }
    public function members($role_id=null)
    {
        $page=$this->uri->uri_to_assoc(3);
        //print_r($page);
        if(array_key_exists('page',$page)){$page_id=$page['page'];}else{$page_id=1;}
        if(array_key_exists('members',$page)){
            $role_id=$page['members'];
        }
        if(!$this->frame->users()->checkaccess('roles_management','assign_users')->read())redirect('roles/');
        if($role_id==null){redirect('roles/');}
        $this->frame->nav()->add($this->page,site_url('roles/'.strtolower(get_class($this))));
        $this->frame->nav()->add('รายชื่อสมาชิกบทบาท');
        if($page_id>0){
            $rowperpage=20;
            $begin = ($page_id==1)?0:$page_id*$rowperpage;
            $condition=array(
                'role_id'=>$role_id,
                'limit'=>array('rowperpage'=>$rowperpage,'begin'=>$begin)
            );
            $data=$this->user_role->get_role_members($condition);

            $member_data=array(
                'users_list'=>$data['members'],
                'num_users'=>$data['num_members'],
                'row_per_page'=>$rowperpage,
                'role_id'=>$page['members']
                );
            $user_list=$this->load->view('roles/members',$member_data,true);
        }
        $left_menu=$this->load->view('left_menu','',true);
        $this->template->content->add($left_menu);
        $this->template->content->add('<div id="admin_panel" style="float:right;width:780px;">');
        $this->template->content->widget('Roles_members_menu');
        $this->jquery_ext->add_script("
                $('.delete').click(function(){
                    jConfirm('ยืนยันการลบข้อมูล','คุณแน่ใจที่จะลบข้อมูล',function(r){
                        if(r==true){
                            var data = { 'role_id[]' : [".$role_id."],'user_id[]':[]};
                            $('input:checked').each(function() {
                              data['user_id[]'].push($(this).val());
                            });
                            $.post('".site_url('users/users_main/revoke_roles')."', data,function(r){
                                window.location.href='".current_url()."';
                            });
                        }
                    });
                });
               $('.add').click(function(){
                    jConfirm('ยืนยัน','คุณต้องการเพิ่มสมาชิกให้กับบทบาทนี้',function(r){
                        if(r==true){
                            var data = { 'role_id[]' : [".$role_id."],'user_id[]':[]};
                            $('input:checked').each(function() {
                              data['user_id[]'].push($(this).val());
                            });
                            $.post('".site_url('roles/roles_main/assign_members')."', data,function(r){
                                window.location.href='".current_url()."';
                            });
                        }
                    });
               });
            ");
        $role_detail=$this->roles->get_roles($role_id);
        $this->template->content->add('<fieldset><legend>สมาชิกของบทบาท '.$role_detail[0]->name.' .</legend>'.$user_list.'</fieldset>');
        if(array_key_exists('not_member_page',$page)){$notpage=$page['not_member_page'];}else{$notpage=1;}
        if($notpage>0)
        {
            // ดึงรายชื่อสมาชิกที่ได้ไม่ได้ไม่มีบทบาททั้งหมดขึ้นมา
            $rowperpage=20;
            $begin = ($page_id==1)?0:$page_id*$rowperpage;
            $condition=array(
                'role_id'=>$role_id,
                'limit'=>array('rowperpage'=>$rowperpage,'begin'=>$begin)
                    );
            $notmember=$this->user_role->get_not_role_members($condition);
            //print_r($notmember);exit;
            $data['users_list']=$notmember['members'];
            $data['num_users']=$notmember['num_members'];
            $data['row_per_page']=$rowperpage;
            $user_panel=$this->load->view('users/user_list',$data,true);
            $pattern=array('/users/','/users_main/','/page/');
            $replacement=array('roles',"roles_main",'members/'.$role_id.'/not_member_page');
            $user_panel=preg_replace($pattern,$replacement,$user_panel);
        }
        $this->template->content->add('<fieldset><legend>รายชื่อสมาชิกที่ยังไม่ได้อยู่ในบทบาทนี้</legend>'.$user_panel.'</fieldset>');
        $this->template->content->add('</div>');
        $this->jquery_ext->add_library(js_path('jquery.alerts.js'));
        $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
        $this->jquery_ext->add_css(css_path('table.css'));
        $this->template->publish();
        
    }
    public function assign_members()
    {
        if($this->frame->users()->checkaccess('roles_management','assignment')->read())
        {
            $input=$this->input->post();
            $role_id=$input['role_id'][0];
            $user_id=$input['user_id'];
            foreach($user_id as $user)
            {
                $rolelist=$this->roles->get_child_to_parent($role_id);
                foreach($rolelist as $role)
                {
                    $this->user_role->assign_role($role,$user);
                }
            }
            redirect('roles/roles_main/members/'.$role_id);
        }else{
            $this->template->content->add('<h1>คุณไม่ได้รับอนุญาติให้จัดการบทบาทให้กับผู้ใช้</h1>');
            $this->template->publish();
        }
    }
    
}
?>
