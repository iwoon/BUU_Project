<div id="data_list" style="float: left;">    
<table border="0" cellpadding="<?=$cellpadding;?>">
    <tr bgcolor="<?=$header_color;?>">
        <? if(isset($checkbox)): ?>
            <td> <? echo form_checkbox('checkall');?> </td>
       <? endif;?>
    <? foreach($header as $item):?>
        <td><?=$item;?></td>
    <? endforeach;?>
    </tr>
    <?
    $i=0;
    foreach($data as $list):
        $i++;
     if (isset($row_color)){?>
    
        <tr bgcolor="<? echo ($i%2)?$row_color[0]:$row_color[1];?>">
     <? }else{?>
         <tr>
     <? } ?>
            <? foreach($list as $item):?>
                <td><?=$user->username;?></td>
            <? endforeach;?>
        </tr>
<?
    endforeach;
?>
</table>
</div>
