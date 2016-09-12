<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv = "X-UA-Compatible" content = "IE=edge,chrome=1">
<title>{$siteinfo.sitetitle}-{$siteinfo.sitename}</title>

<style>
body{background:url(__PUBLIC__/images/tips_bg.jpg) no-repeat center; margin:0;}
.tips{ width:600px; height:300px; margin:0 auto; text-align:center;margin-top:100px;}
.tips .img {margin-bottom:40px;}
.tips .sysmsg{color:#8b8989; font-size:16px; height:24px; line-height:24px;}
.tips .sysmsg b{font-size:20px;}
.tips .message{font-size:30px;}
.tips .jumpurl{ font-size:14px; height:30px; width:100px; margin:20px auto; line-height:30px;-moz-border-radius:3px; -webkit-border-radius:3px;border-radius:3px;}
.tips .jumpurl a{color:#fff; display:block;}
.error .message,.error .sysmsg b{color:#df3f3f;}
.error .jumpurl{background:#df3f3f;}
.success .message,.success .sysmsg b{color:#54a519;}
.success .jumpurl{background:#54a519;}
</style>
</head>
<body>
<?php if(isset($message)) {?>
<div class="tips success">
    <div class="img"><img src="__PUBLIC__/images/payment-duigou.png" alt="success"/></div>
    <div class="message"><?php echo($message); ?></div>
    <div class="sysmsg">系统将会在<b id="wait"><?php echo($waitSecond); ?></b>后自动跳转</div>
    <div class="jumpurl"><a id="href" href="<?php echo($jumpUrl); ?>">立即跳转</a></div>
</div>
<?php }else{?>
<div class="tips error">
    <div class="img"><img src="__PUBLIC__/images/note-pic.png" alt="success"/></div>
    <div class="message"><?php echo($error); ?></div>
    <div class="sysmsg">系统将会在<b id="wait"><?php echo($waitSecond); ?></b>后自动跳转</div>
    <div class="jumpurl"><a id="href" href="<?php echo($jumpUrl); ?>">立即跳转</a></div>
</div>
<?php }?>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;

var interval = setInterval(function(){
    var time = --wait.innerHTML;
    if(time <= 0) {
        location.href = href;
        clearInterval(interval);
    };
}, 1000);
})();
</script>
</body>
</html>
