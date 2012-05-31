<div id="left_menu_panel" style = "float:left;width: 200px;padding:0.2em 0.2em 1em;">
    <? echo form_fieldset('การจัดการ');?>
      <div style = "float:left;">
              <? 
                echo anchor('users/','จัดการผู้ใช้')."<br/>";
                echo anchor('roles/','จัดการบทบาท')."<br/>";
                echo anchor('permissions/','จัดการสิทธิ')."<br/>";
                //echo anchor('programes/','จัดการโปรแกรม')."<br/>";
              ?>
         </a>
       </div>
    <? echo form_fieldset_close();?>
</div>
