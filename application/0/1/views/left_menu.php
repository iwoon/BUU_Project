<div id="left_menu_panel" style = "float:left;width: 200px;padding:0.2em 0.2em 1em;">
    <? echo form_fieldset('การจัดการ');?>
      <div style = "float:left;">
              <? 
                echo anchor('admin_manages_users','จัดการผู้ใช้')."<br/>";
                echo anchor('admin_manages_roles','จัดการบทบาท')."<br/>";
                echo anchor('admin_manages_permissions','จัดการสิทธิ')."<br/>";
                echo anchor('admin_manages_programes','จัดการโปรแกรม')."<br/>";
              ?>
         </a>
       </div>
    <? echo form_fieldset_close();?>
</div>
