<?php
 // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('Profile', $attributes); ?>

<p>
        <label for="firstname">ชื่อ <span class="required">*</span></label>
        <?php echo form_error('firstname'); ?>
        <br /><input id="firstname" type="text" name="firstname" maxlength="255" value="<?php echo set_value('firstname'); ?>"  />
</p>

<p>
        <label for="lastname">นามสกุล <span class="required">*</span></label>
        <?php echo form_error('lastname'); ?>
        <br /><input id="lastname" type="text" name="lastname" maxlength="255" value="<?php echo set_value('lastname'); ?>"  />
</p>

<p>
        <label for="current_password">รหัสผ่านเดิม <span class="required">*</span></label>
        <?php echo form_error('current_password'); ?>
        <br /><input id="current_password" type="text" name="current_password" maxlength="255" value="<?php echo set_value('current_password'); ?>"  />
</p>

<p>
        <label for="new_password">รหัสผ่านใหม่ <span class="required">*</span></label>
        <?php echo form_error('new_password'); ?>
        <br /><input id="new_password" type="text" name="new_password" maxlength="255" value="<?php echo set_value('new_password'); ?>"  />
</p>

<p>
        <label for="confirm_new_password">ยืนยันรหัสผ่าน <span class="required">*</span></label>
        <?php echo form_error('confirm_new_password'); ?>
        <br /><input id="confirm_new_password" type="text" name="confirm_new_password" maxlength="255" value="<?php echo set_value('confirm_new_password'); ?>"  />
</p>

<p>
        <label for="email">อีเมล์ <span class="required">*</span></label>
        <?php echo form_error('email'); ?>
        <br /><input id="email" type="text" name="email" maxlength="255" value="<?php echo set_value('email'); ?>"  />
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>

?>
