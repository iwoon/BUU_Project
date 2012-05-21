        <div class="nav">
            <? foreach($menu as $item):?>
            <a href="<?=$item['url'];?>" class="<?=$item['type'];?>">
                <img src="<?=$item['icon'];?>" alt="<?=$item['label'];?>"/>
                <?=$item['label'];?>
            </a>
            <? endforeach;?>
            <? /*
                <a href="$item['url']" alt="$item['label']" class="$item['type']"
                    <span class="$item['action']" >$item['label']</span>
                </a>
             */?>
        </div>
        <div class="app-container"><?=$app_list;?></div>
    </body>
</html>
