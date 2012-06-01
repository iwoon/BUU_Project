<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Permissions_main extends CI_Controller
{
    protected $page='จัดการสิทธิ';
    public function __construct()
    {
        parent::__construct();
        $this->frame->nav()->add('หน้าหลัก',$this->frame->url);
        $this->frame->nav()->add('ระบบจัดการผู้ใช้',site_url('welcome'));
                $this->load->model('rbac_users_model','user');
        $this->load->library('form');
        $this->load->model('roles/rbac_roles','roles');
        $this->load->library('jquery_ext');
    }
    public function index()
    {
        $this->frame->nav()->add($this->page);
        $left_menu=$this->load->view('left_menu','',true);
                    $this->template->content->add($left_menu);
                    $this->template->content->add('<div id="admin_panel" style="float:right;width:780px;">');
                    $this->template->content->add('<div id="roles_panel" style="float:left;">'.$this->permission_panel().'</div>');
                    $this->template->content->add('</div>');
        $this->jquery_ext->add_css(css_path('table.css'));
        $this->template->publish();
    }
    private function permission_panel()
    {
        //$roles=$this->roles->get_parent_roles();
        $this->load->model('permissions/rbac_permission','permise');
        $data='';
        $operation=array(
            'read'=>'อ่านได้/เข้าถึงได้',
            'create'=>'เพิ่มได้/เขียนใหมได้',
            'update'=>'แก้ไข้ได้/ปรับปรุงได้',
            'delete'=>'ลบได้');
            
            $permise=$this->permise->get_all_permission();
            $new=0;$i=0;$end=0;
            foreach($permise as $item)
            {
                if($item->role_id!=$new){
                    if($end>0){
                        $data.='</tr></tbody></table></fieldset>';
                    }
                    $data.=' <fieldset><legend>'.$item->rolename.'</legend>';
                    $data.='<p>'.$item->roledescription.'</p>';
                    //$data.='<table border=0 cellpadding=1px style="font-size:12.5px;">';
                    $data.='<table> <thead><tr class="odd">
                        <td class="column1">สิทธิ</td>';
                    foreach( $operation as $op=>$des){$data.='<th scope="col">'.$des.'&nbsp;</th>';}
                    $data.='</tr></thead><tbody>';
                    $new=$item->role_id;
                    $end++;
                }
                $i++;
                $data.='<tr '.(($i%2)? 'class="odd"':'').'>';
                $data.='<td style="text-align:left;">'.$item->description.'</td>';
                foreach($operation as $oper=>$desc)
                {
                    $data.='<td><input type="checkbox" id="permise['.$item->role_id.']['.$item->permission_id.']['.$item->object_id.']['.$oper.']"></td>';
                }
            }
               $data.='</tr></tbody></table></fieldset>';
        return $data;
    }
    /*private function rolepanel()
    {
        $roles=$this->roles->get_parent_roles();
        $data='<table border=0 cellpadding=1px style="font-size:10px;">';
        foreach($roles as $role)    
        {
            $child=$this->roles->getTreeRoles($role->role_id);
            $row=0;
            foreach( $child as $subchild)
            {
                $data.='<tr>';
                for($i=0;$i<$row;$i++){$data.='<td></td>';}
                $data.='<td>'.$subchild->name.'</td><td>'.$subchild->description.'</td>';
                $data.='</tr>';
                $row++;
            }
        }
        $data.='</table>';
        return $data;
    }*/
    private function travalRole($id)
    {
        
    }
    private function gen_form()
    {
        $form=$this->form->open(site_url('users/users_roles/add/roles'),'users_roles|users_roles')->html('<table border=0 cellpadding=1px>')
               ->html('<tr><td>บทบาททั้งหมด</td><td></td><td>บทบาทของผู้ใช้</td><tr>')
                ->html('<tr><td>')->label('ชื่อ')->html('</td><td>')->text('firstname|firstname','','trim|alpha_numberic|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('นามสกุล')->html('</td><td>')->text('lastname|lastname','','trim|alpha_numberic|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('ชื่อผู้ใช้')->html('</td><td>')->text('username|username','','trim|alpha_numberic|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('รหัสผ่าน')->html('</td><td>')->pass('password|password','','trim|xss_clean')->checkbox('auto|auto','true','สร้างอัตโนมัติ',false,'trim|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('อีเมล์')->html('</td><td>')->text('email|email','','trim|xss_clean','',array('maxlength'=>60,'size'=>30))->html('</td></tr>')
                ->html('<tr><td>')->label('พิสูจน์ตัวบุคคล')->html('</td><td></td></tr>')
                ->html('<tr><td>')->label('ผ่าน')->html('</td><td>')->select('authen_type|authen_type',$authen_type,'',1)->html('</td></tr>')
                /*->html('<tr><td>')->label('เซิฟเวอร์')->html('</td><td>')->text('server|server','','trim|xss_clean')->html('</td></tr>')
                ->html('<tr><td>')->label('พอร์ต')->html('</td><td>')->text('port|port','','trim|xss_clean','',array('maxlength'=>4,'size'=>4))->html('</td></tr>')
                */->html('<tr><td></td><td>')->submit('บันทึก')->reset('รีเซ็ต')->html('</td></tr></table>')->get();   
        return $form;
    }
    public function assign()
    {
        
    }
    public function revoke()
    {
        
    }
}
?>
