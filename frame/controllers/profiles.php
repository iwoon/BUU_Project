<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Profiles extends CI_Controller{
    private $_page;
    private $_link;
    public function __construct()
    {
        parent::__construct();
        $this->_page='แก้ไขข้อมูลส่วนตัว';
        $this->_link=base_url('profiles');
        $this->frame->nav()->add('หน้าหลัก',site_url('frameapp'));
        $this->frame->nav()->add($this->_page);
        $this->load->library('jquery_ext');
        if(!$this->frame->users->is_authen())redirect('login');
    }
    private function _genForm(){
        $this->load->library('form');
        $this->load->model('rbac_users_model','users');
        $this->users->set('user_id',$this->frame->users()->get_user_id());
        $userdata=$this->users->getdata();
        $form=$this->form->open(site_url('profiles/edit'),'profiles|profiles')->html('<table border=0 cellpadding=1px>')
               ->html('<tr><td>')->label('ชื่อ')->html('</td><td>')->text('firstname|firstname','','trim|alpha_numberic|xss_clean',$userdata->firstname)->html('</td></tr>')
                ->html('<tr><td>')->label('นามสกุล')->html('</td><td>')->text('lastname|lastname','','trim|alpha_numberic|xss_clean',$userdata->lastname)->html('</td></tr>')
                ->html('<tr><td>')->label('รหัสผ่าน')->html('</td><td></td></tr>')
                ->html('<tr><td>')->label('รหัสผ่านเดิม')->html('</td><td>')->pass('current_password|current_password','','trim|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('รหัสผ่านใหม่')->html('</td><td>')->pass('new_password|new_password','','trim|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('ยืนยันรหัสผ่านใหม่')->html('</td><td>')->pass('verify_new_password|verify_new_password','','trim|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('อีเมล์')->html('</td><td>')->text('email|email','','trim|xss_clean',$userdata->email,array('maxlength'=>20,'size'=>50))->html('</td></tr>')
                ->html('<tr><td>')->label('รูปประจำตัว')->html('</td><td>')->iupload('profile_picture')->html('</td></tr>')
                ->html('<tr><td></td><td><img src="'.$userdata->avatar.'"/>')
                ->html('<tr><td></td><td>')->submit()->reset()->html('</td></tr></table>')->get();   
        return $form;
    }
    public function index($id)
    {
        $this->frame->nav->reset();
        $this->frame->nav->add('หน้าหลัก',site_url('frameapp'));
        $this->frame->nav->add($this->_page);
        if(!$this->frame->users()->is_authen())redirect('login');
        $data['profile']=$this->_genForm();
        $this->jquery_ext->add_script("
                $('#email').keyup(function(){
                    var new_pass=$('#new_password').val();
                    var verify_new_pass=$('#verify_new_password').val();
                    if(new_pass!==verify_new_pass){
                        jAlert('info','รหัสผ่านไม่ตรงกันกรุณาตรวจสอบอีกครั้ง');
                        $('#verify_new_password').empty();
                        $('#verify_new_password').focus();
                    }
                });
            ");
        $this->jquery_ext->add_library(js_path('jquery.alerts.js'));
        $this->jquery_ext->add_css(css_path('jquery.alerts.css'));
        $this->template->content->view('users/profiles',$data);
        $this->template->publish();
    }
    public function edit()
    {
       $this->load->library('jquery_ext');
         if($this->frame->users()->hasPermission('profiles')->object('profile')->update())
        {
             $this->load->model('rbac_users_model','users');
             
            $input=$this->input->post();
            $user_id=$this->frame->users()->get_user_id();
             $password_valid=$this->users->checkauthen($user_id,$input['current_password']);
             if($password_valid<1)
             {
                 $this->template->content->add('<h1>รหัสผ่านเดิมไม่ถูกต้อง</h1>');
                 
             }else{
            $avatarpath='./asset/images/profiles/';
            $config=array(
                'upload_path'=>$avatarpath,
                'file_name'=>$user_id,
                'allowed_types'=>'jpeg|jpg|gif|png',
                'max_size'=>100,
                'max_width'=>150,
                'max_height'=>150
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
                    );
                if(!empty($input['verify_new_password']))$data['password']=$input['verify_new_password'];
                if(!empty($input['firstname']))$data['firstname']=$input['firstname'];
                if(!empty($input['lastname']))$data['lastname']=$input['lastname'];
                if(!empty($input['email']))$data['email']=$input['email'];
                if(!empty($avatar_img))$data['avatar']=$avatar_img;

                if($this->users->save($data))
                {
                    redirect('profiles/'.$this->frame->users()->get_user_id());
                    //echo json_encode(array('msgtitle'=>'ผลการบันทึก','msg'=>'บันทึกข้อมูลเรียบร้อย','redirect'=>''));exit;
                }else{
                    $this->template->content->add('ไม่สามารถบันทึกข้อมูลได้');
                    
                    //echo json_encode(array('msgtitle'=>'ผลการบันทึก','msg'=>'ผิดพลาด','redirect'=>''));exit;
                }
             }  
            }else{$this->template->content->add('คุณไม่ได้รับอนุญาติให้ปรับเปลี่ยนข้อมูลผู้ใช้');}
            $this->template->publish();
    }
}
?>
