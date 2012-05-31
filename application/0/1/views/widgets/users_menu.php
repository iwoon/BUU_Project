<div id="toolbar">
        <? foreach($menu as $item):?>
         <a href="<?=$item['url'];?>" class="<?=$item['type'];?>">
                <img src="<?=$item['icon'];?>" alt="<?=$item['label'];?>"/>
                <?=$item['label'];?>
         </a>
    <? endforeach;?>
</div>
