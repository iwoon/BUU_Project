<? if(isset($navigation_data)):
    $i=0;
    foreach($navigation_data as $item):
     echo ($i>0)?"->":'';
        if(!empty($item['link'])){
        echo '<a href="'.$item['link'].'">'.$item['page'].'</a>';}
        else{echo $item['page'];}      
        $i++;
    endforeach;
endif;?>