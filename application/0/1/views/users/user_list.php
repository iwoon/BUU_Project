<div id="user_list" style="float: left;">   
<table border="0" cellpadding="2em">
    <tr bgcolor="83BBA5"><td></td><td>ชื่อผู้ใช้</td><td>ชื่อ-นามสกุล</td><td>อีเมล์</td><td>จัดการ</td></tr>
    <?
    $i=0;
    foreach($users_list as $user):
        $i++;
        ?>
    <tr bgcolor="<?echo ($i%2)?'F5FAFD':'DEEFFD';?>">
        <td><?=form_checkbox('user_id[]',$user->user_id);?></td>
        <td><?=$user->username;?></td>
        <td><? echo $user->firstname.' '.$user->lastname;?></td>
        <td><?=$user->email;?></td><td><? echo anchor('users/users_main/profiles/'.$user->user_id,'ข้อมูล');?>,<? echo anchor('users/users_main/role/'.$user->user_id,'บทบาท');?></td>
        </tr>
<?
    endforeach;
?>
</table>
    <p>Page :<? $page=0;
            for($j=0;$j<$num_users;$j+=(int)$row_per_page){
                $page++;
                echo anchor('users/users_main/page/'.$page,$page).' | ';
            }
        ?></p>
</div>
