<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Permissions_main extends CI_Controller
{
    protected $page='จัดการสิทธิ';
    public function __construct()
    {
        parent::__construct();
        $this->frame->nav()->add('หน้าหลัก',$this->frame->url);
        $this->frame->nav()->add('ระบบจัดการผู้ใช้',site_url('welcome'));
                $this->load->model('users/rbac_users','user');
        $this->load->library('form');
        $this->load->model('roles/rbac_roles','roles');
        $this->load->model('roles/rbac_role_permission','role_permise');
        $this->load->model('operations/rbac_operations','operation');
        $this->load->model('permissions/rbac_permissions','permise');
        $this->load->library('jquery_ext');
        $this->load->helper('form');
    }
    public function index()
    { 
        
        $this->permissions_roles();
    }
    private function permission_panel($role=null)
    {
        //$roles=$this->roles->get_parent_roles();
        $this->load->model('permissions/rbac_permission','permise');
        $data=null;
        $operation=array(
            'create'=>'เพิ่มได้',
            'read'=>'อ่านได้',
            'update'=>'แก้ไขได้',
            'delete'=>'ลบได้');
            if($role==null){
                $this->frame->nav()->add('สิทธิของบทบาททั้งหมด');
                if($this->frame->users()->get_user_id()==0){
                    $permise=$this->permise->get_all_permission();
                }else{
                    $permise=$this->permise->get_permission(array('creater_id'=>$this->frame->users()->get_user_id()));
                }
            }else{
                $rolename=$this->roles->get_roles($role);
                $this->frame->nav()->add('สิทธิบทบาท '.$rolename[0]->name);
                $condition=array('role_id'=>$role,'creater_id'=>$this->frame->users()->get_user_id());
                $permise=$this->permise->get_permission($condition);
                unset($rolename);
            }
            //creat role option for move or copy to other
            $condition=array('creater_id'=>$this->frame->users()->get_user_id());
            $owner_role=$this->roles->get_all_roles($condition);
                $option=array('none'=>'บทบาท');
                foreach($owner_role['rolelist'] as $item)
                {
                    $option[$item->role_id]=$item->name.' '.$item->description;
                }
                unset($owner_role);
             $num_role=0;
             $current_role_id=-1;
            foreach($permise as $item_role)
            {
                if($num_role>1)break;
                if($item_role->role_id!=$current_role_id){$num_role++;$current_role_id=$item_role->role_id;}
            }
            $new=0;$i=0;$end=0;

            foreach($permise as $item)
            {
                if($item->role_id!=$new){
                    if($end>0){
                        $data.='</tr></tbody></table>';
                        $data.='<input type="hidden" name="url" value="'.current_url().'"/>';
                        $data.='</form></fieldset></div>';
                    }
                    $data.='<div id="permission_panel'.$i.'"> <fieldset><legend>'.$item->role_name.'</legend><form name="permission['.$item->role_id.']" method="post" action="'.site_url('permissions/permissions_main/edit').'">';
                    $data.='<p>'.$item->role_description.'</p>';
                    $data.='<span id="toolbar">';
                    if(!$item->locked)
                    {
                        $data.='<a href="'.site_url('permissions/permissions_add/role/'.$item->role_id).'" class="add"><img src="'.image_path('icons/without-shadows/badge-circle-plus-16-ns.png').'">&nbsp;เพิ่ม</a>&nbsp';
                        $data.='&nbsp;<a href="#'.$item->role_id.'" class="delete"><img src="'.image_path('icons/without-shadows/badge-circle-minus-16-ns.png').'">&nbsp;ลบ</a>&nbsp;';
                    }
                    
                    if($num_role>1)
                    {
                        if(!$item->locked)
                            {
                                $data.='<a href="#'.$item->role_id.'" class="copy"><img src="'.image_path('copy.png').'" align="bottom" width="16px" hiegth="16px">&nbsp;คัดลอกมาที่นี้</a>
                                &nbsp;<a href="#'.$item->role_id.'" class="move"><img src="'.image_path('move.gif').'" align="bottom" width="25px" height="25px">&nbsp;ย้ายมาที่นี้</a>';
                            }
                    }
                     $data.='&nbsp;<a href="#'.$item->role_id.'" class="copyTo"><img src="'.image_path('copy.png').'" align="bottom" width="16px" hiegth="16px">&nbsp;คัดลอกไปยัง</a>';
                    if(!$item->locked)
                    {
                        $data.='&nbsp;<a href="#'.$item->role_id.'" class="moveTo"><img src="'.image_path('move.gif').'" align="bottom" width="25px" height="25px">&nbsp;ย้ายไปยัง</a></span>';
                    }
                    //$data.='<table border=0 cellpadding=1px style="font-size:12.5px;">';
                    $data.='<table> <thead><tr class="odd">
                        <td class="column1"></td>
                        <td class="column1">กลุ่มสิทธิ</td>
                        <td class="column1">วัตถุ</td>
                        <td class="column1">สิทธิ</td>';
                    foreach( $operation as $op=>$des){$data.='<th scope="col">'.$des.'&nbsp;</th>';}
                    $data.='<td class="column1">แก้ไข</td></tr></thead><tfoot><td class="column1"></td><td><input type="submit" value="save"></td></tfoot><tbody>';
                    $new=$item->role_id;
                    $end++;
                }
                $i++;
                $data.='<tr '.(($i%2)? 'class="odd"':'').'>';
                $data.='<td class="column1"><input type="checkbox" name="id'.$item->role_id.'" value="'.$item->permission_id.'"/></td>';
                $data.='<td class="column1">'.$item->permission_group_name.'</td><td class="column1">'.$item->object_name.'</td>';
                $data.='<td style="text-align:left;">'.$item->permission_name.'</td>';
                foreach($operation as $oper=>$desc)
                {
                  $data.='<td><input type="checkbox" name="permise['.$item->permission_id.'][]" value="'.$oper.'" '.(($item->{$oper}==1)?'checked':'').'/></td>';
                }
                $data.='<td>'.anchor(site_url('permissions/permissions_add/permission/'.$item->permission_id),'แก้ไข').'</td>';
                }
            if(!is_null($data)){
               $data.='</tr></tbody></table>';
               $data.='<input type="hidden" name="url" value="'.current_url().'"/>';
               $data.='</form></fieldset></div>';
            }else{$data.=anchor('permissions/permissions_add/role/'.$role,'เพิ่มสิทธิให้บทบาทนี้');}
               $this->jquery_ext->add_script("
                   $('.delete').click(function(){
                    var url=$(this).attr('href');
                    
                    jConfirm('ยืนยันการลบข้อมูล','คุณแน่ใจที่จะลบข้อมูล',function(r){
                        if(r==true){
                            var role_id=$.trim(url.substring(1,10));
                            var data = { 'id[]' : [],'role_id' : role_id };
                            $('input:checked').each(function() {
                               var name=$(this).attr('name');
                               if(name.substring(0,2)=='id'){
                                data['id[]'].push($(this).val());
                               }
                            });
                            $.post('".site_url('permissions/permissions_main/revoke')."', data,function(){
                                window.location.href='".current_url()."';
                            });
                        }
                    });
                });
                $('#copy_panel,#move_panel').dialog('destroy');
                $('#copy_panel,#move_panel').dialog({
                    autoOpen:false,
                    bgiframe: false,
                    height: 150,
                    width:400,
                    modal: true,
                    overlay: {
                        backgroundColor: '#000',
                        opacity: 0.5
                    }
                    
                });
                $('.copy').click(function() {
                       var url=$(this).attr('href');
                        var role_id=$.trim(url.substring(1,10));
                       var data = { 'id[]' : [],'to_role_id':role_id};
                            $('input:checked').each(function() {
                               var name=$(this).attr('name');
                               if(name.substring(0,2)=='id'){
                                if($.trim(name.substring(2,10))!=role_id){
                                    data['id[]'].push($(this).val());
                                }
                               }
                            });
			if(data['id[]']!='' && data['to_role_id'] !=''){ 
                            $.post('".site_url('permissions/permissions_main/permission_copy')."', data,function(){
                                window.location.href='".current_url()."';
                            });
                        } 
		}); 
                $('.move').click(function() {
                        var url=$(this).attr('href');
                        var role_id=$.trim(url.substring(1,10));
                       var data = { 'id[]' : [],'from_role_id[]':[],'to_role_id':role_id};
                            $('input:checked').each(function() {
                               var name=$(this).attr('name');
                               if(name.substring(0,2)=='id'){
                                if($.trim(name.substring(2,10))!=role_id){
                                    data['id[]'].push($(this).val());
                                    data['from_role_id[]'].push($.trim(name.substring(2,10)));
                                }
                               }
                            });
			if(data['id[]']!='' && data['from_role_id[]'] != '' && data['to_role_id'] !=''){ 
                             $.post('".site_url('permissions/permissions_main/permission_move')."', data,function(){
                                window.location.href='".current_url()."';
                            });
                       }
		});
                $('.copyTo').click(function() {
                       var url=$(this).attr('href');
                        var role_id=$.trim(url.substring(1,10));
                       var data = { 'id[]' : [],'to_role_id':role_id};
                            $('input:checked').each(function() {
                               var name=$(this).attr('name');
                               if(name.substring(0,2)=='id'){
                                data['id[]'].push($(this).val());
                               }
                            });
			if(data['id[]']!='' && data['to_role_id'] !=''){ 
                            $('input[name=\"id[]\"]').val(data['id[]']);
                            $('input[name=\"to_role_id\"]').val(data['to_role_id']);
                            $('#copy_panel').dialog('open');
                        } 
		}); 
                $('.moveTo').click(function() {
                        var url=$(this).attr('href');
                        var role_id=$.trim(url.substring(1,10));
                       var data = { 'id[]' : [],'from_role_id[]':[]};
                            $('input:checked').each(function() {
                               var name=$(this).attr('name');
                               if(name.substring(0,2)=='id'){
                                if($.trim(name.substring(2,10))==role_id){
                                    data['id[]'].push($(this).val());
                                    data['from_role_id[]'].push($.trim(name.substring(2,10)));
                                }
                               }
                            });
                        if(data['id[]']!='' && data['from_role_id[]'] != ''){ 
                            $('input[name=\"id[]\"]').val(data['id[]']);
                            $('input[name=\"from_role_id\"]').val(data['from_role_id[]']);
                            $('#move_panel').dialog('open');
                        } 
                        
		});
                ");
               $data.="<div id='copy_panel' title='คัดลอกไปยังบทบาท'>"
                            .form_open('permissions/permissions_main/permission_copy',array('id'=>'copy_form'))
                            .form_dropdown('to_role_id',$option)
                            .form_hidden('url',current_url())
                            .form_hidden('id[]')
                            .form_submit('submit','คัดลอก').form_close()."</div>";
               $data.="<div id='move_panel' title='ย้ายไปยังบทบาท'>"
                            .form_open('permissions/permissions_main/permission_move',array('id'=>'move_form'))
                            .form_dropdown('to_role_id',$option)
                            .form_hidden('url',current_url())
                            .form_hidden('from_role_id[]')
                            .form_hidden('id[]')
                            .form_submit('submit','ย้าย').form_close()."</div>";
               $this->jquery_ext->add_library(js_path('jquery.alerts.js'));
               $this->jquery_ext->add_library(js_path('jqueryUI/jquery-ui-1.8.21.custom.min.js'));
               $this->jquery_ext->add_library(js_path('jqueryUI/ui/jquery.ui.dialog.js'));
               $this->jquery_ext->add_library(js_path('jqueryUI/ui/jquery.effects.core.js'));
               $this->jquery_ext->add_css(css_path('jqueryUI/themes/ui-lightness/jquery-ui-1.8.21.custom.css'));
               $this->jquery_ext->add_css(css_path('jqueryUI/themes/base/jquery.ui.all.css'));
               $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
        return $data;
    }
    /*private function rolepanel()
    {
        $roles=$this->roles->get_parent_roles();
        $data='<table border=0 cellpadding=1px style="font-size:10px;">';
        foreach($roles as $role)    
        {
            $child=$this->roles->getTreeRoles($role->role_id);
            $row=0;
            foreach( $child as $subchild)
            {
                $data.='<tr>';
                for($i=0;$i<$row;$i++){$data.='<td></td>';}
                $data.='<td>'.$subchild->name.'</td><td>'.$subchild->description.'</td>';
                $data.='</tr>';
                $row++;
            }
        }
        $data.='</table>';
        return $data;
    }*/
    public function permissions_roles($role_id=null)
    {
        $this->frame->nav()->add($this->page,site_url('permissions/'));
        $select_data=array('ดูทั้งหมด');
        $role_id=$role_id;
        $condition=array('creater_id'=>$this->frame->users()->get_user_id());
            if($this->frame->users()->get_user_id()==0)
            {
                unset($condition['creater_id']);
            }
            $role_detail=$this->roles->get_all_roles_condition($condition);
            if(!empty($role_detail)){
                foreach($role_detail['rolelist'] as $role)
                {
                    $select_data[$role->role_id]=$role->name.' '.$role->description;
                }
                if($role_id==null){
                $form=$this->form->fieldset('เลือกบทบาท')->open('permissions/permissions_main/permissions_roles','filter|filter')
                        ->select('role_id|role_id',$select_data,'',0)->submit('เลือก')->get();
                }
                
            }
        $input=$this->input->post();
        if(!empty($input['role_id'])&&$input['role_id'][0]>0){
            $role_id=$input['role_id'][0];
        }
        //$left_menu=$this->load->view('left_menu','',true);
                    //$this->template->content->add($left_menu);
                    $this->template->content->add('<div id="admin_panel" style="float:left;width:auto;">');
                    
                    
                    //$template->content->widget('Permissions_menu');
                    if(isset($form))
                    {
                        $this->template->content->add('<div id="select_role">'.$form.'</div>');
                    }
                    
                    $this->template->content->add('<div id="roles_panel" style="float:left;">'.$this->permission_panel($role_id).'</div>');
                    $this->template->content->add('</div>');
        $this->jquery_ext->add_css(css_path('table.css'));
        $this->template->publish();
    }

    public function revoke()
    {
            if($this->frame->users()->hasPermission('permissions_management')->object('permission')->delete())
            {
                $input=$this->input->post();
                $id=$input['id'];
                $role_id=$input['role_id'];
                $this->role_permise->revoke($role_id,$id);
                if(is_array($id))
                {
                    $this->permise->delete_by_id($id);
                    //echo "<script>window.location.herf='/index.php/users';</script>";
                    //redirect('permissions/');
                }else{
               // $this->load->model('rbac_users_model','user');
                    if($this->permise->delete($id)!=false){
                        $this->jquery_ext->add_script("jAlert('success','ลบข้อมูลเรียบร้อยแล้ว')");
                        $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
                    }
                }
            }else{echo "No permission";}
    }
    public function edit()
    {
        $input=$this->input->post();
        if(!$this->frame->users()->checkaccess('permissions_management','object_operation')->update())redirect($input['url']);
        $this->permise->update_permission_operation($input['permise']);
        redirect($input['url']);
            //print_r($input);
    }
    public function add()
    {
        redirect('permissions/permissions_add/');
    }
    public function permission_copy($opt=null)
    {
        $input=$this->input->post();
        if(count($input['id'])==1)
        {
            $input['id']=explode(',',$input['id'][0]);
            
        }
        foreach($input['id'] as $permission_id)
        {
            $this->role_permise->assign($input['to_role_id'],$permission_id);
        }
        if($opt==null)redirect($input['url']);
    }
    public function permission_move($opt=null)
    {
        
        $input=$this->input->post();
        if(count($input['id'])==1)
        {
            $input['id']=explode(',',$input['id'][0]);
            
        }
        if(count($input['from_role_id'])==1)
        {
            $input['from_role_id']=explode(',',$input['from_role_id'][0]);
            
        }
        $count_permise_id=count($input['id']);
        $count_from_role_id=count($input['from_role_id']);
        if($count_permise_id==$count_from_role_id)
        {
            for($i=0;$i<$count_permise_id;$i++)
            {
                $this->role_permise->revoke($input['from_role_id'][$i],$input['id'][$i]); //revoke permision from role
                $this->role_permise->assign($input['to_role_id'],$input['id'][$i]); //assing permission to new role
            }
        }
        unset($input);
        if($opt==null)redirect($input['url']);
    }
}
?>
