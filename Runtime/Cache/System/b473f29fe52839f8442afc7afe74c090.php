<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit|ie-stand" />
    <title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
    <link rel="stylesheet" href="/Public/css/login.css" />
</head>
<body class="bodyBg">
<div class="sysWrap">
    <div class="loginBox" style="height: 315px;">
        <div class="logoPic"></div>
        <div class="rows">
            <input type="text" class="pwdInp" placeholder="请输入短信验证码" name="verifyCode" autocomplete="off">
            <i class="pwdIcon"></i>
        </div>
        <div class="rows">
            <input type="button" class="verifySubmit loginSubmit" value="验证" style="width: 319px; margin-right: 5px;float: left;">
            <a href="<?php echo U('System/Admin/logout');?>" class="verifySubmit loginSubmit" style="width: 106px;float: left;background: #ea5353 ">退出</a>
        </div>
        <div class="rows">
            <p class="versionNumber" style="height: 20px;">由于系统检测到登录地异常，请输入短信异常验证码验证！</p>
        </div>
    </div>
</div>

<script src="/Public/js/jquery-1.9.1.min.js"></script>
<script src="/Public/js/common_ajax.js"></script>
<script src="/Public/js/layer/layer.js"></script>
<script>
    $('.verifySubmit').click(function(){
        var data = {
            verifyCode : $(':input[name="verifyCode"]').val()
        };
        common_ajax2(data);
    });
</script>
</body>
</html>