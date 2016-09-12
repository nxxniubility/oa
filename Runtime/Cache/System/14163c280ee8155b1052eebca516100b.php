<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
    <link rel="stylesheet" href="/Public/css/common.css">
    <link rel="stylesheet" href="/Public/css/home.css">
</head>
<body>
<style>
    .on_btn {background: #53B567;}
    .on_btn a {color: #fff;}
</style>
<div class="wrapBox">
    <div class="feCont">
        <div class="feContTop clearfix">
            <div class="topTit l">
                <span class="masterList">系统首页 </span>
            </div>
            <div class="topRight r">
                <?php if($system_user_role_id==C('ADMIN_SUPER_ROLE')): ?><a href="<?php echo U(updateRecord);?>" class="return">设置系统更新</a><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="shortcutButton">
        <div class="sbTop clearfix">
            <span>快捷按钮</span>
            <i title="自定义快捷按钮">···</i>
        </div>
        <div class="sbCont">
            <div class="sbContBd">
                <ul class="clearfix">
                    <?php if(is_array($default_nodes)): foreach($default_nodes as $k=>$defineBtn): ?><li>
                            <a href="<?php echo ($defineBtn["url"]); ?>" class="<?php echo $navClass[$k]; ?>"><?php echo ($defineBtn["title"]); ?></a>
                        </li><?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="msgBox clearfix">
        <div class="systemUpdate">
            <div class="msgBoxTop clearfix">
                <span>系统更新</span>
                <em onclick="location.href='<?php echo U(updateRecord);?>'">更多&gt;&gt;</em>
            </div>

            <ul class="msgBoxCont clearfix">
                <?php if(is_array($sysUpdateData)): foreach($sysUpdateData as $k=>$update_item): ?><li>
                        <a href="<?php echo U('updateDetail',array('system_update_id'=>$update_item['system_update_id']));?>">
                            <?php if($k < 5): ?><div class="msgNum"><?php echo ($k+1); ?></div>
                                <div class="msgHint"><?php echo ($update_item["uptitle"]); ?></div>
                                <!-- <div class="msgDate"><?php echo (date('Y-m-d H:m:s',$update_item["createtime"])); ?></div>-->
                                <div class="msgDate"><?php echo substr($update_item['createtime'],0,10);?></div><?php endif; ?>

                        </a>
                    </li><?php endforeach; endif; ?>
            </ul>
        </div>
        <div class="announcement">
            <div class="msgBoxTop">
                <span>通知公告</span>
                <em onclick="location.href='javascript:;'" id="informMore">更多&gt;&gt;  </em>
            </div>
            <ul class="msgBoxCont clearfix">
               <!-- <li>
                    <a href="javascript:;">
                        <div class="msgIcon"></div>
                        <div class="msgHint">通知消息xxxxxxxs</div>
                        <div class="msgDate">2015-4-25</div>
                    </a>
                </li>-->
            </ul>
        </div>
    </div>
</div>

<div class="sbBox">
    <div class="sbBoxTop clearfix">
        <span>自定义快捷按钮</span>
        <i class="sbClose"></i>
    </div>
    <div class="sbDrag">
        <h4>拖动区块调整显示顺序</h4>
        <ul class="clearfix" id="show_node">
            <?php if(is_array($default_nodes)): foreach($default_nodes as $k=>$defineBtn): ?><li class="show_node_<?php echo ($defineBtn["id"]); ?>" node_id="<?php echo ($defineBtn["id"]); ?>">
                    <?php echo ($defineBtn["title"]); ?>
                </li><?php endforeach; endif; ?>

        </ul>
    </div>
    <div class="sbDisplayBtn">
        <h3>选择需要显示的按钮</h3>
        <div class="btnBox">
            <?php if(is_array($siderbar)): foreach($siderbar as $key=>$siderItem): ?><div class="itemBox">
                    <h4><?php echo ($siderItem["title"]); ?></h4>
                    <ul class="childNodes" id="child_nodes">
                        <?php if(is_array($siderItem["children"])): foreach($siderItem["children"] as $key=>$childItem): ?><li>
                                <a class="li_<?php echo ($childItem['id']); ?> node_btn <?php echo (in_array($childItem['id'],$in_array))?'on_btn':''; ?>"
                                   node_id="<?php echo ($childItem["id"]); ?>" name='childNode'
                                   href="javascript:;"><?php echo ($childItem["title"]); ?></a>
                            </li><?php endforeach; endif; ?>
                    </ul>
                </div><?php endforeach; endif; ?>
        </div>
    </div>
    <div class="sbBtnBox clearfix">
        <input type="submit" class="submitBtn" value="提交">
        <input type="button" class="resetBtn" value="重置">
    </div>
</div>

<script src="/Public/js/jquery-1.9.1.min.js"></script>
<script src="/Public/js/layer/layer.js"></script>
<script src="/Public/js/common_ajax.js"></script>
<script src="/Public/js/home.js"></script>
<script type="text/javascript" src="/Public/js/jquery.sortable.js"></script>

<script>
    $('#show_node').sortable();
    $('#informMore').click(function(){
            alert("功能未开通,暂无任何通告.");
    });
</script>

<script>
    //用户初始化自定义配置
    var json_arr = <?php echo json_encode($default_nodes);?>;

</script>
</body>
</html>