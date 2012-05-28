

<div><center>
	<div id="loginpanel">
                <? if($failure): ?>
		<span id="message" name="message" style="color:red;font-size:14;"><?=$msgtitle;?><br/><?=$msg;?></span>
		<?=$login_error;?>
                <? endif; ?>
                <center><h3>:: Authentication :: </h3></center>
		<fieldset ><legend>Login Panel</legend>
              
                   <?=$login_form;?>
		</fieldset>
	</div>
    </center></div>
