
<?php // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('Admin_roles_move', $attributes); ?>

<p>
        <label for="from_role">บทบาท <span class="required">*</span></label>
        <?php echo form_error('from_role'); ?>
        
        <?php // Change the values in this array to populate your dropdown as required ?>
        <?php $options = array(
                                                  ''  => 'Please Select',
                                                  'example_value1'    => 'example option 1'
                                                ); ?>

        <br /><?php echo form_dropdown('from_role', $options, set_value('from_role'))?>
</p>                                             
                        
<p>
        <label for="to_roles">ย้ายไปยัง <span class="required">*</span></label>
        <?php echo form_error('to_roles'); ?>
        
        <?php // Change the values in this array to populate your dropdown as required ?>
        <?php $options = array(
                                                  ''  => 'Please Select',
                                                  'example_value1'    => 'example option 1'
                                                ); ?>

        <br /><?php echo form_dropdown('to_roles', $options, set_value('to_roles'))?>
</p>                                             
                        

<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>

