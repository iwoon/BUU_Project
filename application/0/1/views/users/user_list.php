<div id="user_list">
<!--<table border="0" cellpadding="2em" cellspanding="2em">-->
<table>
        <thead>
	<tr class="odd">
		<td class="column1"></td>
		<th scope="col" abbr="Home">ชื่อผู้ใช้</th>
		<th scope="col" abbr="Home Plus">ชื่อ - นามสกุล</th>	
		<th scope="col" abbr="Business">อีเมล์</th>
		<th scope="col" abbr="Business Plus">จัดการ</th>
	</tr>	
	</thead>
<tbody>
    <?
    $i=0;
    foreach($users_list as $user):
        $i++;
        ?>
        <tr <?echo ($i%2)? 'class="odd"':'';?>>
		<th scope="row" class="column1"><?=form_checkbox('user_id[]',$user->user_id);?></th>
		<td><?=$user->username;?></td>
		<td><? echo $user->firstname.' '.$user->lastname;?></td>
		<td><?=$user->email;?></td>
                <td><? echo anchor('users/users_main/profiles/'.$user->user_id,'ข้อมูล');?>,<? echo anchor('users/users_main/role/'.$user->user_id,'บทบาท');?></td>
	</tr>
<?
    endforeach;
?></tbody>
</table>
    <p id="page" style="text-align:center;">Page :<? $page=0;
            for($j=0;$j<$num_users;$j+=(int)$row_per_page){
                $page++;
                echo anchor('users/users_main/page/'.$page,$page).' | ';
            }
        ?></p>
</div>
