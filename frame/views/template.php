<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->template->title->default("FRAME Applications"); ?></title>
<link href="<? echo base_url().'frame/asset/css/template.css';?>" rel="stylesheet" type="text/css" />
<? $this->jquery_ext->output();?>
	<?php echo $this->template->meta; ?>
	<?php echo $this->template->stylesheet; ?>
</head>
<body>
	<div id="header">
      <div class="logo"><img src="<? echo base_url().'frame/asset/images/buu_logo.jpg';?>" alt="มหาวิทยาลัยบูรพา" width="100px" height="100px" /></div><p class="apptitle"> ระบบจัดการผู้ใช้</p>
    <div  class="profiles_pannel">
        <? echo $this->template->widget('profiles_widget',array('fullname'=>'คุณยังไม่ได้เข้าสู่ระบบ'));?>
    </div>
   </div>
	<!-- top navigation start -->
<div id="topNav">
            <? echo $this->template->widget('navigation_widget',array('หน้าหลัก'));?>
        </div>
	<!-- top navigation end -->
	<!-- body start -->
<div id="body">
		<!-- body pannel start -->
        <div id="topShadow"></div>
        <div id="bodypannel">
          <? echo $this->template->content;?>
        </div>
        <div id="bottomShadow"></div><br class="spacer" />
		<!-- body pannel end -->
</div>
	  <!-- body end -->
	  <!-- footer start -->
  <div id="footer">
    <p >คณะวิทยาการสารสนเทศ มหาวิทยาลัยบูรพา </p>
    <p>169 ต.แสนสุข อ.เมือง จ.ชลบุรี 20131 โทรศัพท์ 038-103061 แฟกซ์ 038-393245</p>
    <p class="tworld">Designed By Weerayut Plaharn<br/>Page rendered in : <strong>{elapsed_time}</strong> seconds</p>
</div>
	<!-- footer end -->	
</body>
</html>
