
<div id="program_panel">
<? foreach($app_list as $app):?>
        <div id = "cpanel">
                    <div style = "float:left;">
                            <div class = "icon">
                                    <a href = "<? echo $this->config->item('app_controller').$app->app_id;?>">
                                            <img src = "<?=$app->app_icon;?>" alt = "<?=$app->app_name;?>" width = "120" height = "120" border = "0" align = "middle"/>
                                            <span><?=$app->app_name;?></span>
                                    </a>
                            </div>
                    </div>
         </div>
    <? endforeach;?>
</div>