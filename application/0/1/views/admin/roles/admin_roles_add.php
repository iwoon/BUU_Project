
<?php // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('Admin_roles_add', $attributes); ?>

<p>
        <label for="role_name">ชื่อบทบาท <span class="required">*</span></label>
        <?php echo form_error('role_name'); ?>
        <br /><input id="role_name" type="text" name="role_name" maxlength="255" value="<?php echo set_value('role_name'); ?>"  />
</p>

<p>
        <label for="alias_name">ชื่อแทน <span class="required">*</span></label>
        <?php echo form_error('alias_name'); ?>
        <br /><input id="alias_name" type="text" name="alias_name" maxlength="255" value="<?php echo set_value('alias_name'); ?>"  />
</p>

<p>
        <label for="parent_roles_id">ภายใต้ <span class="required">*</span></label>
        <?php echo form_error('parent_roles_id'); ?>
        
        <?php // Change the values in this array to populate your dropdown as required ?>
        <?php $options = array(
                                                  ''  => 'Please Select',
                                                  'example_value1'    => 'example option 1'
                                                ); ?>

        <br /><?php echo form_dropdown('parent_roles_id', $options, set_value('parent_roles_id'))?>
</p>                                             
                        
<p>
        <label for="description">คำอธิบาย</label>
	<?php echo form_error('description'); ?>
	<br />
							
	<?php echo form_textarea( array( 'name' => 'description', 'rows' => '5', 'cols' => '80', 'value' => set_value('description') ) )?>
</p>

<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>

