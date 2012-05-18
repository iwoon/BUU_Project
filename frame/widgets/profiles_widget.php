<?php if(!defined('BASEPATH')) exit("No direct access script allowed");
class profiles_widget extends Widget {
        private $fullname='คุณยังไม่ได้เข้าสู่ระบบ';
        private $edit_profile_link=NULL;
        private $data=NULL;
        private $avatar=NULL;
	public function display($args) {
            
            if($this->frame->users()->is_authen())
            {
                $this->fullname=$this->frame->users()->fullname;
                $this->edit_profile_link=site_url('um/index.php/users/profiles/edit/'.$this->frame->users()->user_id);
		$this->avatar=$this->frame->users()->avatar;
            $this->data = array(
                    'avatar'=>$this->avatar,
                    'fullname'=>$this->fullname,
                    'edit_profile'=>$this->edit_profile_link
            );
                $this->load->view('widgets/profiles', $this->data);
            }
        }

}
?>
