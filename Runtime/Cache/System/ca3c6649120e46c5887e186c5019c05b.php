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
			<div class="loginBox">
				<div class="logoPic"></div>
					<div class="rows">
						<input type="text" class="nameInp" placeholder="请输入手机号" name="username">
						<i class="nameIcon"></i>
					</div>
					<div class="rows">
						<input type="password" class="pwdInp" placeholder="请输入密码" name="password">
						<i class="pwdIcon"></i>
					</div>
					<div class="rows clearfix">
						<input type="text" class="codes" maxlength="4" name="verification">
						<div class="codesBox">
							<div class="codesCont">
							<img id="verify_img" src="<?php echo ($verify); ?>" onclick="this.src='<?php echo ($verify); ?>?'+Math.random();" alt="验证码"></div>
							<span onclick="document.getElementById('verify_img').src='<?php echo ($verify); ?>?'+Math.random();">看不清楚?换一张</span>
						</div>
					</div>
					<div class="rows">
						<input type="button" class="loginSubmit" value="登录">
					</div>
				<div class="rows">
					<p class="already">账号未激活？<a href="<?php echo ($url_activation); ?>">马上激活</a></p>
				</div>
					<div class="rows">
						<p class="versionNumber">版本号v<?php echo ($version); ?></p>
					</div>
			</div>
		</div>

		<script src="/Public/js/jquery-1.9.1.min.js"></script>
		<script src="/Public/js/common_two.js"></script>
		<script src="/Public/js/layer/layer.js"></script>
		<script src="/Public/js/login.js"></script>

	</body>
</html>