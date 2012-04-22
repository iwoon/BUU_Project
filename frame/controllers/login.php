<?php if(!defined('BASEPATH')) exit("No direct access script allowed");
	class Login extends CI_Controller{
		public function __contruct(){
			parent::__construct();
		}
                public function gen_login_form(){
                        $this->load->library('form');
                        return $this->form->open('login/CheckAuth','login|login')
                                    ->text('username|username','Username:','trim|alpha_numberic|xss_clean')
                                    ->pass('password|password','Password:','trim|xss_clean')
                                    ->submit()
                                    ->reset()
                                    ->get();
                }
		public function index(){
                        $this->load->library('session');
                        if(!$this->session->Is_logged_in()){
			$data=array('login_form'=>$this->gen_login_form(),'login_error'=>'',
                            'app_url'=>base_url(),
                            'session'=>$this->session->userdata,
                            'failure'=>'',
                            'msgtitle'=>'',
                            'msg'=>''
                            );
			$this->load->view('login',$data);
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
                                //if(!$this->load->library('rbac_session'))exit('can\'t load library rbac_session');
                                $sess=new Rbac_session();
                                if(!$sess->add(array('session_id'=>$this->session->get_session_id(),'user_id'=>$user->user_id)))exit('can\'t add user session');
				$user_sess=array('USER'=>array('user_id'=>$user->user_id));
				$this->session->set_userdata($user_sess);
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