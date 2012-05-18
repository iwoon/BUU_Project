<?php if(!defined('BASEPATH')) exit("No direct access script allowed");
	class Login extends CI_Controller{
		public function __contruct(){
			parent::__construct();
		}
                public function gen_login_form(){
                        $this->load->library('form');
                        return $this->form->open('login/CheckAuth','login|login')
                                    ->text('username|username','Username: ','trim|alpha_numberic|xss_clean')
                                    ->pass('password|password','Password: ','trim|xss_clean')
                                    ->submit()
                                    ->reset()
                                    ->get();
                }
		public function index(){
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
                                                  "./*var div_message_box='<div id='status_bar' name='status_bar'>';
                                                  var close_icon='<img id=\"close_message\" style=\"float:right;cursor:pointer\" src=\"frame/asset/images/close.png\" /></div>';
                                                $('#body').append(div_message_box);
						$('#status_bar').empty().append(data.msgtitle).fadeOut(800).fadeIn(800)
                                                        .fadeOut(800).fadeIn(800,function(){"."$('#status_bar').empty().append(data.msg+''+close_icon);}).fadeOut(800).fadeIn(800)
                                                        .delay(10000).hide('fast',function(){"."$('#status_bar').remove();});*/
                                              "jAlert('error',data.msg,data.msgtitle,function(r){"."$('username').empty().focus();$('password').empty()});   
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
                        }else{redirect('frame/');}
		}
		public function checkAuth($opt=NULL){
                    //$this->load->library('form');
			$this->load->model('Login_Model','login',true);
			$authen_params=array('username'=>$this->input->post('username'),
                            'password'=>(strtolower($this->input->post('username'))=='guest')?'':md5($this->input->post('password'))
                            );
			$user=$this->login->CheckAuth($authen_params);
         
			if(!empty($user)&&$user->user_id>-1){
                                //$sess=new Rbac_session();
                                //if(!$sess->add(array('session_id'=>$this->session->get_session_id(),'user_id'=>$user->user_id)))exit('can\'t add user session');
				//$user_sess=array('USERS'=>array('user_id'=>$user->user_id));
				//$this->session->set_userdata($user_sess);
                                $sess_user=$this->frame->users();
                                $sess_user->user_id=$user->user_id;
                                $sess_user->avatar=$user->avatar;
                                $sess_user->is_logedin=true;
                                $sess_user->save();
                                if($opt=='json'){
                                    $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
                                            $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
                                            $this->output->set_header("Pragma: no-cache");
                                            //$this->output->set_content_type('text/plian')->set_output(json_encode($data));
						$this->output->set_content_type('application/json')->set_output(json_encode(array('redirect'=>site_url('frame/'))));
                                            
                                    }else{
                                        redirect('frame/');
                                        }
				
			}else{
				$data=array('login_form'=>$this->gen_login_form(),'login_error'=>'',
					'failure'=>true,
					'msgtitle'=>'ไม่สามารถเข้าสู่ระบบได้',
					'msg'=>'ชื่อผู้ใช้หรือรหัสผ่านผิด กรูณาลองใหม่อีกครั้ง',
					'app_url'=>base_url(),
					'session'=>$this->session->userdata
				);
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
	}
?>