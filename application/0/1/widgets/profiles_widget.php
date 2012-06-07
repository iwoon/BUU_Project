<?php if(!defined('BASEPATH')) exit("No direct access script allowed");
class profiles_widget extends Widget {
        private $fullname='คุณยังไม่ได้เข้าสู่ระบบ';
        private $edit_profile_link=NULL;
        private $data=NULL;
        private $avatar=NULL;  
	public function display($args) {
          if($this->frame->users()->checkaccess('profiles','profiles_panel')->read())
          {
            if($this->frame->users()->is_authen())
            {
                $this->jquery_ext->add_script("$('#profile_picture').hover(function(){
                        $('.profiles_pannel').append(\"<span id='change_picture'>เปลี่ยนรูประจำตัว</spand>\");
                    },function(){
                        $('.profiles_pannel').find('span:last').remove();
                    });");
                
                $this->fullname=$this->frame->users()->fullname;
                $this->edit_profile_link=$this->frame->url.'/profiles/'.$this->frame->users()->user_id;
		$this->avatar=$this->frame->users()->avatar;
            $this->data = array(
                    'avatar'=>image((empty($this->avatar))?base_url().'frame/asset/images/profiles/noimage.gif':$this->avatar,'',array('width'=>'100px','height'=>'100px')),
                    'fullname'=>$this->fullname,
                    'edit_profile'=>$this->edit_profile_link
            );
                $this->load->view('widgets/profiles', $this->data);
            }
          }
        }

}
?>
