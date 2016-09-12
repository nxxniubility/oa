<?php if (!defined('THINK_PATH')) exit();?><!-- 系统首页index -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit|ie-stand" />
<meta http-equiv="X-UA-Compatible" content="IE=8">
<title>OA后台系统首页<?php echo ($siteinfo["sitename"]); ?></title>
<link rel="stylesheet" href="/Public/css/common.css"/>

</head>
<body style="overflow: hidden">
<!DOCTYPE html>
<html>
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
	    <link rel="stylesheet" href="/Public/css/common.css">
	    <link rel="stylesheet" href="/Public/css/external.min.css">
	</head>
	<body>
		<div class="header clearfix <?php echo ($engagedStatus['status']==1)?'redBg':'';?>" style="min-width: 1250px">
		    <div class="logo l">
		        <a href="javascript:;">
		            <img src="/Public/images/newSys_header-01.png" alt="Logo">
		        </a>
		    </div>
		    <ul class="nav l">
		        <li><a href="<?php echo U('system/Index/index');?>">系统主页</a></li>
		        <li><a href="http://www.zelininfo.com/" target="_blank">网站主页</a></li>
		    </ul>
		    <div class="headSearch l">
		        <div class="inpBox clearfix">
                    <form action=""  method="get"  id="search_form"  target="main">
                    	<select class="headSelect l" name="keyname">
                    		<option value="username" selected="selected">手机号码</option>
                    		<option value="qq">QQ</option>
							<option value="tel">固定电话</option>
							<option value="user_id">用户ID</option>
                    	</select>
			            <input type="text" name="search" class="headSearchInp l" placeholder="请输入手机号码">
			            <a href="javascript:;" class="headSearchBtn l">搜索</a>
                    </form>

		        </div>
		    </div>
		    <ul class="userinfo r">
		        <li class="userinfo logout_parent">
					<?php echo ($userinfo["realname"]); ?>
					(
					<?php if(is_array($system_user_role)): foreach($system_user_role as $k=>$v): if($k > 0): ?><a href="javascript:;">/<?php echo ($v["name"]); ?></a>
							<?php else: ?>
							<a href="javascript:;"><?php echo ($v["name"]); ?></a><?php endif; endforeach; endif; ?>
					)
		            <div class="logout">
		                <a href="<?php echo ($user_logout_url); ?>">退出</a>
		            </div>
		            <div class="peopleStatus">
						<?php if($engagedStatus['status'] == 1): ?><span class="xian" style="display: none"><img src="/Public/images/peopleStatus_01-01.png"></span>
							<span class="mang"><img src="/Public/images/peopleStatus_01-02.png"></span>
						<?php else: ?>
							<span class="xian"><img src="/Public/images/peopleStatus_01-01.png"></span>
							<span class="mang" style="display: none"><img src="/Public/images/peopleStatus_01-02.png"></span><?php endif; ?>
		            	<!--<span class="chao">30</span>-->
		            </div>
		        </li>
		        <!--<li style="min-width: 70px;text-align: center">-->
					<!--<?php if(is_array($system_user_role)): foreach($system_user_role as $k=>$v): ?>-->
						<!--<?php if($k > 0): ?>-->
							<!--<a href="javascript:;">/<?php echo ($v["name"]); ?></a>-->
							<!--<?php else: ?>-->
							<!--<a href="javascript:;"><?php echo ($v["name"]); ?></a>-->
						<!--<?php endif; ?>-->
					<!--<?php endforeach; endif; ?>-->
		        <!--</li>-->
		        <li><a href="javascript:;">在线反馈</a></li>
		        <li class="message">
		            <a href="javascript:;">
		                <span id="poll_total_msg">0</span>
		            </a>
		            <div class="msgbody">
		                <div class="msg_con" id="poll_msg_con">
		                    <div class="msgtype">
		                        <p class="msgtype_title">通知</p>

		                        <div class="msgtype_list">
		                            <p>
		                                <a href="javascript:;">李xx 今天上门了 请贺静立即来前台接待</a>
		                                <span>15:30:03</span>
		                            </p>

		                            <p>
		                                <a href="javascript:;">李xx 今天上门了 请贺静立即来前台接待</a>
		                                <span>15:30:03</span>
		                            </p>
		                            <p>
		                                <a href="javascript:;">李xx 今天上门了 请贺静立即来前台接待</a>
		                                <span>15:30:03</span>
		                            </p>
		                        </div>

		                    </div>
		                </div>
		                <div class="msg_bottom">
		                    <a href="javascript:;" id="checkMore">查看更多</a>
		                </div>
		            </div>
		        </li>
		    </ul>
		</div>

		<div class="contentBox">
		    <div class="sidebarbox l">
		        <div class="sidebar">
		            <div class="sidebar_btn"><i></i></div>
		            <?php if(is_array($sidebar)): foreach($sidebar as $key=>$sidebarItem): ?><div class="sideber_nav">
		                    <div class="sideber_title sideber_title_on">
		                        <span class="sideber_title_icon l <?php if( $sidebar["open"] == 0): ?>sideber_title_icon_on<?php endif; ?>"></span>
		                        <span class="sidebar_manage l"><?php echo ($sidebarItem["title"]); ?></span>
		                        <span class="icon_setup r"></span>
		                    </div>
		                    <ul class="sideber_trans" style="display:none">
		                        <?php if(is_array($sidebarItem["children"])): foreach($sidebarItem["children"] as $key=>$child): ?><li><a href="<?php echo ($child["url"]); ?>" target="main"><span class="link_icon <?php echo ($child["icon"]); ?>"></span><span
		                                    class="nav-title"><?php echo ($child["title"]); ?></span></a></li><?php endforeach; endif; ?>
		                    </ul>
		                </div><?php endforeach; endif; ?>
		        </div>
		    </div>
		<script src="/Public/js/jquery-1.9.1.min.js"></script>
		<script src="/Public/js/jquery.lib.min.js"></script>

		<script>
			$("#checkMore").click(function(){
				alert('功能未开通,暂无任何消息');
			});
		</script>
	<!--<div class="sidebarbox l">
    <div class="sidebar">
        <div class="sidebar_btn"></div>
        <?php if(is_array($sidebar)): foreach($sidebar as $key=>$sidebar): ?><div class="sideber_nav">
        	<div class="sideber_title sideber_title_on">
            	<span class="sideber_title_icon l <?php if($sidebar["open"] == 0): ?>sideber_title_icon_on<?php endif; ?>"></span>
                <span class="sidebar_manage l"><?php echo ($sidebar["title"]); ?></span>
                <span class="icon_setup r"></span>
            </div>
            <ul class="sideber_trans" style="display:none">
            	<?php if(is_array($sidebar["children"])): foreach($sidebar["children"] as $key=>$child): ?><li><a href="<?php echo ($child["url"]); ?>" target="main"><span class="link_icon"></span><span class="nav-title"><?php echo ($child["title"]); ?></span></a></li><?php endforeach; endif; ?>
            </ul>
        </div><?php endforeach; endif; ?>
	</div>
</div>-->
    <iframe class="content" id="main" name="main" src="<?php echo ($system_index_main_url); ?>" frameBorder=0>
    </iframe>

	<script language="javascript" type="text/javascript" src="/Public/js/jquery-1.9.1.min.js"></script>
	<script language="javascript" type="text/javascript" src="/Public/js/jquery.mousewheel.js"></script>
	<script language="javascript" type="text/javascript" src="/Public/js/header.js"></script>
</div>


<div class="dn">

	<!-- 闲 S -->
	<div class="xianBox popup" id="xian">
		<p>我正在忙，需要处理其他工作事项（设置状态为忙线）</p>
		<div class="btnBox clearfix">
			<input type="button" class="xDefine l" onclick="editStatus(1)" value="确定">
			<input type="button" class="xCancel l" value="取消">
		</div>
	</div>
	<!-- 闲 E -->

	<!-- 闲 S -->
	<div class="mangBox popup" id="mang">
		<p>我有空，设置状态为空闲状态</p>
		<div class="btnBox clearfix">
			<input type="button" class="mDefine l" onclick="editStatus(2)" value="确定">
			<input type="button" class="mCancel l" value="取消">
		</div>
	</div>
	<!-- 闲 E -->

	<!-- 忙状态超过30分钟 S -->
	<div class="chaoBox popup" id="chao">
		<p>你处于忙线状态<span class="code2_msg"></span></p>
		<p>请问可以更换为闲线状态吗？</p>
		<div class="btnBox clearfix">
			<input type="button" class="chDefine l" onclick="editStatus(2)" value="确定">
			<input type="button" class="chCancel l" value="取消">
		</div>
	</div>
	<!-- 忙状态超过30分钟 E <x></x>
	<!-- 客户到访提醒 S -->
	<div class="visitReminds popup" id="automatic">
		<div class="vsTips">客户已到访，请到前台接待</div>
		<div class="vsRow clearfix">
			<span>真实姓名：</span>
			<em ></em>
		</div>
		<div class="vsRow clearfix">
			<span>手机号码：</span>
			<em></em>
		</div>
		<div class="vsRow clearfix">
			<span>固定电话：</span>
			<em></em>
		</div>
		<div class="vsRow clearfix">
			<span>QQ：</span>
			<em></em>
		</div>
		<a href="javascript:;" class="vsJum">查看客户信息</a>
	</div>
	<!-- 客户到访提醒 E -->
</div>


<script language="javascript" type="text/javascript" src="/Public/js/jquery-1.8.3.min.js"></script>
<script language="javascript" type="text/javascript" src="/Public/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="/Public/js/artDialog/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript" src="/Public/js/artDialog/plugins/iframeTools.js"></script>
<script src="/Public/js/jquery.lib.min.js"></script>
<script type="text/javascript" language="JavaScript" src="/Public/js/peopleStatus.js"></script>
<script language="javascript" type="text/javascript" src="/Public/js/common.js"></script>
<script src="/Public/js/layer/layer.js"></script>
<script type="text/javascript">
	var ajax_url = "<?php echo U('System/Index/index');?>";
	var detailUser_url = "<?php echo U('System/User/detailUser');?>";
	var time_ajax = setInterval(getEngagedStatus_ajax,20000);
	$(function(){
		$(".headSearchBtn").click(function(){
			var keywords=$.trim($("input.headSearchInp").val());
			if(keywords.length==0){
				layer.msg('请输入搜索内容', {icon:2});
				return false;
			}
			var fr=$(this).parent("#search_form");
			fr.attr("action",detailUser_url);
			//fr.attr('action','/System/User/detailUser/id/200022.html');
			fr.submit();
		});
		$(':input[name="keyname"]').change(function(){
			if($(this).val()=='username'){
				$(':input[name="search"]').attr('placeholder','请输入手机号码');
			}else if($(this).val()=='qq'){
				$(':input[name="search"]').attr('placeholder','请输入QQ号码');
			}else if($(this).val()=='tel'){
				$(':input[name="search"]').attr('placeholder','请输入固定电话');
			}else if($(this).val()=='user_id'){
				$(':input[name="search"]').attr('placeholder','请输入用户ID');
			};
		});
	});
</script>
</body>
</html>