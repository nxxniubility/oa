<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
		<link rel="stylesheet" href="/Public/css/login.css" />
	</head>
	<body class="bodyBg">
		<div class="sysWrap">
			<div class="activationBox">
				<div class="logoPic"></div>
				<div class="rows">
					<input type="text" class="nameInp" placeholder="请输入手机号" name="username">
					<i class="nameIcon"></i>
				</div>
				<div class="rows clearfix">
					<div class="aboxLeft">
						<input type="tel" class="activationCodes" maxlength="6" placeholder="请输入手机验证码" name="phoneverify">
						<i class="activationIcon"></i>
					</div>
					<div class="aboxRight" id="getCode">获取验证码</div>
					<div class="aboxRight countdown" style="display: none;">等待60秒</div>
				</div>
				<div class="rows">
					<input type="password" class="pwdInp" placeholder="请输入密码" name="password">
					<i class="pwdIcon"></i>
				</div>
				<div class="rows">
					<input type="password" class="pwdInp" placeholder="请输入确认密码" name="confirmPassword">
					<i class="pwdIcon"></i>
				</div>
				<div class="rows">
					<input type="button" class="acSubmit" value="激活">
				</div>
				<div class="rows">
					<p class="already">已经有账号？<a href="<?php echo ($url_login); ?>">马上登录</a></p>
				</div>
				<div class="rows">
					<p class="versionNumber2">版本号v<?php echo ($version); ?></p>
				</div>
			</div>
		</div>
		<script  type="text/javascript"  src="/Public/js/jquery-1.9.1.min.js"></script>
<script  type="text/javascript"  src="/Public/js/common_two.js"></script>
<script  type="text/javascript"  src="/Public/js/login.js"></script>

		<script src="/Public/js/layer/layer.js"></script>
		<script src="/Public/js/layer/placeholder.js"></script>

		<script>
			$(function(){
				$('.acSubmit').click(function(){
					var data = {
						username:$(':input[name="username"]').val(),
						password:$(':input[name="password"]').val(),
						confirmPassword:$(':input[name="confirmPassword"]').val(),
						phoneverify:$(':input[name="phoneverify"]').val()
					};
					common_ajax(data);
				});

				/*点击验证码框事件*/
				$('#getCode').click(function(){
					var data = {
						username:$(':input[name="username"]').val(),
						smsType:'activate'
					};
					common_ajax(data,"<?php echo U('System/Admin/randVerifyCode');?>",'no',codeTime);
				});
			});

			//配置定时器
			function codeTime(){
				var thisObj = $('#getCode');
				var lodingObj = $('.countdown');
				var sh;
				var time = 60;
				thisObj.hide();
				lodingObj.show();
				setInterval(codeInterval,1000);
				function codeInterval(){
					if(time!=0){
						var str = '等待'+time+'秒';
						lodingObj.html(str);
						time = time-1;
						return time;
					}else{
						thisObj.show();
						lodingObj.hide();
						clearInterval(sh);
					}
				}
			}
		</script>
		<script src="/Public/js/common_ajax.js"></script>

	</body>
</html>