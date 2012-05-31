
	<div id="admin_icon_panel">        
        <? foreach($app_list as $app):?>
                        <div id = "cpanel">    
                                    <div class = "icon">
                                            <a href = "<? echo $app['app_url'];?>">
                                                    <img src = "<?=$app['app_icon'];?>" alt = "<?=$app['app_name'];?>" width = "120" height = "120" border = "0" align = "middle"/>
                                                    <span><?=$app['app_name'];?></span>
                                            </a>
                                    </div>
                       </div>
            <? endforeach;?>
        </div>
