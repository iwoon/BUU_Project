<?php
class Users_main extends CI_Controller
{
    protected $page='จัดการผู้ใช้';
    public function __construct()
    {
        parent::__construct();
        $this->load->library('jquery_ext');
        $this->frame->nav()->add('หน้าหลัก',$this->frame->url);
        $this->frame->nav()->add('ระบบจัดการผู้ใช้',site_url());
        $this->load->model('users/rbac_users','users');
        $this->load->model('roles/rbac_roles','roles');
        $this->load->model('users/rbac_user_role','user_role');
    }
    public function index($page=null)
    {
        if($page==null){$page_id=1;}
        $this->page($page_id);
    }
    public function page($page_id)
    {
        if(!$this->frame->users()->checkaccess('users_management','users')->read())redirect('welcome');
         $this->frame->nav()->add($this->page);
        $rowperpage=20;
        $begin = ($page_id==1)?0:$page_id*$rowperpage;
        $condition=array(
            'creater_id'=>$this->frame->users()->get_user_id(),
            'limit'=>array('rowperpage'=>$rowperpage,'begin'=>$begin)
                );
        if($this->frame->users()->get_user_id()==0
        ||$this->frame->users()->checkaccess('users_management','all_users')->read())
        {
            unset($condition['creater_id']);
        }
        $data['users_list']=$this->users->get_users_list($condition);
        $data['num_users']=$this->users->get_num_users();
        $data['row_per_page']=$rowperpage;
        $user_panel=$this->load->view('users/user_list',$data,true);
        $this->jquery_ext->add_script("
                $('.delete').click(function(){
                    jConfirm('ยืนยันการลบข้อมูล','คุณแน่ใจที่จะลบข้อมูล',function(r){
                        if(r==true){
                            var data = { 'user_id[]' : []};
                            $('input:checked').each(function() {
                              data['user_id[]'].push($(this).val());
                            });
                            $.post('users/users_main/delete', data,function(r){
                                window.location.href='".site_url('users/')."';
                            });
                        }
                    });
                });
            ");
        $left_menu=$this->load->view('left_menu','',true);
        $this->template->content->add($left_menu);
        $this->template->content->add('<div id="admin_panel" style="float:right;width:780px;">');
        $this->template->content->widget('Users_menu');
        $this->template->content->add($user_panel);
        $this->template->content->add('</div>');
        $this->jquery_ext->add_css(css_path('table.css'));
           $this->jquery_ext->add_css(css_path('button.css'));
           $this->jquery_ext->add_library(js_path('jquery.alerts.js'));
           $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
           $this->template->publish();
    }
    public function add($opt='view')
    {
        $this->frame->nav()->add($this->page,site_url('users'));
        $this->frame->nav()->add('เพิ่มผู้ใช้');
        switch($opt)
        {
            case 'submit':
                
                if($this->frame->users()->hasPermission('users_management')->object('users')->create())
                {
                    $user=$this->input->post('username');
                    if(empty($user)){redirect('users');}
                    $authen=$this->input->post('authen_type');
                    $avatarpath='./asset/images/profiles/';
                    $user_id=$this->frame->users()->get_user_id();
                    $input=$this->input->post('avatar');
                    if(!empty($input['avatar']))
                    {
                        $config=array(
                            'upload_path'=>$avatarpath,
                            'file_name'=>$user_id,
                            'allowed_types'=>'jpeg|jpg|gif|png',
                            'max_size'=>250,
                            'max_width'=>250,
                            'max_height'=>250
                        );
                        $this->load->library('upload',$config);
                        $avatar_img='';
                        if($this->upload->do_upload('avatar'))
                        {
                           // echo json_encode(array('error'=>$this->upload->display_errors()));
                           // exit;
                            $avatar=$this->upload->data();
                            $avatar_img=base_url($avatarpath.$user_id.$avatar['file_ext']);
                        }
                    }
                    $form_data=array(
                        'firstname'=>$this->input->post('firstname'),
                        'lastname'=>$this->input->post('lastname'),
                        'username'=>$this->input->post('username'),
                        'password'=>$this->input->post('password'),
                        'email'=>$this->input->post('email')
                        //'authen_id'=> $authen[0]
                    );
                    if(!empty($avatar_img)){$form_data['avatar']=$avatar_img;}
                    if($this->users->save($form_data))
                    {
                        /*$this->jquery_ext->add_script("
                                jConfirm('เพิ่มผู้ใช้รายใหม่','คุณต้องการเพิ่มผู้ใช้อีกหรือไม่?',function(r){
                                    if(r==true){
                                        window.location.href='".site_url('users/users_main/add')."';
                                    }else{
                                    window.location.href='".site_url('users/')."';}
                                });
                            "); */
                        redirect('users/');
                    }
                }else{$this->template->content->add('<h1>คุณไม่ได้รับอนุญาติให้เพิ่มรายชื่อผู้ใช้ใหม่เข้าสู่ระบบ</h1>');}
                break;
            default:
                if($this->frame->users()->hasPermission('users_management')->object('users')->create())
                {
                    $users_form=$this->load->view('users/users_new','',true);
                    $left_menu=$this->load->view('left_menu','',true);
                    $this->template->content->add($left_menu);
                    $this->template->content->add('<div id="admin_panel" style="float:right;width:780px;">');
                    $this->template->content->widget('Users_menu');
                    $this->template->content->add('<div id="users_form" style="float:left;padding 1em 1em;">'.$this->gen_form().'</div>');
                    $this->template->content->add('</div>');
                    
                }else{
                    $this->template->content->add('<h1><font color="read">คุณไม่ได้รับอนุญาติให้เพิ่มผู้ใช้</font></h1>');
                    
                }
        }
        //$this->jquery_ext->add_css(css_path('table.css'));
           $this->jquery_ext->add_css(css_path('button.css'));
           $this->jquery_ext->add_library(js_path('jquery.alerts.js'));
           $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
           $this->template->publish();
    }
    public function delete($user_id=null)
    {
        //$this->load->model('rbac_users_model','user');
            if($this->frame->users()->hasPermission('users_management')->object('users')->delete())
            {
                $user_id=$this->input->post('user_id');
                if(is_array($user_id))
                {
                    $this->users->delete_by_id($user_id);
                    //echo "<script>window.location.herf='/index.php/users';</script>";
                    redirect('users/');
                }else{
               // $this->load->model('rbac_users_model','user');
                    if($this->users->delete($user_id)!=false){
                        $this->jquery_ext->add_script("jAlert('success','ลบข้อมูลเรียบร้อยแล้ว')");
                        $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
                    }
                }
            }
    }
    public function update($user_id)
    {
        if($this->frame->users()->hasPermission('users_management')->object('users')->update())
        {
            $input=$this->input->post();
            $avatarpath='./asset/images/profiles/';
            $config=array(
                'upload_path'=>$avatarpath,
                'file_name'=>$user_id,
                'allowed_types'=>'jpeg|jpg|gif|png',
                'max_size'=>250,
                'max_width'=>250,
                'max_height'=>250
            );
            $this->load->library('upload',$config);
            $avatar_img='';
            if($this->upload->do_upload('avatar'))
            {
               // echo json_encode(array('error'=>$this->upload->display_errors()));
               // exit;
                $avatar=$this->upload->data();
                $avatar_img=base_url($avatarpath.$user_id.$avatar['file_ext']);
            }
                
                
                $data=array(
                    'user_id'=>$user_id,
                    'firstname'=>$input['firstname'],
                    'lastname'=>$input['lastname'],
                    'username'=>$input['username'],
                    'email'=>$input['email'],
                    'avatar'=>$avatar_img
                    //'authen_id'=>$input['authen_type'][0]
                    );
                if(!empty($input['password']))
                {
                    $data['password']=$input['password'];
                }
                if($this->users->save($data))
                {
                    redirect('users');
                    //echo json_encode(array('msgtitle'=>'ผลการบันทึก','msg'=>'บันทึกข้อมูลเรียบร้อย','redirect'=>''));exit;
                }else{
                    $this->template->content->add('ไม่สามารถบันทึกข้อมูลได้');
                    
                    //echo json_encode(array('msgtitle'=>'ผลการบันทึก','msg'=>'ผิดพลาด','redirect'=>''));exit;
                }
                
            }else{$this->template->content->add('คุณไม่ได้รับอนุญาติให้ปรับเปลี่ยนข้อมูลผู้ใช้');
                //$this->template->publish();
            }
        $this->jquery_ext->add_css(css_path('table.css'));
           $this->jquery_ext->add_css(css_path('button.css'));
           $this->jquery_ext->add_library(js_path('jquery.alerts.js'));
           $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
           $this->template->publish();
    }
    public function profiles($user_id)
    {
        $this->frame->nav()->add($this->page,site_url('users/'));
        $this->frame->nav()->add('แก้ไขข้อมูลผู้ใช้');
        if($this->frame->users()->hasPermission('users_management')->object('users')->create())
                {
                    $left_menu=$this->load->view('left_menu','',true);
                    $this->template->content->add($left_menu);
                    $this->template->content->add('<div id="admin_panel" style="float:right;width:780px;">');
                    $this->template->content->widget('Users_menu');
                    $this->template->content->add('<div id="users_form" style="float:left;padding 1em 1em;">'.$this->gen_profile($user_id).'</div>');
                    $this->template->content->add('</div>');
                }else{
                    $this->template->content->add('<h1><font color="read">คุณไม่ได้รับอนุญาติให้เพิ่มผู้ใช้</font></h1>');
                    
                }
        $this->jquery_ext->add_script("
            $('#profiles').submit(function (e){
                var url=$(this).attr('action');
                var data = {data[]:''};
                $('input').each(function() {
                   data['data[$(this).attr('name')]'].push($(this).val());
                });
                
                e.preventDefault();
                return false;
            });
            ");
        //$this->jquery_ext->add_css(css_path('table.css'));
           $this->jquery_ext->add_css(css_path('button.css'));
           $this->jquery_ext->add_library(js_path('jquery.alerts.js'));
           $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
           $this->template->publish();
                
    }
    private function gen_profile($user_id=NULL)
    {
        $this->load->library('form');
        if($user_id!=NULL){
            $this->users->set('user_id',$user_id);
            $profile=$this->users->getdata();
        }
        $form_value=array(
            'firstname'=>(!empty($profile))?$profile->firstname:'',
            'lastname'=>(!empty($profile))?$profile->lastname:'',
            'username'=>(!empty($profile))?$profile->username:'',
            'email'=>(!empty($profile))?$profile->email:''
            //'authen_type'=>(!empty($profile))?$profile->authen_id:'',
        );
         $this->load->model('authen_type_m','authen');
        $authen_type=array();
        $authen_data=$this->authen->get_all_authen();
        
        $this->jquery_ext->add_script("
            $('#authen_type').change(function () {
               if($(this).val()!=2){ $('#server').attr('disabled',true);$('#port').attr('disabled',true);}
               else{ $('#server').removeAttr('disabled');$('#port').removeAttr('disabled');}
            }).trigger('change');
            ");
        foreach( $authen_data as $row)
        {
            $authen_type[$row->id]=$row->authen_name;
        }
        $form=$this->form->open(site_url('users/users_main/update/'.$user_id),'profiles|profiles')->html('<table border=0 cellpadding=1px>')
               ->html('<tr><td>')->label('ชื่อ')->html('</td><td>')->text('firstname|firstname','','trim|alpha_numberic|xss_clean',$form_value['firstname'])->html('</td></tr>')
                ->html('<tr><td>')->label('นามสกุล')->html('</td><td>')->text('lastname|lastname','','trim|alpha_numberic|xss_clean',$form_value['lastname'])->html('</td></tr>')
                ->html('<tr><td>')->label('ชื่อผู้ใช้')->html('</td><td>')->text('username|username','','trim|alpha_numberic|xss_clean',$form_value['username'])->html('</td></tr>')
                ->html('<tr><td>')->label('รหัสผ่าน')->html('</td><td>')->pass('password|password','','trim|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('อีเมล์')->html('</td><td>')->text('email|email','','trim|xss_clean',$form_value['email'],array('maxlength'=>60,'size'=>30))->html('</td></tr>')
                //->html('<tr><td>')->label('พิสูจน์ตัวบุคคล')->html('</td><td></td></tr>')
                //->html('<tr><td>')->label('ผ่าน')->html('</td><td>')->select('authen_type|authen_type',$authen_type,'',$form_value['authen_type'])->html('</td></tr>')
                ->html('<tr><td>')->label('รูปโปรไฟล์')->html('</td><td>')->iupload('avatar')->html('</td></tr>')
                ->html('<tr><td></td><td><img src="'.$profile->avatar.'"/>')
                ->html('<tr><td></td><td>')->submit('บันทึก')->reset('รีเซ็ต')->html('</td></tr></table>')->get();   
        return $form;
    }
    private function gen_form()
    {
        
        $this->load->library('form');
        $this->load->model('authen_type_m','authen');
        $authen_type=array();
        $authen_data=$this->authen->get_all_authen();
        
        $this->jquery_ext->add_script("
            $('#authen_type').change(function () {
               if($(this).val()!=2){ $('#server').attr('disabled',true);$('#port').attr('disabled',true);}
               else{ $('#server').removeAttr('disabled');$('#port').removeAttr('disabled');}
            }).trigger('change');
            $('#auto').click(function(){
                if($(this).is(':checked')){
                       var marker = $('<span />').insertBefore('#password');
                        $('#password').detach().attr('type', 'text').insertAfter(marker).focus();
                        marker.remove();
                        $('#password').val(gen_password(8,false));
                    }else{
                        var marker = $('<span />').insertBefore('#password');
                        $('#password').detach().attr('type', 'password').insertAfter(marker).focus();
                        marker.remove();
                        $('#password').val('');
                    }
                });
              function gen_password(length, special) {
                var iteration = 0;
                var password = '';
                var randomNumber;
                if(special == undefined){
                    var special = false;
                }
                while(iteration < length){
                    randomNumber = (Math.floor((Math.random() * 100)) % 94) + 33;
                    if(!special){
                        if ((randomNumber >=33) && (randomNumber <=47)) { continue; }
                        if ((randomNumber >=58) && (randomNumber <=64)) { continue; }
                        if ((randomNumber >=91) && (randomNumber <=96)) { continue; }
                        if ((randomNumber >=123) && (randomNumber <=126)) { continue; }
                    }
                    iteration++;
                    password += String.fromCharCode(randomNumber);
                }
                return password;
            }
            ");
        foreach( $authen_data as $row)
        {
            $authen_type[$row->id]=$row->authen_name;
        }
        $form=$this->form->open(site_url('users/users_main/add/submit'),'users_add|users_add')->html('<table border=0 cellpadding=1px>')
               ->html('<tr><td>')->label('ชื่อ')->html('</td><td>')->text('firstname|firstname','','trim|alpha_numberic|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('นามสกุล')->html('</td><td>')->text('lastname|lastname','','trim|alpha_numberic|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('ชื่อผู้ใช้')->html('</td><td>')->text('username|username','','trim|alpha_numberic|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('รหัสผ่าน')->html('</td><td>')->pass('password|password','','trim|xss_clean')->checkbox('auto|auto','true','สร้างอัตโนมัติ',false,'trim|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('อีเมล์')->html('</td><td>')->text('email|email','','trim|xss_clean','',array('maxlength'=>60,'size'=>30))->html('</td></tr>')
                ->html('<tr><td>')->label('รูปประจำตัว')->html('</td><td>')->iupload('avatar')->html('</td></tr>')
                //->html('<tr><td>')->label('พิสูจน์ตัวบุคคล')->html('</td><td></td></tr>')
                //->html('<tr><td>')->label('ผ่าน')->html('</td><td>')->select('authen_type|authen_type',$authen_type,'',1)->html('</td></tr>')
                /*->html('<tr><td>')->label('เซิฟเวอร์')->html('</td><td>')->text('server|server','','trim|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('พอร์ต')->html('</td><td>')->text('port|port','','trim|xss_clean','',array('maxlength'=>4,'size'=>4))->html('</td></tr>')
                */->html('<tr><td></td><td>')->submit('บันทึก')->reset('รีเซ็ต')->html('</td></tr></table>')->get();   
        return $form;
    }
    public function role($user_id=null)
    {
        $this->frame->nav()->add($this->page,site_url('users/'));
        $this->frame->nav()->add('บทบาทผู้ใช้');
        if(($user_id==null)){redirect('users/');}
        $user_role=$this->users->get_user_has_roles($user_id);
        $data='<fieldset><legend>บทบาทของ[ '.((!empty($user_role))?($user_role[0]->firstname.' '.$user_role[0]->lastname):'').' ] </legend><table>';
        $data.='<thead>
            <tr class="odd">
                    <td class="column1"></td>
                    <th scope="col" abbr="Home">ชื่อบทบาท</th>
                    <th scope="col" abbr="Home Plus">รายละเอียด</th>
                    <td class="column1">สิทธิ</td>
            </tr>	
            </thead>
            <tbody>';
        $i=0;
        foreach($user_role as $role)
        {
            $data.='<tr'.(($i%2)? 'class="odd"':'').'><th scope="row" class="column1"><input type="checkbox" name="role_id[]" value="'.$role->role_id.'"/></th>';
            $data.='<td>'.$role->name.'</td><td>'.$role->description.'</td><td>'.anchor('permissions/permissions_main/permissions_roles/'.$role->role_id,'สิทธิ').'</td></tr>';
            $i++;
        }
        $data.="</tbody></table></fieldset>";
        $this->load->library('form');
        $role_data=array('0'=>'กรุณาเลือก');
        $roles=$this->roles->get_not_assigned_roles($user_id);
        foreach($roles as $role)
        {
            $role_data[$role->role_id]=$role->name.' : '.$role->description;
        }
        $form_roles=$this->form->fieldset('เพิ่มบทบาทให้กับผู้ใช้')->open('users/users_main/assign_roles')->select('role_id',$role_data,'เลือกบทบาท')
                ->hidden('user_id',$user_id)->submit('เพิ่มบทบาท')->get();
        $users_form=$this->load->view('users/users_new','',true);
                    $left_menu=$this->load->view('left_menu','',true);
                    $this->template->content->add($left_menu);
                    $this->template->content->add('<div id="admin_panel" style="float:right;width:780px;">');
                    $this->template->content->widget('Users_role_menu');
                    if(!empty($user_role)){
                        $this->template->content->add('<div id="users_role" style="float:left;padding 1em 1em;">'.$data.'</div>');
                    }
                    if($this->frame->users()->hasPermission('roles_management')->object('assign_users')->create()){
                        $this->template->content->add('<div id="form_assign_role">'.$form_roles.'</div>');
                    }
                    $this->template->content->add('</div>');
           $this->jquery_ext->add_script("
                $('.delete').click(function(){
                    jConfirm('ยืนยันการลบข้อมูล','คุณแน่ใจที่จะลบข้อมูล',function(r){
                        if(r==true){
                            var data = { 'role_id[]' : [],'user_id[]':[".$user_id."]};
                            $('input:checked').each(function() {
                              data['role_id[]'].push($(this).val());
                            });
                            $.post('".site_url('users/users_main/revoke_roles')."', data,function(){
                                window.location.href='".site_url('users/users_main/role/')."';
                            });
                        }
                    });
                });
            ");
           $this->jquery_ext->add_css(css_path('table.css'));
           $this->jquery_ext->add_css(css_path('button.css'));
           $this->jquery_ext->add_library(js_path('jquery.alerts.js'));
           $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
           $this->template->publish();
           
    }
    public function assign_roles()
    {
        if($this->frame->users()->checkaccess('roles_management','assign_users')->create())
        {
            $input=$this->input->post();
            $role_id=$input['role_id'][0];
            $user_id=$input['user_id'];
            $rolelist=$this->roles->get_child_to_parent($role_id);
            foreach($rolelist as $role)
            {
                $this->user_role->assign_role($role,$user_id);
            }
            redirect('users/users_main/role/'.$user_id);
        }else{
            $this->template->content->add('<h1>คุณไม่ได้รับอนุญาติให้จัดการบทบาทให้กับผู้ใช้</h1>');
            $this->template->publish();
        }
    }
    
    public function revoke_roles()
    {
        if($this->frame->users()->checkaccess('roles_management','revoke')->read()){
            $input=$this->input->post();
            foreach($input['user_id'] as $user_id){
                foreach($input['role_id'] as $role_id)
                {
                    $rolelist=$this->roles->getTreeRoles($role_id);
                    foreach($rolelist as $role)
                    {
                        $this->user_role->revoke_role($role->role_id,$user_id);
                     }
                }
            }
        }
        //redirect('users/users_main/role');
    }
    
}
?>
