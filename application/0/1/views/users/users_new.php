<div id="users_form" style="float: left;padding:1em 3em ">
<?php
 // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('users/users_main/add/submit', $attributes); 
echo form_fieldset('รายละเอียดผู้ใช้');
?>

<p>
        <label for="firstname">ชื่อ <span class="required">*</span></label>
        <?php echo form_error('firstname'); ?>
        <input id="firstname" type="text" name="firstname" maxlength="255"  />
</p>

<p>
        <label for="lastname">นามสกุล <span class="required">*</span></label>
        <?php echo form_error('lastname'); ?>
        <input id="lastname" type="text" name="lastname" maxlength="255" />
</p>
<p>
        <label for="email">อีเมล์ <span class="required">*</span></label>
        <?php echo form_error('email'); ?>
        <input id="email" type="text" name="email" maxlength="255"  />
</p>
<? echo form_fieldset_close();?>
<br/>
<? echo form_fieldset('รหัสผ่าน');?>
<p>
        <label for="current_password">รหัสผ่านเดิม <span class="required">*</span></label>
        <?php echo form_error('current_password'); ?>
        <input id="current_password" type="text" name="current_password" maxlength="255"/>
</p>

<p>
        <label for="new_password">รหัสผ่านใหม่ <span class="required">*</span></label>
        <?php echo form_error('new_password'); ?>
        <input id="new_password" type="text" name="new_password" maxlength="255"  />
</p>

<p>
        <label for="confirm_new_password">ยืนยันรหัสผ่าน <span class="required">*</span></label>
        <?php echo form_error('confirm_new_password'); ?>
        <input id="confirm_new_password" type="text" name="confirm_new_password" maxlength="255" />
</p>
<? echo form_fieldset_close();?>
<p>
        <?php echo form_submit( 'submit', 'บันทึก'); ?>
</p>

<?php echo form_close(); ?>
</div>
