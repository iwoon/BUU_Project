<?php // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('Admin_users_add', $attributes); ?>

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
        <label for="username">ชื่อผู้ใช้ <span class="required">*</span></label>
        <?php echo form_error('username'); ?>
        <br /><input id="username" type="text" name="username" maxlength="255" value="<?php echo set_value('username'); ?>"  />
</p>

<p>
        <label for="password">รหัสผ่าน <span class="required">*</span></label>
        <?php echo form_error('password'); ?>
        <br /><input id="password" type="text" name="password" maxlength="255" value="<?php echo set_value('password'); ?>"  />
</p>

<p>
	
        <?php echo form_error('auto_generate'); ?>
        
        <?php // Change the values/css classes to suit your needs ?>
        <br /><input type="checkbox" id="auto_generate" name="auto_generate" value="true" class="" <?php echo set_checkbox('auto_generate', true); ?>> 
                   
	<label for="auto_generate">auto generate</label>
</p> 
<p>
        <label for="email">อีเมล์ <span class="required">*</span></label>
        <?php echo form_error('email'); ?>
        <br /><input id="email" type="text" name="email" maxlength="255" value="<?php echo set_value('email'); ?>"  />
</p>

<p>
        <label for="authen_type">ประเภท <span class="required">*</span></label>
        <?php echo form_error('authen_type'); ?>
        
        <?php // Change the values in this array to populate your dropdown as required ?>
        <?php $options = array(
                                                  ''  => 'Internal',
                                                  'Ldap'    => 'Ldap'
                                                ); ?>

        <br /><?php echo form_dropdown('authen_type', $options, set_value('authen_type'))?>
</p>                                             
                        
<p>
        <label for="server">เซิฟเวอร์ <span class="required">*</span></label>
        <?php echo form_error('server'); ?>
        <br /><input id="server" type="text" name="server" maxlength="255" value="<?php echo set_value('server'); ?>"  />
</p>

<p>
        <label for="port">พอร์ต</label>
        <?php echo form_error('port'); ?>
        <br /><input id="port" type="text" name="port"  value="<?php echo set_value('port'); ?>"  />
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
