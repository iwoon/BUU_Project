<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Profiles extends CI_Controller{
    private $_page;
    private $_link;
    public function __construct()
    {
        parent::__construct();
        $this->_page='แก้ไขข้อมูลส่วนตัว';
        $this->_link=site_url('profiles');
        
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
                ->html('<tr><td>')->label('อีเมล์')->html('</td><td>')->text('email|email','','trim|xss_clean','',array('maxlength'=>20,'size'=>50))->html('</td></tr>')
                ->html('<tr><td></td><td>')->submit()->reset()->html('</td></tr></table>')->get();   
        return $form;
    }
    public function index($id)
    {
        $this->frame->nav->reset();
        $this->frame->nav->add('หน้าหลัก',site_url('frameapp'));
        $this->frame->nav->add($this->_page,'');
        $this->load->library('jquery_ext');
        if(!$this->frame->users()->is_authen())redirect('login');
        $data['profile']=$this->_genForm();
        $this->template->content->view('users/profiles',$data);
        $this->template->publish();
    }
}
?>
