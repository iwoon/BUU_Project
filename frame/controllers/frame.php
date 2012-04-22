<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');

class Frame extends CI_Controller{
    private $_menu=array();
    public function _construct(){
        parent::__construct();
        $this->load->library('session');
        }
    public function index(){
        $this->display_menu();
        $data['app_list']=$this->generate_app_list();
        $data['menu']=$this->_menu;
        $this->load->view('frame',$data);

    }
    public function display_menu(){
        $this->create_menu();
        $this->load->library('jquery_ext');
        $this->jquery_ext->add_jquery();
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
        if($this->session->get_user_id()==0){
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
                );
        //generate <ul><li></li></ul>
    }
    public function generate_app_list(){
        //$this->load->model('Application','app');
        //$app_list=$this->app->get_app_list();
        $apps=new stdClass;
        $apps->app_name='test1';
        $apps->app_url='#';
        $apps->app_icon='#';
        $apps2=new stdClass;
        $apps2->app_name='โปรแกรมจัดการข้อมูลนิสิต';
        $apps2->app_url='#';
        $apps2->app_icon='#';
        $app_list=array($apps,$apps2);
        $data='';
        foreach($app_list as $app){
        $data.='<div id = "cpanel">
		<div style = "float:left;">
			<div class = "icon">
				<a href = "'.$app->app_url.'">
					<img src = "'.$app->app_url.'" alt = "'.$app->app_name.'" width = "100" height = "100" border = "0" align = "middle"/>
					<span>'.$app->app_name.'</span>
				</a>
			</div>
		</div>
	</div>';
        }
        return $data;
        
    }
}
?>
