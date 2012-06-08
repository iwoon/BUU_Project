<?php
class Roles_main extends CI_Controller
{
    protected $page='จัดการบทบาท';
    public function __construct()
    {
        parent::__construct();
        $this->load->library('jquery_ext');
        $this->load->model('users/rbac_users','users');
        $this->load->model('roles/rbac_roles','roles');
        $this->load->model('users/rbac_user_role','user_role');
        $this->frame->nav()->add('หน้าหลัก',$this->frame->url);
        $this->frame->nav()->add('ระบบจัดการผู้ใช้',site_url('welcome'));
        $this->load->library('form');
        
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
                'creater_id'=>$this->frame->users()->get_user_id(),
                'role_id'=>$input['role_id']
            );
            if($input['role_id']==null){
                unset($data['role_id']);
            }
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
        if($this->frame->users()->get_user_id()==0
         ||$this->frame->users()->checkaccess('roles_management','all_roles')->read())
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
    private function gen_form($role_id=null)
    {
        $this->load->library('form');
        $condition=array(
                'creater_id'=>$this->frame->users()->get_user_id(),
            );
        if($this->frame->users()->get_user_id()==0||$this->frame->users()->checkaccess('visible_all_roles','all_roles')->read())unset($condition['creater_id']);
        if($role_id!=null){
            $edit_role_data=$this->roles->get_roles($role_id);
        }
        if($this->frame->users()->get_user_id()==0
          ||$this->frame->users()->checkaccess('visible_all_roles','all_roles')->read())
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
                ->label('ชื่อบทบาท')->text('role_name|role_name','','trim|alpha_numberic|xss_clean',(isset($edit_role_data))?$edit_role_data[0]->name:'')
                ->label('ภายใต้บทบาท')->select('base_on',$select_data,'',(!is_null($role_id))?$role_id:0)
                ->label('รายระเอียดบทบาท')->br()->textarea('description|description','','trim|xss_clean',(isset($edit_role_data))?$edit_role_data[0]->description:'')->margin(90)
                ->hidden('role_id',$role_id)
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
    public function edit($role_id)
    {
        $this->frame->nav()->add('จัดการบทบาท',site_url('roles/'));
        $this->frame->nav()->add('แก้ไขบทบาท');
        $left_menu=$this->load->view('left_menu','',true);
        $this->template->content->add($left_menu);
        $this->template->content->add('<div id="admin_panel" style="float:right;width:780px;">');
        $this->template->content->add('<div id="roles_form">'.$this->gen_form($role_id).'</div>');
        $this->template->content->add('</div>');
        $this->template->publish();
    }
    public function members($role_id=null)
    {
        $page=$this->uri->uri_to_assoc(3);
        //print_r($page);
        if(array_key_exists('page',$page)){$page_id=$page['page'];}else{$page_id=1;}
        if(array_key_exists('members',$page)){
            $role_id=$page['members'];
        }
        $role_detail=$this->roles->get_roles($role_id);
        if(!$this->frame->users()->checkaccess('roles_management','assign_users')->read())redirect('roles/');
        if($role_id==null){redirect('roles/');}
        $this->frame->nav()->add($this->page,site_url('roles/'.strtolower(get_class($this))));
        $this->frame->nav()->add('รายชื่อสมาชิกบทบาท '.$role_detail[0]->name);
        $user_list=null;
        if($page_id>0){
            $rowperpage=20;
            $begin = ($page_id==1)?0:$page_id*$rowperpage;
            $condition=array(
                'role_id'=>$role_id,
                'limit'=>array('rowperpage'=>$rowperpage,'begin'=>$begin)
            );
            $data=$this->user_role->get_role_members($condition);
            if((int)$data['num_members']>0){
            $member_data=array(
                'users_list'=>$data['members'],
                'num_users'=>$data['num_members'],
                'row_per_page'=>$rowperpage,
                'role_id'=>$page['members']
                );
            $user_list=$this->load->view('roles/members',$member_data,true);
            }
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
                    jConfirm('คุณต้องการเพิ่มสมาชิกให้กับบทบาทนี้','ยืนยัน',function(r){
                        if(r==true){
                            var data = { 'role_id[]' : [".$role_id."],'user_id[]':[],'ajax':'true'};
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
        
        $this->template->content->add('<fieldset><legend>สมาชิกของบทบาท '.$role_detail[0]->name.'</legend>'.(($user_list!=null)?$user_list:'ไม่มีสามาชิก').'</fieldset>');
        $user_panel=null;
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
            if((int)$notmember['num_members']>0){
            $data['users_list']=$notmember['members'];
            $data['num_users']=$notmember['num_members'];
            $data['row_per_page']=$rowperpage;
            $user_panel=$this->load->view('users/user_list',$data,true);
            $pattern=array('/users/','/users_main/','/page/');
            $replacement=array('roles',"roles_main",'members/'.$role_id.'/not_member_page');
            $user_panel=preg_replace($pattern,$replacement,$user_panel);
            }
        }
        $this->template->content->add('<fieldset><legend>รายชื่อสมาชิกที่ยังไม่ได้อยู่ในบทบาทนี้</legend>'.(($user_panel!=null)?$user_panel:'ไม่พบรายชื่อ').'</fieldset>');
        $this->template->content->add('</div>');
        $this->jquery_ext->add_library(js_path('jquery.alerts.js'));
        $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
        $this->jquery_ext->add_css(css_path('table.css'));
        $this->template->publish();
        
    }
    public function assign_members()
    {
        if($this->frame->users()->checkaccess('roles_management','assign_users')->create())
        {
            $input=$this->input->post();
            $role_id=$input['role_id'][0];
            $user_id=$input['user_id'];
            $rolelist=$this->roles->get_child_to_parent($role_id);
            foreach($user_id as $user)
            {
                foreach($rolelist as $role)
                {
                    $this->user_role->assign_role($role,$user);
                }
            }
            if(!$input['ajax']){redirect('roles/roles_main/members/'.$role_id);}
        }else{
            $this->template->content->add('<h1>คุณไม่ได้รับอนุญาติให้จัดการบทบาทให้กับผู้ใช้</h1>');
            $this->template->publish();
        }
    }
    public function detail($role_id=null)
    {
        $this->frame->nav()->add($this->page,site_url('roles/'));
        if($role_id==null)redirect('roles/');
        
        if(!$this->frame->users()->checkaccess('roles_management','roles_detail')->read())
        {
            $this->template->content->add('คุณไม่ได้รับอนุญาติให้ดูรายละเอียดของบทบาท');
        }else{
            $role=$this->roles->get_roles($role_id);
            if(!empty($role[0]->parent_role_id)){
                $base_on=$this->roles->get_roles($role[0]->parent_role_id);
            }
            
            $owner_detail=$this->users->get_users_detail($role[0]->creater_id);
            $role_detail=$this->form->fieldset('รายละเอียดบทบาท')->html('<table border=0 cellpadding=1px>')
               ->html('<tr><td>')->label('ชื่อบทบาท')->html('</td><td>')->label($role[0]->name)->html('</td></tr>')
                ->html('<tr><td>')->label('รายละเอียด')->html('</td><td>')->label($role[0]->description)->html('</td></tr>')
                ->html('<tr><td>')->label('ภายใต้บทบาท')->html('</td><td>')->label((isset($base_on)?$base_on[0]->name:'บทบาทหลัก'))->html('</td></tr>')
                ->html('<tr><td>')->label('ผู้ดูแลบทบาท')->html('</td><td>')->label($owner_detail->firstname.' '.$owner_detail->lastname,'',array('class'=>'lbowner'))
                    ->label((($this->frame->users()->checkaccess('roles_management','owner_role')->update())?'<a href="#'.$role[0]->role_id.'" class="owner" >เปลี่ยน</a>':''))
                ->html('</td></tr>')
                ->html('<tr><td></td><td>')->html('</td></tr></table>')->get();
            
            $this->frame->nav()->add('รายละเอียดของบทบาท '.$role[0]->name);
            $left_menu=$this->load->view('left_menu','',true);
            $this->template->content->add($left_menu);
            $this->template->content->add('<div id="admin_panel" style="float:right;width:780px;">');
            $this->template->content->add('<div id="role_detail">'.$role_detail.'</div>');
            $this->template->content->add('<div id="owner_panel">'.form_open('roles/roles_main/change_owner').
                                form_input(array('name'=>'owner_name','id'=>'owner_name','size'=>40)).
                                form_hidden('role_id').
                                form_hidden('user_id').
                                form_close().'</div>');
            $this->template->content->add('</div>');
            $this->jquery_ext->add_script("
                    $('#owner_panel').dialog('destroy');
                    $('#owner_panel').dialog({
                        autoOpen:false,
                        bgiframe: false,
                        height: 150,
                        width:400,
                        modal: true,
                        overlay: {
                            backgroundColor: '#000',
                            opacity: 0.5
                        },
                        buttons:{
                              'บันทึก':function(){
                                var role_id=$('input[name=\"role_id\"]').val();
                                var user_id=$('input[name=\"user_id\"]').val();
                                var data={'role_id':role_id,'user_id':user_id};
                                    $.post('".site_url('roles/roles_main/change_owner')."', data,function(r){
                                        $(this).dialog(\"close\");
                                        window.location.href='".current_url()."';
                                    });
                              }
                        }
                    });
                    $('#owner_name').autocomplete({
			source: function(request,response) {
				$.ajax({ url: '".site_url('users/users_main/search')."',
                                    data: { user: $('#owner_name').val()},
                                    dataType: 'json',
                                    type: 'POST',
                                    success: function(data){
                                        response(data);
                                    }
                                });
                        },
			minLength: 2,
			select: function( event, ui ) {
				$('input[name=\"user_id\"]').val(ui.item.id);
                                $('.lbowner').empty().append(ui.item.label);
			}
                    });
                    $('.owner').click(function(){
                        
                        var url=$(this).attr('href');
                        var role_id=$.trim(url.substring(1,10));
                        if(role_id!='')
                        {
                            $('input[name=\"role_id\"]').val(role_id);
                            $('#owner_panel').dialog('open');
                        }
                    });
            ");
        }
        $this->jquery_ext->add_library(js_path('jquery.alerts.js'));
               $this->jquery_ext->add_library(js_path('jqueryUI/jquery-ui-1.8.21.custom.min.js'));
               $this->jquery_ext->add_library(js_path('jqueryUI/ui/jquery.ui.dialog.js'));
               $this->jquery_ext->add_library(js_path('jqueryUI/ui/jquery.ui.autocomplete.js'));
               $this->jquery_ext->add_library(js_path('jqueryUI/ui/jquery.effects.core.js'));
               $this->jquery_ext->add_css(css_path('jqueryUI/themes/ui-lightness/jquery-ui-1.8.21.custom.css'));
               $this->jquery_ext->add_css(css_path('jqueryUI/themes/base/jquery.ui.all.css'));
               $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
        $this->template->publish();
    }
    public function change_owner()
    {
        if(!$this->frame->users()->checkaccess('roles_management','owner_role')->update())redirect('roles/roles_main/');
        $input=$this->input->post();
        $this->roles->save(array('role_id'=>$input['role_id'],'creater_id'=>$input['user_id']));
        
    }
    
}
?>
