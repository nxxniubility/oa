<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
    <link rel="stylesheet" href="/Public/css/common.css">
    <link rel="stylesheet" href="/Public/css/updateDetail.css">
</head>
<body>
<div class="wrapBox">
    <div class="proCont">
        <div class="proContTop clearfix">
            <div class="topTit l">
                <span class="masterList">系统更新详情</span>
            </div>
            <div class="topRight r">
                <a href="javascript:void(0);" class="return">返回</a>
            </div>
        </div>
    </div>

    <div class="w900">
        <div class="head">客户管理系统更新说明</div>
        <div class="main">创建时间：<?php echo (date('Y-m-d H:i:s',$updateItem["createtime"])); ?> 创建者: <?php echo ($updateItem["realname"]); ?></div>
        <div class="foot"><?php echo (htmlspecialchars_decode($updateItem["upbody"])); ?> </div>
    </div>

</div>

<script src="/Public/js/jquery-1.9.1.min.js"></script>
<script src="/Public/js/main.js"></script>


</body>
</html>