<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Frameapp extends CI_Controller{
    private $_menu=array();
    private $_page='หน้าหลัก';
    private $_link;
    public function _construct(){
        parent::__construct();
        }
    public function index(){
        if(!$this->frame->users()->is_authen())redirect('login');
        $this->frame->nav->reset();
        $this->frame->nav->add($this->_page,'');
        $this->display_menu();
        $data['app_list']=$this->generate_app_list();
        $data['menu']=$this->_menu;
        $this->template->title->set('Frame Application :: รายการโปรแกรมที่ติดตั้งในระบบ');
        $this->jquery_ext->add_css(css_path('menu.css'));
        $this->jquery_ext->add_css(css_path('button.css'));
        $this->jquery_ext->add_css(css_path('frame.css'));
        $this->template->content->view('frame',$data);
        $this->template->publish();
    }
    public function display_menu(){
        $this->create_menu();
        $this->load->library('jquery_ext');
        //$this->jquery_ext->add_jquery();
        $this->jquery_ext->add_script('$(".logout").click(function(e){
                jConfirm(\'คุณแน่ใจที่จะออกจากระบบ?\',\'ยืนยันอีกครั้ง!\',function(r){
                    if(r==true){window.location.href="'.site_url('logout/').'/";}
                });
                e.preventDefault();
                return false;
            });');
        
        $this->jquery_ext->add_library('frame/asset/js/jquery.alerts.js');
        $this->jquery_ext->add_css('/frame/asset/css/jquery.alerts.css');
    }
    private function create_menu(){
       /* if($this->frame->users->get_user_id()==0){
                    $this->_menu[]=array(
                                'label'=>'เพิ่ม/ลบ โปรแกรมเสริม',
                                'url'=>site_url('program/'),
                                'icon'=>base_url('frame/asset/images/icons/without-shadows/cpanel.png'),
                                'type'=>'buttons',
                                'action'=>'install'
                   );
        }
            $this->_menu[]=array(
                            'label'=>'จัดการข้อมูลส่วนตัว',
                            'url'=>site_url('profile/'),
                            'icon'=>  base_url('frame/asset/images/icons/with-shadows/person-profile-16.png'),
                            'type'=>'buttons',
                            'action'=>'profiles'
                        );
            $this->_menu[]=array(
                            'label'=>'ออกจากระบบ',
                            'url'=>site_url('/logout'),
                            'icon'=>base_url('frame/asset/images/icons/with-shadows/badge-circle-power-16.png'),
                            'type'=>'logout',
                            'action'=>'logout'
                );*/
        //generate <ul><li></li></ul>
    }
    public function generate_app_list(){
        $this->load->model('application');
        $user_enable_app=$this->application->get_enable_app();
        if($user_enable_app->num_rows()>0){
            $data['app_list']=$user_enable_app->result();
            return $this->load->view('program_panel',$data,true);
        }
        return '';
    }
}
?>
