
<?php // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('Admin_permission_add', $attributes); ?>

<p>
        <label for="permission_name">ชื่อสิทธิ <span class="required">*</span></label>
        <?php echo form_error('permission_name'); ?>
        <br /><input id="permission_name" type="text" name="permission_name" maxlength="100" value="<?php echo set_value('permission_name'); ?>"  />
</p>

<p>
        <label for="alias_permision_name">ชื่อแทน <span class="required">*</span></label>
        <?php echo form_error('alias_permision_name'); ?>
        <br /><input id="alias_permision_name" type="text" name="alias_permision_name" maxlength="255" value="<?php echo set_value('alias_permision_name'); ?>"  />
</p>

<p>
        <label for="role_id">ภายใต้ <span class="required">*</span></label>
        <?php echo form_error('role_id'); ?>
        
        <?php // Change the values in this array to populate your dropdown as required ?>
        <?php $options = array(
                                                  ''  => 'Please Select',
                                                  'example_value1'    => 'example option 1'
                                                ); ?>

        <br /><?php echo form_dropdown('role_id', $options, set_value('role_id'))?>
</p>                                             
                        
<p>
        <label for="object_name">วัตถุ <span class="required">*</span></label>
        <?php echo form_error('object_name'); ?>
        <br /><input id="object_name" type="text" name="object_name"  value="<?php echo set_value('object_name'); ?>"  />
</p>

<p>
	
        <?php echo form_error('read'); ?>
        
        <?php // Change the values/css classes to suit your needs ?>
        <br /><input type="checkbox" id="read" name="read" value="enter_value_here" class="" <?php echo set_checkbox('read', 'enter_value_here'); ?>> 
                   
	<label for="read">อ่าน/เข้าถึง</label>
</p> 
<p>
        <label for="create">เขียน/เพิ่ม</label>
        <?php echo form_error('create'); ?>
        <br /><input id="create" type="text" name="create"  value="<?php echo set_value('create'); ?>"  />
</p>

<p>
        <label for="edit">แก้ไข/ปรับปรุง</label>
        <?php echo form_error('edit'); ?>
        <br /><input id="edit" type="text" name="edit"  value="<?php echo set_value('edit'); ?>"  />
</p>

<p>
        <label for="delete">ลบ</label>
        <?php echo form_error('delete'); ?>
        <br /><input id="delete" type="text" name="delete"  value="<?php echo set_value('delete'); ?>"  />
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
