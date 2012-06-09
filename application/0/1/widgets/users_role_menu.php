<?php
class Users_role_menu extends Widget{
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
        /*if($this->frame->users()->hasPermission('roles_management')->object('assignment')->read()){
                    $menu[]=array(
                                'label'=>'เพิ่มบทบาท',
                                'url'=>site_url('users/users_main/roles/add'),
                                'icon'=>image_path('icons/without-shadows/cpanel.png'),
                                'type'=>'buttons',
                                'action'=>'users/users_main/roles/add'
                   );
        }*/
        if($this->frame->users()->hasPermission('roles_management')->object('revoke')->read()){
            $menu[]=array(
                            'label'=>'ออกจากบทบาท',
                            'url'=> '#',//site_url('users/users_main/delete'),
                            'icon'=>  image_path('icons/without-shadows/badge-circle-cross-16-ns.png'),
                            'type'=>'delete',
                            'action'=>'users/users_main/roles/delete'
                        );
        }
        return $menu;
    }
}
?>
