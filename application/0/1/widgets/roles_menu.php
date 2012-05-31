<?php
class Roles_menu extends Widget
{
    public function display()
    {
        if($this->frame->users()->is_authen())
        {
            $data['menu']=$this->create_menu();
            $this->load->view('widgets/users_menu',$data);
        }
    }
    private function create_menu(){
        $menu=array();
        if($this->frame->users()->hasPermission('users_management')->object('users')->read()){
                    $menu[]=array(
                                'label'=>'เพิ่มผู้ใช้',
                                'url'=>site_url('users/users_main/add'),
                                'icon'=>image_path('icons/without-shadows/cpanel.png'),
                                'type'=>'buttons',
                                'action'=>'users/users_main/add'
                   );
        }
        if($this->frame->users()->hasPermission('users_management')->object('users')->delete()){
            $menu[]=array(
                            'label'=>'ลบ',
                            'url'=> '#',//site_url('users/users_main/delete'),
                            'icon'=>  image_path('icons/with-shadows/person-profile-16.png'),
                            'type'=>'deletebutton',
                            'action'=>'users/users_main/delete'
                        );
        }
        return $menu;
    }
}
?>
