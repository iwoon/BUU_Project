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
        if($this->frame->users()->hasPermission('users_management')->object('users')->delete()){
            $menu[]=array(
                            'label'=>'ลบ',
                            'url'=> '#',//site_url('users/users_main/delete'),
                            'icon'=>  image_path('icons/without-shadows/badge-square-minus-16-ns.png'),
                            'type'=>'delete',
                            'action'=>'roles/roles_main/delete'
                        );
        }
        return $menu;
    }
}
?>
