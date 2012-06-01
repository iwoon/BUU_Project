<?php if(!defined('BASEPATH')) exit("No direct access script allowed");
	class Login extends CI_Controller{
            
		public function __contruct(){
			parent::__construct();
                        if($this->frame->users()->is_authen())
                        {
                            $this->app()->set_app_id($this->config->item('app_id'));
                            $this->app()->initialize();
                            redirect('frameapp');
                        }
		}
                public function gen_login_form(){
                        $this->load->library('form');
                        return $this->form->open('login/CheckAuth','login|login')->html('<table border=0 cellpadding=3px>')
                                    ->html('<tr><td>')->label('Username')->html('</td><td>')->text('username|username','','trim|alpha_numberic|xss_clean')->html('</td></tr>')
                                    ->html('<tr><td>')->label('Password')->html('</td><td>')->pass('password|password','','trim|xss_clean')->html('</td></tr>')
                                    ->html('<tr><td></td><td>')->submit()->reset()->html('</td></tr></table>')->get();
                }
                /*private function _authen_type(){
                    $this->load->model('Rbac_users_model','users');
                        $this->users->set('username',$this->input->post('username'));
                        $user='';
                        switch($this->users->get_authen_type())
                        {
                            case 'internal':
                                    $this->load->model('Login_Model','login',true);
                                    $authen_params=array('username'=>$this->input->post('username'),
                            'password'=>(strtolower($this->input->post('username'))=='guest')?'':md5($this->input->post('password'))
                            );
                                    $user=$this->login->CheckAuth($authen_params);
                                    
                                break;
                            case 'Ldap':
                                    $this->load->library('Authen_Ldap',array('username'=>$this->input->post('username'),'password'=>$this->input->post('password')));
                                    $user=($this->authen_ldap->login())?$this->users->getdata():'';
                                break;
                        }
			return $user;
                }*/
		public function index()
                {
                        $this->load->library('session');
                        $this->load->library('jquery_ext');
                        $this->jquery_ext->add_library('frame/asset/js/jquery.alerts.js');
                        $this->jquery_ext->add_css('/frame/asset/css/jquery.alerts.css');
                        $jquery_script="
                        $('#login').submit(function(e){
                            var form=$(this);
                            var user=form.find('input[name=\"username\"]').val();
                            var pass=form.find('input[name=\"password\"]').val();
                            var url=form.attr('action');
				var data='username='+user+'&password='+pass;
				$.ajax({
					type:'POST',
					url: url+'/json',//'index.php/login/CheckAuth/json',
					data:data,
					success:function(data){
                                              if(data.msgtitle!=null){
                                                jAlert('error',data.msg,data.msgtitle);
                                                 $('#username').val('');
                                                 $('#password').val('');
                                                 $('#username').focus();  
                                              }
                                                if(data.redirect!=null){window.location.href=data.redirect;}
					},
					dataType:'json'
				});
                            e.preventDefault();
                            return false;
			});
                        ";/*$('#close_message').click(function()
                        {
                          $('#status_bar').animate({ top=\"+=15px\",opacity:0 }, \"fast\");
                          $('#status_bar').remove();
                        });
                        $(window).scroll(function(){"."$('#status_bar').animate({top:$(window).scrollTop()+\"px\" },{queue: false, duration: 350});});*/
                        
                        $this->jquery_ext->add_script($jquery_script);
                        //if(!$this->session->Is_logged_in()){
                        if(!$this->frame->users()->is_authen()){
			$data=array('login_form'=>$this->gen_login_form(),'login_error'=>'',
                            'app_url'=>base_url(),
                            'session'=>$this->session->userdata,
                            'failure'=>'',
                            'msgtitle'=>'',
                            'msg'=>''
                            );
			//$this->load->view('login',$data);
                        $this->template->content->view('login',$data);
                        $this->template->publish();
                        }else{redirect('frameapp/');}
		}
		public function checkAuth($opt=NULL){
                    $username=$this->input->post('username');
                    $username=(empty($username))?'guest':$username;
                    $avatar_path=$this->config->item('avatar_path');
                    $noavatar_image=$this->config->item('no_avatar');
                        $autherizer=array('username'=>$username,'password'=>$this->input->post('password'));
                        $this->load->library('authentications',$autherizer);
                        $this->authentications->login();
                        if($this->authentications->authenticated()){
                            $this->load->model('Rbac_users_model','user');
                            $user=$this->user;
                            $user->set('user_id',$this->authentications->get_authen_obj()->get_user_id());
                            $user=$user->getdata();
                        }
                        if(!empty($user)&&$user->user_id>-1){
                                $this->frame->users()->user_id=$user->user_id;
                                $this->frame->users()->fullname=$user->firstname.' '.$user->lastname;
                                $this->frame->users()->avatar=(!empty($user->avatar))?$user->avatar:base_url($avatar_path.$noavatar_image);
                                $this->frame->users()->is_logedin=true;
                                $this->frame->users()->save();
                                //$this->frame->initialize();
                                $this->frame->app()->set_app_id($this->config->item('app_id'));
                                $this->frame->app()->initialize();
                                
                        }
			
			//var_dump($this->frame->users()->hasPermission('login'));
                        if($this->frame->users()->hasPermission('login')->object('loginpanel')->read())
                        {
                                $data=array(
                                            'failure'=>false,
                                            'msgtitle'=>null,
                                            'msg'=>null,
                                            'redirect'=>site_url('frameapp')
                                    );
                        }else{	
                            $data=array(
                                            'failure'=>false,
                                            'msgtitle'=>'ไม่สามาระเข้าสู่ระบบได้',
                                            'msg'=>'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง',
                                            'redirect'=>null
                                            
                                    );
                            if($this->frame->users->is_authen()){
                                 $data['msg']='คุณไม่ได้รับอนุญาติให้เข้าสู่ระบบเนื่องจากปัญหาบางประการ<br/>กรุณาติดต่อผู้ดูแลระบบหรือผู้ที่มีส่วนรับผิดชอบ';
                                 $this->frame->logout();
                            }
				
                        }
                        switch(strtolower($opt))
				{
					case 'json':
                                            $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
                                            $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
                                            $this->output->set_header("Pragma: no-cache");
                                            //$this->output->set_content_type('text/plian')->set_output(json_encode($data));
						$this->output->set_content_type('application/json')->set_output(json_encode($data));
					break;
					default:
						$this->load->view('login',$data);
				}
	}
   }
?>