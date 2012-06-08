<div id="roles_list" style="text-align: left;">   
 <table>
        <thead>
	<tr class="odd">
		<td class="column1"></td>
		<th scope="col" abbr="Home">ชื่อบทบาท</th>
		<th scope="col" abbr="Home Plus">รายละเอียด</th>	
		<th scope="col" abbr="Business Plus">จัดการ</th>
	</tr>	
	</thead>
<tbody>
    <?
    $i=0;
    foreach($roles_list as $role):
        $i++;
        ?>
    <tr <?echo ($i%2)? 'class="odd"':'';?>>
        <th scope="row" class="column1"><?=form_checkbox('roles_id[]',$role->role_id);?></th>
        <td><? echo anchor('roles/roles_main/detail/'.$role->role_id,$role->name);?></td>
        <td><? echo $role->description;?></td>
        <td><? echo anchor(current_url().'#'.$role->role_id,'เพิ่มบทบาทย่อย',array('class'=>'add_subroles'));?>,<? echo anchor('permissions/permissions_main/permissions_roles/'.$role->role_id,'สิทธิ',array('class'=>'manage_p'));?>,<? echo anchor('roles/roles_main/members/'.$role->role_id,'สมาชิก',array('class'=>'assign_users'));?>,<? echo anchor('roles/roles_main/edit/'.$role->role_id,'แก้ไข');?></td>
    </tr>
<?
    endforeach;
?></tbody>
</table>
    <p id="page" style="text-align: center;">Page :<? $page=0;
            for($j=0;$j<$num_roles;$j+=(int)$row_per_page){
                $page++;
                echo anchor('roles/roles_main/page/'.$page,$page).' | ';
            }
        ?></p>
</div>
