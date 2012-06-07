<div>
<script type="text/javascript">
        $(document).ready(function() {
            $('#btnAddObj').click(function() {
                var num     = $('.clonedObject').size(); // how many "duplicatable" input fields we currently have
                var newNum  = new Number(num + 1);      // the numeric ID of the new input field being added
 
                // create the new element via clone(), and manipulate it's ID using newNum value
                var newElem = $('#object' + num).clone().attr('id', 'object' + newNum);
                    newElem.children(':first').attr('name', 'operation[' + newNum+']');
                // insert the new element after the last "duplicatable" input field
                $('#object' + num).after(newElem);
                if(num>1){$('#btnDelObj').removeAttr('disabled');}
            });
 
            $('#btnDelObj').click(function() {
                var num = $('.clonedObject').size(); // how many "duplicatable" input fields we currently have
                $('#object' + num).remove();     // remove the last element
                if(num<=2){$('#btnDelObj').attr('disabled','disabled');}
            });
            
            $('#btnDelObj').attr('disabled','disabled');
            //$('input').click(function(){$(this).val('');});
            //$('textarea').click(function(){$(this).val('');})
        });
    </script>
 <?
    if(!isset($permise))
    {
        $permise=new stdClass();
        $permise->permission_group_name='สิทธิภาษาอังกฤษ (ตัวเล็ก)';
        $permise->permission_group_description='เช่น สิทธิอนุญาติเกี่ยวกับการเข้าสู่ระบบ';
        $permise->permission_name='เช่น อนุญาติให้เข้าสู่ระบบ';
        $permise->object_name='ชื่อวัตถุเป็นภาษาอังกฤษ (ตัวเล็ก)';
        $permise->read=0;
        $permise->create=0;
        $permise->update=0;
        $permise->delete=0;
        $permise->permission_group_id=0;
  
    }
 
 ?>
<form id="myForm" method="post" action="<? echo site_url('permissions/permissions_add/save');?>">
    <div id="permise1" style="margin-bottom:4px;" class="clonePermise">
        <fieldset><legend>สิทธิ</legend>
        <div>
        ชื่อกลุ่มสิทธิ <? echo form_dropdown('permise_group',$group_data,$permise->permission_group_id);?><br/>สร้างกลุ่มใหม่ :<input type="text" name="group_name" size="50" value="<? echo $permise->permission_group_name;?>"/><br/>
        รายละเอียด<br/><textarea name="group_description" cols="80" rows="3"/><?=$permise->permission_group_description;?></textarea><br/>
        ภายใต้บทบาท <br/> <select name="base_on[]" id="base_on"/>
                        <? foreach($select_role as $role):?>
                            <option value="<?=$role->role_id;?>" <? echo (isset($role_id)&&($role->role_id==$role_id))?'selected':'';?>/><? echo $role->name.' '.$role->description;?></option>
                        <? endforeach;?>
                    </select>
        </div>
        <div id="object1" style="margin-bottom:4px;float:left;" class="clonedObject"><br/>
            คำอธิบายเกียวกับอนุญาตินี้<br/><textarea name="name" cols="80" rows="3"/><?=$permise->permission_name;?></textarea><br/>
            วัตถุ: <input type="text" name="object" size="50" value="<?=$permise->object_name;?>"/><br/>
            <fieldset><legend>การกระทำ</legend>
            อ่าน/เข้าถึง<input type="checkbox" name="operation[]" value="read" <? echo ($permise->read==1)?'checked':'';?>/>
            เพิ่ม/สร้างใหม่<input type="checkbox" name="operation[]" value="create" <? echo ($permise->create==1)?'checked':'';?>/>
            แก้ไข/ปรับปรุง<input type="checkbox" name="operation[]" value="update" <? echo ($permise->update==1)?'checked':'';?>/>
            ลบ<input type="checkbox" name="operation[]" value="delete" <? echo ($permise->delete==1)?'checked':'';?>/>
            </fieldset>
            <hr/>
        </div>
           
        </fieldset>
    </div>
        <div>
            <input type="submit" id="btnAddPer" value="เพิ่มสิทธิ" />
            <? if(isset($permise->permission_group_id)){?>
            <input type="hidden" name="permission_group_id" value="<?=$permise->permission_group_id;?>"/>
            <?} if(isset($permise->permission_id)){?>
            <input type="hidden" name="permission_id" value="<?=$permise->permission_id;?>"/>
           <? } if(isset($role_id)){?>
            <input type="hidden" name="role_id" value="<?=$role_id;?>"/>
            <? } ?>
        </div>
</form>
</div>