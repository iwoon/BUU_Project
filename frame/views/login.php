<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?=base_url();?>">
	<meta charset="utf-8">
	<title>Application Frame</title>
	<style type="text/css">
	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }
	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body{
		margin: 0 15px 0 15px;
	}
	
	p.footer{
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}
	
	#container{
		margin: 10px;
		border: 1px solid #D0D0D0;
		-webkit-box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
        <? echo css('frame.css');
            echo css('messagebox.css');
        ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js" type="text/javascript"></script>
	<script>
		$(document).ready(function(){
			$("#login").submit(function(e){
                            var form=$(this);
                            var user=form.find('input[name="username"]').val();
                            var pass=form.find('input[name="password"]').val();
                            var url=form.attr('action');
				var data='username='+user+'&password='+pass;
				$.ajax({
					type:'POST',
					url: url+'/json',//'index.php/login/CheckAuth/json',
					data:data,
					success:function(data){
                                              if(data.msgtitle!=null){
                                                  var div_message_box='<div id=\'status_bar\' name=\'status_bar\'>';
                                                  var close_icon='<img id="close_message" style="float:right;cursor:pointer" src="<?=base_url();?>frame/asset/images/close.png" /></div>';
                                                $('#body').append(div_message_box);
						$('#status_bar').empty().append(data.msgtitle).fadeOut(800).fadeIn(800)
                                                        .fadeOut(800).fadeIn(800,function(){$('#status_bar').empty().append(data.msg+''+close_icon);}).fadeOut(800).fadeIn(800)
                                                        .delay(10000).hide('fast',function(){$('#status_bar').remove();});
                                              }
                                                if(data.redirect!=null){window.location.href=data.redirect;}
					},
					dataType:'json'
				});
                            e.preventDefault();
                            return false;
			});
                        $('#close_message').click(function()
                        {
                          $('#status_bar').animate({ top:"+=15px",opacity:0 }, "fast");
                          $('#status_bar').remove();
                        });
                        $(window).scroll(function(){$('#status_bar').animate({top:$(window).scrollTop()+"px" },{queue: false, duration: 350});});
  
		});
	</script>
</head>
<body>
<div id="container">
	<h1><center>:: Authentication :: </center></h1>

	<div id="body">
                <? if($failure): ?>
		<span id="message" name="message" style="color:red;font-size:14;"><?=$msgtitle;?><br/><?=$msg;?></span>
		<?=$login_error;?>
                <? endif; ?>
		<fieldset ><legend>Login Panel</legend>
              
                    <?=$login_form;?>
		</fieldset>
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds <br/><br/>Session id:<?=$session['session_id'];?></p>
</div>

</body>
</html>