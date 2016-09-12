<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
    <title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
    <link rel="stylesheet" href="/Public/css/common.css?v=201609031">
    <link rel="stylesheet" href="/Public/css/ClientList.css?v=201609031">
    <link rel="stylesheet" href="/Public/css/external.min.css?v=201609031">
    <link rel="stylesheet" href="/Public/js/glDatePicker/glDatePicker.default.css">
    <script src="/Public/js/jquery-1.9.1.min.js"></script>
    <style>
    	.tr_title,.tr_content{min-width: 85px;text-align: center;}
		.iconBox div{float: right !important;}
    </style>
    <script>
        //创建订单
        var createOrder_href = "<?php echo U('System/User/createOrder');?>";
        //放弃
        var abandonUser_href = "<?php echo U('System/User/abandonUser');?>";
        //转出
        var allocationUser_href = "<?php echo U('System/User/allocationUser');?>";
        //出库
        var restartUser_href = "<?php echo U('System/User/restartUser');?>";
        //设置重点
        var editUserMark_href = "<?php echo U('System/User/editUserMark');?>";
        //设置自定义显示列
        var editColumn_href = "<?php echo U('System/User/editColumn');?>";
        //确认到访
        var affirmVisit_href = "<?php echo U('System/User/affirmVisit');?>";
        //发送短信
        var sendSms_href = "<?php echo U('System/User/sendSms');?>";

        var myname = "<?php echo $system_user['realname'];?>";
        var myphone = "<?php echo decryptPhone($system_user['username'],C('PHONE_CODE_KEY'));?>";
        //双击
        $(document).on('dblclick', ".content_li", function(){
            var el = $(this).find('.hrefDetail');
            $('#hrefForm').attr('action', el.attr('href')).submit();
        });
    </script>
</head>
<body>

<div class="wrapBox">
    <div class="orgCont">
        <div class="orgContTop clearfix">
            <div class="topTit l"><span class="masterList">客户列表</span></div>
            <div class="topRight r">
            	<a href="<?php echo U('System/User/orderList');?>" class="orderJump">订单列表</a>
                <?php if(!empty($access_list['ADDUSER'])): ?><a href="<?php echo U('System/User/addUser');?>" class="newDepartMent">添加客户</a><?php endif; ?>
                <?php if(!empty($access_list['IMPORTUSER'])): ?><a href="<?php echo U('System/User/importUser/type/2');?>" class="newDepartMent">导入客户数据</a><?php endif; ?>
                <?php if(!empty($access_list['IMPORTUSERLIBRARY'])): ?><a href="<?php echo U('System/User/importUserLibrary/type/2');?>" class="return">导入中心客户</a><?php endif; ?>
            </div>
        </div>

        <div class="p clearfix">快速筛选:</div>
        <div class="Filter">
            <div class="frame">
                <div class="details">
                    <span>客户状态：</span>
                    <ul data-id="status">
                    	<li><a href="javascript:;" data-value="0" <?php echo $data['request']['status']==0?'class="on_hover"':'';?>>全部</a></li>
                        <li><a href="javascript:;" data-value="20" <?php echo $data['request']['status']==20?'class="on_hover"':'';?>>待联系</a></li>
                        <li><a href="javascript:;" data-value="30" <?php echo $data['request']['status']==30?'class="on_hover"':'';?>>待跟进</a></li>
                        <li><a href="javascript:;" data-value="70" <?php echo $data['request']['status']==70?'class="on_hover"':'';?>>交易</a></li>
                    </ul>
                </div>
                <div class="details">
                    <span>信息质量：</span>
                    <ul data-id="infoquality">
                        <li><a href="javascript:;" data-value="0" <?php echo $data['request']['infoquality']==0?'class="on_hover"':'';?>>全部</a></li>
                        <li><a href="javascript:;" data-value="1" <?php echo $data['request']['infoquality']==1?'class="on_hover"':'';?>>A</a></li>
                        <li><a href="javascript:;" data-value="2" <?php echo $data['request']['infoquality']==2?'class="on_hover"':'';?>>B</a></li>
                        <li><a href="javascript:;" data-value="3" <?php echo $data['request']['infoquality']==3?'class="on_hover"':'';?>>C</a></li>
                        <li><a href="javascript:;" data-value="4" <?php echo $data['request']['infoquality']==4?'class="on_hover"':'';?>>D</a></li>
                    </ul>
                </div>
                <div class="details">
                    <span>重点标记客户：</span>
                    <ul data-id="mark">
                        <li><a href="javascript:;" data-value="0" <?php echo $data['request']['mark']==0?'class="on_hover"':'';?>>全部</a></li>
                        <li><a href="javascript:;" data-value="1" <?php echo $data['request']['mark']==1?'class="on_hover"':'';?>>普通客户</a></li>
                        <li><a href="javascript:;" data-value="2" <?php echo $data['request']['mark']==2?'class="on_hover"':'';?>>重点客户</a></li>
                    </ul>
                </div>
                <div class="details">
                    <span>最近跟进结果：</span>
                    <ul data-id="attitude_id">
                        <li><a href="javascript:;" data-value="0" <?php echo $data['request']['attitude_id']==0?'class="on_hover"':'';?>>全部</a></li>
                        <?php if(is_array($data['attitude'])): foreach($data['attitude'] as $k=>$v): ?><li><a href="javascript:;" data-value="<?php echo ($v["num"]); ?>" <?php echo $data['request']['attitude_id']==$v['num']?'class="on_hover"':'';?>><?php echo ($v["text"]); ?></a></li><?php endforeach; endif; ?>
                    </ul>
                </div>
                <div class="details">
                    <span>最近回访：</span>
                    <?php $getlastvisit = explode('@',$data['request']['lastvisit']); ?>
                    <ul data-id="lastvisit">
                        <li><a href="javascript:;" data-value="1@0" <?php echo empty($getlastvisit[0]) || $getlastvisit[0]==1?'class="on_hover"':'';?>>全部</a></li>
                        <li><a href="javascript:;" data-value="2@<?php echo date('Y-m-d',time()).'@time';?>" <?php echo $getlastvisit[0]==2?'class="on_hover"':'';?>>今日已回访</a></li>
                        <li><a href="javascript:;" data-value="3@<?php echo date('Y-m-d',strtotime('-1 day')).'@'.date('Y-m-d',strtotime('-1 day'));?>" <?php echo $getlastvisit[0]==3?'class="on_hover"':'';?>>昨日已回访</a></li>
                        <li><a href="javascript:;" data-value="4@<?php echo date('Y-m-d',strtotime('-7 day')).'@time';?>" <?php echo $getlastvisit[0]==4?'class="on_hover"':'';?>>一周内已回访</a></li>
                        <li class="clickli"><a href="javascript:;" class="<?php echo ($getlastvisit[0]==5)?'on_hover':'';?> false" data-num="5">自定义时间段</a></li>
                        <li class="selectbox1 employeeStatus l start" <?php echo ($getlastvisit[0]==5)?'style="display: block; visibility: visible;"':'';?> >
                            <input type="text" class="afTime" value="<?php echo ($getlastvisit[1]); ?>" placeholder="开始时间">
                        </li>
                        <li class="selectbox1 employeeStatus l end" <?php echo ($getlastvisit[0]==5)?'style="display: block; visibility: visible;"':'';?>>
                            <input type="text" class="afTime endTiem" value="<?php echo ($getlastvisit[2]); ?>" placeholder="结束时间" autocomplete="off">
                        </li>
                    </ul>
                </div>
                <div class="details">
                    <span>下次回访/承诺到访：</span>
                    <?php $getnextvisit = explode('@',$data['request']['nextvisit']); ?>
                    <ul data-id="nextvisit">
                        <li><a href="javascript:;" data-value="1@0" <?php echo empty($getnextvisit[0]) || $getnextvisit[0]==1?'class="on_hover"':'';?>>全部</a></li>
                        <li><a href="javascript:;" data-value="6@<?php echo date('Y-m-d',strtotime(date('Y-m-d',time()))).'@'.date('Y-m-d',strtotime(date('Y-m-d',time())));?>" <?php echo $getnextvisit[0]==6?'class="on_hover"':'';?>>今日回访</a></li>
                        <li><a href="javascript:;" data-value="2@<?php echo date('Y-m-d',strtotime('+1 day')).'@'.date('Y-m-d',strtotime('+1 day'));?>" <?php echo $getnextvisit[0]==2?'class="on_hover"':'';?>>明日回访</a></li>
                        <li><a href="javascript:;" data-value="3@<?php echo date('Y-m-d',strtotime('+1 day')).'@'.date('Y-m-d',strtotime('+3 day'));?>" <?php echo $getnextvisit[0]==3?'class="on_hover"':'';?>>未来三日内</a></li>
                        <li><a href="javascript:;" data-value="4@<?php echo date('Y-m-d',strtotime('+1 day')).'@'.date('Y-m-d',strtotime('+7 day'));?>" <?php echo $getnextvisit[0]==4?'class="on_hover"':'';?>>未来一周内</a></li>
                        <li class="clickli"><a href="javascript:;" class="<?php echo ($getnextvisit[0]==5)?'on_hover':'';?> false" data-num="5">自定义时间段</a></li>
                        <li class="selectbox1 employeeStatus l start" <?php echo ($getnextvisit[0]==5)?'style="display: block; visibility: visible;"':'';?> >
                        <input type="text" class="afTime" value="<?php echo ($getnextvisit[1]); ?>" placeholder="开始时间">
                        </li>
                        <li class="selectbox1 employeeStatus l end" <?php echo ($getnextvisit[0]==5)?'style="display: block; visibility: visible;"':'';?>>
                        <input type="text" class="afTime endTiem" value="<?php echo ($getnextvisit[2]); ?>" placeholder="结束时间" autocomplete="off">
                        </li>
                    </ul>
                </div>
                <div class="details">
                    <span>实际到访：</span>
                    <?php $getvisittime = explode('@',$data['request']['visittime']); ?>
                    <ul data-id="visittime">
                        <li><a href="javascript:;" data-value="1@0" <?php echo empty($getvisittime[0]) || $getvisittime[0]==1?'class="on_hover"':'';?>>全部</a></li>
                        <li><a href="javascript:;" data-value="2@<?php echo date('Y-m-d',time()).'@time';?>" <?php echo $getvisittime[0]==2?'class="on_hover"':'';?>>今日到访</a></li>
                        <li><a href="javascript:;" data-value="3@<?php echo date('Y-m-d',strtotime('-3 day')).'@time';?>" <?php echo $getvisittime[0]==3?'class="on_hover"':'';?>>三日内到访</a></li>
                        <li><a href="javascript:;" data-value="4@<?php echo date('Y-m-d',strtotime('-7 day')).'@time';?>" <?php echo $getvisittime[0]==4?'class="on_hover"':'';?>>一周内到访</a></li>
                        <li class="clickli"><a href="javascript:;" class="<?php echo ($getvisittime[0]==5)?'on_hover':'';?> false" data-num="5">自定义时间段</a></li>
                        <li class="selectbox1 employeeStatus l start" <?php echo ($getvisittime[0]==5)?'style="display: block; visibility: visible;"':'';?> >
                        <input type="text" class="afTime" value="<?php echo ($getvisittime[1]); ?>" placeholder="开始时间">
                        </li>
                        <li class="selectbox1 employeeStatus l end" <?php echo ($getvisittime[0]==5)?'style="display: block; visibility: visible;"':'';?>>
                        <input type="text" class="afTime endTiem" value="<?php echo ($getvisittime[2]); ?>" placeholder="结束时间" autocomplete="off">
                        </li>
                    </ul>
                </div>
                <div class="details">
                    <span>分配时间：</span>
                    <?php $getallocationtime = explode('@',$data['request']['allocationtime']); ?>
                    <ul data-id="allocationtime">
                        <li><a href="javascript:;" data-value="1@0" <?php echo empty($getallocationtime[0]) || $getallocationtime[0]==1?'class="on_hover"':'';?>>全部</a></li>
                        <li><a href="javascript:;" data-value="2@<?php echo date('Y-m-d',time()).'@time';?>" <?php echo $getallocationtime[0]==2?'class="on_hover"':'';?>>今日分配</a></li>
                        <li><a href="javascript:;" data-value="3@<?php echo date('Y-m-d',strtotime('-1 day')).'@'.date('Y-m-d',strtotime('-1 day'));?>" <?php echo $getallocationtime[0]==3?'class="on_hover"':'';?>>昨日分配</a></li>
                        <li><a href="javascript:;" data-value="4@<?php echo date('Y-m-d',strtotime('-7 day')).'@time';?>" <?php echo $getallocationtime[0]==4?'class="on_hover"':'';?>>一周内分配</a></li>
                        <li class="clickli"><a href="javascript:;" class="<?php echo ($getallocationtime[0]==5)?'on_hover':'';?> false" data-num="5">自定义时间段</a></li>
                        <li class="selectbox1 employeeStatus l start" <?php echo ($getallocationtime[0]==5)?'style="display: block; visibility: visible;"':'';?> >
                        <input type="text" class="afTime" value="<?php echo ($getallocationtime[1]); ?>" placeholder="开始时间">
                        </li>
                        <li class="selectbox1 employeeStatus l end" <?php echo ($getallocationtime[0]==5)?'style="display: block; visibility: visible;"':'';?>>
                        <input type="text" class="afTime endTiem" value="<?php echo ($getallocationtime[2]); ?>" placeholder="结束时间" autocomplete="off">
                        </li>
                    </ul>
                </div>
                <div class="details">
                    <span>出库时间：</span>
                    <?php $getupdatetime = explode('@',$data['request']['updatetime']); ?>
                    <ul data-id="updatetime">
                        <li><a href="javascript:;" data-value="1@0" <?php echo empty($getupdatetime[0]) || $getupdatetime[0]==1?'class="on_hover"':'';?>>全部</a></li>
                        <li><a href="javascript:;" data-value="2@<?php echo date('Y-m-d',time()).'@time';?>" <?php echo $getupdatetime[0]==2?'class="on_hover"':'';?>>今日出库</a></li>
                        <li><a href="javascript:;" data-value="3@<?php echo date('Y-m-d',strtotime('-1 day')).'@'.date('Y-m-d',strtotime('-1 day'));?>" <?php echo $getupdatetime[0]==3?'class="on_hover"':'';?>>昨日出库</a></li>
                        <li><a href="javascript:;" data-value="4@<?php echo date('Y-m-d',strtotime('-7 day')).'@time';?>" <?php echo $getupdatetime[0]==4?'class="on_hover"':'';?>>一周内出库</a></li>
                        <li class="clickli"><a href="javascript:;" class="<?php echo ($getupdatetime[0]==5)?'on_hover':'';?> false" data-num="5">自定义时间段</a></li>
                        <li class="selectbox1 employeeStatus l start" <?php echo ($getupdatetime[0]==5)?'style="display: block; visibility: visible;"':'';?> >
                        <input type="text" class="afTime" value="<?php echo ($getupdatetime[1]); ?>" placeholder="开始时间">
                        </li>
                        <li class="selectbox1 employeeStatus l end" <?php echo ($getupdatetime[0]==5)?'style="display: block; visibility: visible;"':'';?>>
                        <input type="text" class="afTime endTiem" value="<?php echo ($getupdatetime[2]); ?>" placeholder="结束时间" autocomplete="off">
                        </li>
                    </ul>
                </div>
                <div class="details">
                    <span>意向课程：</span>
                    <ul data-id="course_id">
                        <li><a href="javascript:;" data-value="0" <?php echo $data['request']['course_id']==0?'class="on_hover"':'';?>>全部</a></li>
                        <?php if(is_array($data['courseAll'])): foreach($data['courseAll'] as $k=>$v): ?><li><a href="javascript:;" data-value="<?php echo ($v["course_id"]); ?>" <?php echo $data['request']['course_id']==$v['course_id']?'class="on_hover"':'';?>><?php echo ($v["coursename"]); ?></a></li><?php endforeach; endif; ?>
                    </ul>
                </div>
                <div class="details">
                    <span>学习平台：</span>
                    <ul data-id="learningtype">
                        <li><a href="javascript:;" data-value="0" <?php echo $data['request']['learningtype']==0?'class="on_hover"':'';?>>全部</a></li>
                        <?php if(is_array($data['learningtype'])): foreach($data['learningtype'] as $k=>$v): ?><li><a href="javascript:;" data-value="<?php echo ($v["num"]); ?>" <?php echo $data['request']['learningtype']==$v['num']?'class="on_hover"':'';?>><?php echo ($v["text"]); ?></a></li><?php endforeach; endif; ?>
                    </ul>
                </div>

                <div class="details">
                    <span>渠道：</span>
                    <select name="channel_sele" data-id="channel_id" autocomplete="off">
                        <option value="0" <?php echo $data['request']['channel_id']==0?'selected="selected"':'';?>>--全部渠道--</option>
                        <?php if(is_array($data['channel']['data'])): foreach($data['channel']['data'] as $k=>$v): ?><option value="<?php echo ($v["channel_id"]); ?>" <?php echo $data['request']['channel_id']==$v['channel_id']?'selected="selected"':'';?>><?php echo ($v["channelname"]); ?></option>
                            <?php if(!empty($v['children'])): if(is_array($v['children'])): foreach($v['children'] as $key=>$v2): ?><option value="<?php echo ($v2["channel_id"]); ?>" <?php echo $data['request']['channel_id']==$v2['channel_id']?'selected="selected"':'';?>>&nbsp;&nbsp;├─ <?php echo ($v2['channelname']); ?></option><?php endforeach; endif; endif; endforeach; endif; ?>
                    </select>
                </div>
                <div class="details lastDes">
                	<span>&nbsp;</span>
                    <form action="<?php echo U('System/User/userList');?>" method="get" id="subForm" target="main" onsubmit="return false;">
                        <input type="hidden" name="status" value="<?php echo ($data['request']['status']); ?>" autocomplete="off">
                        <input type="hidden" name="infoquality" value="<?php echo ($data['request']['infoquality']); ?>" autocomplete="off">
                        <input type="hidden" name="mark" value="<?php echo ($data['request']['mark']); ?>" autocomplete="off">
                        <input type="hidden" name="attitude_id" value="<?php echo ($data['request']['attitude_id']); ?>" autocomplete="off">
                        <input type="hidden" name="lastvisit" value="<?php echo ($data['request']['lastvisit']); ?>" autocomplete="off">
                        <input type="hidden" name="nextvisit" value="<?php echo ($data['request']['nextvisit']); ?>" autocomplete="off">
                        <input type="hidden" name="visittime" value="<?php echo ($data['request']['visittime']); ?>" autocomplete="off">
                        <input type="hidden" name="allocationtime" value="<?php echo ($data['request']['allocationtime']); ?>" autocomplete="off">
                        <input type="hidden" name="updatetime" value="<?php echo ($data['request']['updatetime']); ?>" autocomplete="off">
                        <input type="hidden" name="course_id" value="<?php echo ($data['request']['course_id']); ?>" autocomplete="off">
                        <input type="hidden" name="learningtype" value="<?php echo ($data['request']['learningtype']); ?>" autocomplete="off">
                        <input type="hidden" name="channel_id" value="<?php echo ($data['request']['channel_id']); ?>" autocomplete="off">
                        <input type="submit" class="confirmBtn" id="subSearch" value="确定">
                    </form>
                    <input type="submit" class="confirmBtn screenReset" id="clearSearch" value="重置">
                </div>
            </div>

            <div class="frame1">
                <ul></ul>
            </div>
        </div>

        <div class="arrowFather1">
            <div class="arrow1">
            	<span>展开筛选</span>
            	<i class="showIcon"></i>
            </div>
        </div>
        <div class="arrowFather">
            <div class="arrow">
            	<span>收起筛选</span>
            	<i class="hideIcon"></i>
            </div>
        </div>
        </form>
        <div class="proContTop clearfix">
            <div class="topLeft l">
                <form action="<?php echo U('System/User/userList');?>#main" method="get" target="main">
                    <select name="key_name" >
                        <option value="realname" <?php echo $data['request']['key_name']=='realname'?'selected="true"':'';?>>真实姓名</option>
                        <option value="username" <?php echo $data['request']['key_name']=='username'?'selected="true"':'';?>>手机号码</option>
                        <option value="qq" <?php echo $data['request']['key_name']=='qq'?'selected="true"':'';?>>QQ</option>
                        <option value="tel" <?php echo $data['request']['key_name']=='tel'?'selected="true"':'';?>>固定电话</option>
                    </select>
                    <input type="text" name="key_value" value="<?php echo ($data['request']['key_value']); ?>" placeholder="请输入关键词"/>
                    <button>搜索</button>
                </form>
            </div>

            <div class="topRight r">
                <a href="javascript:location.reload();" class="return refresh">刷新</a>
                <a href="javascript:;" id="userDefined" class="return">设置</a>
            </div>
        </div>
		
		<div class="promptText">鼠标左键双击客户信息打开对应的客户详情</div>

        <div id="pjax_userlist">
            <div class="proContMiddle clearfix" id="proListMid">
                <table cellpadding="0" cellspacing="0" width="40" id="table1">
                    <thead>
                    <tr><th>选中</th></tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($data['userAll'])): foreach($data['userAll'] as $k=>$v): ?><tr class="box_li">
                            <td>
                                <?php if((($v['status']) == 20) || (($v['status']) == 30)): ?><input type="checkbox" class="proListChk" name="librayChk" autocomplete="off" value="<?php echo ($v['user_id']); ?>"><?php endif; ?>
                            </td>
                        </tr><?php endforeach; endif; ?>
                    </tbody>
                </table>
                <div class="listBody"  id="table2_div">
                    <table cellpadding="0" cellspacing="0" width="" id="table2">
                        <thead>
                        <tr id="title">
                            <th class="tr_title" isShow="true"></th>
                            <th class="tr_title title_realname">真实姓名</th>
                            <th class="tr_title title_username">手机号码</th>
                            <th class="tr_title title_qq">QQ</th>
                            <th class="tr_title title_tel">固定电话</th>
                            <th class="tr_title title_infoquality">信息质量</th>
                            <th class="tr_title title_channelname">渠道</th>
                            <th class="tr_title title_status">状态</th>
                            <th class="tr_title title_system_user_id">所属人</th>
                            <th class="tr_title title_updateuser_id">出库人</th>
                            <th class="tr_title title_isvisittime">是否到访</th>
                            <th class="tr_title title_visittime">第一次到访时间</th>
                            <th class="tr_title title_allocationtime">分配时间</th>
                            <th class="tr_title title_lastvisit">最后回访</th>
                            <th class="tr_title title_nextvisit">下次回访/承诺到访</th>
                            <th class="tr_title title_attitude_id">最近跟进</th>
                            <th class="tr_title title_course_id">意向课程</th>
                            <th class="tr_title title_learningtype">学习平台</th>
                            <!-- <th class="tr_title title_reservetype">预报审核状态</th> -->
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($data['userAll'])): foreach($data['userAll'] as $k=>$v): ?><tr class="content_li" <?php echo ((time()-$v['createtime']) < 86400)?'style="color:green"':(($v['status']==30 && $v['nextvisit']!=0 && $v['lastvisit']!=0 && $v['lastvisit']<time() && $v['nextvisit']<time())?'style="color:red"':'');?>>
                            <td class="tr_content" isShow="true">
                                <div class="iconBox clearfix bodyr_<?php echo ($k); ?>"  data-value="<?php echo ($v["user_id"]); ?>" data-realname="<?php echo ($v["realname"]); ?>" data-qq="<?php echo ($v["qq"]); ?>" data-tel="<?php echo ($v["tel"]); ?>" data-username="<?php echo decryptPhone($v['username'],C('PHONE_CODE_KEY'));?>">
                                    <?php if((time()-$v['allocationtime']) < 86400): ?><div class="new"><img src="/Public/images/new-client.png" alt="新增" width="20" height="20"></div>
                                        <?php else: ?>
                                        <div class="new"><img src="/Public/images/new-client-2.png" alt="非新增" width="20" height="20"></div><?php endif; ?>
                                    <?php if(($v['mark']) == 2): ?><div class="emphasis btn_mark" key="<?php echo ($k); ?>" data-value="1"><img src="/Public/images/star-client.png" alt="重点" width="20" height="20"> </div>
                                        <?php else: ?>
                                        <div class="emphasis btn_mark" key="<?php echo ($k); ?>" data-value="2"><img src="/Public/images/star-client-2.png" alt="普通" width="20" height="20"></div><?php endif; ?>
                                    <?php if(!empty($v['introducermobile'])): ?><div class="introduce"><img src="/Public/images/intro-client.png" alt="介绍" width="20" height="20"></div>
                                        <?php else: ?>
                                        <div class="introduce"><img src="/Public/images/intro-client-2.png" alt="无介绍" width="20" height="20"></div><?php endif; ?>
                                </div>
                                <a  href="<?php echo U('System/User/detailUser',array('id'=>$v['user_id']));?>"  class="hrefDetail dn" target="_blank">详情</a>
                            </td>
                            <td class="tr_content content_realname"><div><?php echo $v['realname']?$v['realname']:'--';?></div></td>
                            <td class="tr_content content_username"><?php echo $v['mobile']?$v['mobile']:'--'; echo $v['phonevest']?'('.$v['phonevest'].')':'';?></td>
                            <td class="tr_content content_qq"><?php echo $v['qq']?$v['qq']:'--';?></td>
                            <td class="tr_content content_tel"><?php echo $v['tel']?$v['tel']:'--';?></td>
                            <td class="tr_content content_infoquality"><?php echo $v['infoqualityname'];?></td>
                            <td class="tr_content content_channelname"><?php echo $v['channelnames']?$v['channelnames']:'--';?></td>
                            <td class="tr_content content_status"><?php echo $v['statusname'];?></td>
                            <td class="tr_content content_system_user_id"><?php echo $v['status']!=160?$v['system_realname']:'--';?></td>
                            <td class="tr_content content_updateuser_id"><?php echo $v['updateuser_realname'];?></td>
                            <td class="tr_content content_isvisittime"><?php echo empty($v['visittime'])?'否':'是';?></td>
                            <td class="tr_content content_visittime"><?php echo $v['visit_time']?$v['visit_time']:'--';?></td>
                            <td class="tr_content content_allocationtime"><?php echo $v['allocation_time'];?></td>
                            <td class="tr_content content_lastvisit"><?php echo $v['lastvisit_time']?$v['lastvisit_time']:'--';?></td>
                            <td class="tr_content content_nextvisit"><?php echo $v['nextvisit_time']?$v['nextvisit_time']:'--';?></td>
                            <td class="tr_content content_attitude_id"><?php echo $v['attitudename']?$v['attitudename']:'--';?></td>
                            <td class="tr_content content_course_id"><?php echo empty($v['coursename'])?'--':$v['coursename'];?></td>
                            <td class="tr_content content_learningtype"><?php echo empty($v['learningtype'])?'泽林':$v['learningtypename'];?></td>
                            </tr><?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <table cellpadding="0" cellspacing="0" width="40" id="table3">
                    <thead>
                    <th>操作</th>
                    </thead>
                    <tbody>
                    <?php if(is_array($data['userAll'])): foreach($data['userAll'] as $k=>$v): ?><tr class="dispost_li">
                            <td>
                                <?php if($userinfo['system_user_id'] == $v['system_user_id']): ?><a href="javascript:;" class="proSelect"><i></i></a>
                                    <div class="otherOperation">
                                        <div class="otherIcon">
                                            <ul class="dispost_<?php echo ($k); ?>" data-value="<?php echo ($v["user_id"]); ?>" data-realname="<?php echo ($v["realname"]); ?>" data-qq="<?php echo ($v["qq"]); ?>" data-tel="<?php echo ($v["tel"]); ?>" data-username="<?php echo decryptPhone($v['username'],C('PHONE_CODE_KEY'));?>">
                                                <?php if($v['status']!=120): ?><li class="edit btn_mark" key="<?php echo ($k); ?>" data-value="<?php echo $v['mark']==1?'2':'1';?>">
                                                        <span class="setEdit"></span>
                                                        <?php if(($v['mark']==1)): ?><em>标为重点</em>
                                                            <?php else: ?>
                                                            <em>标为普通</em><?php endif; ?>
                                                    </li><?php endif; ?>
                                                <?php if(!empty($access_list['CREATEORDER'])): if($v['status']==20 || $v['status']==30 || $v['status']==70): ?><li class="apply btn_reserve">
                                                            <span class="setApp"></span>
                                                            <em>创建订单</em>
                                                        </li><?php endif; endif; ?>
                                                <?php if(!empty($access_list['AFFIRMVISIT'])): if($v['status']!=70): ?><li class="apply btn_visited">
                                                            <span class="setVisit"></span>
                                                            <em>确认到访</em>
                                                        </li><?php endif; endif; ?>
                                                <?php if(!empty($access_list['ABANDONUSER'])): if($v['status']!=70): ?><li class="btn_abandon">
                                                            <span class="setDel"></span>
                                                            <em>放弃</em>
                                                        </li><?php endif; endif; ?>
                                                <?php if(!empty($access_list['RESTARTUSER'])): if(($v['status']!=70)): ?><li class=" btn_restart">
                                                            <span class="setVisit"></span>
                                                            <em>出库</em>
                                                        </li><?php endif; endif; ?>
                                                <?php if(!empty($access_list['ALLOCATIONUSER'])): if(($v['status']!=70)): ?><li class="btn_allocation ">
                                                            <span class="setOut"></span>
                                                            <em>转出</em>
                                                        </li><?php endif; endif; ?>
                                                <?php if(!empty($access_list['SENDSMS'])): ?><li class="btn_msg">
                                                        <span class="setMsg"></span>
                                                        <em>发送短信</em>
                                                    </li><?php endif; ?>
                                                <li class="giveUp">
                                                    <span class="setApp"></span>
                                                    <a href="<?php echo U('System/User/detailUser',array('id'=>$v['user_id']));?>" target="_blank" class="proSelect a-des"><em>详情</em></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div><?php endif; ?>
                            </td>
                        </tr><?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <dl class="feOptions clearfix">
                <div class="batchDelete">
                    <label for="batchDeleteChk">
                        <input type="checkbox" id="batchDeleteChk" autocomplete="off" />
                        <span>全选</span>
                    </label>
                    <!--<input type="button" class="batchDeleteBtn btn_abandon pl" value="批量放弃">-->
                    <?php if(!empty($access_list['ALLOCATIONUSER'])): ?><input type="button" class="batchDeleteBtn btn_allocation pl" value="批量转出"><?php endif; ?>
                    <?php if(!empty($access_list['RESTARTUSER'])): ?><input type="button" class="batchDeleteBtn btn_restart pl" value="批量出库"><?php endif; ?>
                </div>
                <div class="collegaPage" id="paging">
                    <?php echo $data['paging_data'];?>
                    <!--<img class="fee_loding" style="margin: 0px 90px 0px 0px; width: 31px;" src="/Public/images/loading.gif">-->
                </div>
            </dl>
            <script>
                $(function(){
                    var data_column = <?php echo $data['column']?json_encode($data['column']):0;?>;
                    listBody(data_column);
//                    getPaging();
                    <?php echo (!empty($data['request']))?'offtop("#pjax_userlist")':'';?>
                });
                function offtop(obj){
                    $("body,html").animate({scrollTop:$(obj).offset().top},300);
                }
            </script>
        </div>
    </div>
</div>

<input type="hidden" name="temp_user_id" autocomplete="off">

<!-- 自定义显示列 S -->
<div class="panelConcentNew" style="display: none">
	<p class="setIllustrate">可设置6-17项显示列</p>
    <div class="Capacity"  style="height: 405px;">
        <div class="overflow">
            <dl class="proTit clearfix">
                <dt class="wOne proSequence clearfix">是否启动</dt>
                <dt class="wTwo">显示列</dt>
                <dt class="wThr">排序</dt>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_realname" type="checkbox"  autocomplete="off" value="realname" autocomplete="off"/></label>
                </dd>
                <dd class="wTwo">真实姓名</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_username" type="checkbox"  autocomplete="off" value="username"/></label>
                </dd>
                <dd class="wTwo">手机号码</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_qq" type="checkbox"  autocomplete="off" value="qq"/></label>
                </dd>
                <dd class="wTwo">QQ</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_tel" type="checkbox"  autocomplete="off" value="tel"/></label>
                </dd>
                <dd class="wTwo">固定电话</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_infoquality" type="checkbox"  autocomplete="off" value="infoquality" /></label>
                </dd>
                <dd class="wTwo">信息质量</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_channelname" type="checkbox"  autocomplete="off" value="channelname" /></label>
                </dd>
                <dd class="wTwo">渠道</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_status" type="checkbox"  autocomplete="off" value="status" /></label>
                </dd>
                <dd class="wTwo">状态</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_system_user_id" type="checkbox"  autocomplete="off" value="system_user_id" /></label>
                </dd>
                <dd class="wTwo">所属人</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_updateuser_id" type="checkbox"  autocomplete="off" value="updateuser_id" /></label>
                </dd>
                <dd class="wTwo">出库人</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_isvisittime" type="checkbox"  autocomplete="off" value="isvisittime" /></label>
                </dd>
                <dd class="wTwo">是否到访</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_visittime" type="checkbox"  autocomplete="off" value="visittime" /></label>
                </dd>
                <dd class="wTwo">第一次到访时间</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_allocationtime" type="checkbox"  autocomplete="off" value="allocationtime" /></label>
                </dd>
                <dd class="wTwo">分配时间</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_lastvisit" type="checkbox"  autocomplete="off" value="lastvisit" /></label>
                </dd>
                <dd class="wTwo">最后回访</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_nextvisit" type="checkbox"  autocomplete="off" value="nextvisit" /></label>
                </dd>
                <dd class="wTwo">下次回访/承诺到访</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_attitude_id" type="checkbox"  autocomplete="off" value="attitude_id" /></label>
                </dd>
                <dd class="wTwo">最近跟进</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_course_id" type="checkbox"  autocomplete="off" value="course_id" /></label>
                </dd>
                <dd class="wTwo">意向课程</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
            <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_learningtype" type="checkbox"  autocomplete="off" value="learningtype" /></label>
                </dd>
                <dd class="wTwo">学习平台</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl>
           <!--  <dl class="clearfix fw">
                <dd class="wOne">
                    <label><input name="Fruit" class="column_name column_reservetype" type="checkbox"  autocomplete="off" value="reservetype" /></label>
                </dd>
                <dd class="wTwo">预报审核状态</dd>
                <dd class="wThr">
                    <input type="text" value="1" autocomplete="off" disabled/>
                </dd>
            </dl> -->
            
        </div>
    </div>
    <div class="d2">
        <input type="submit" id="column_submit" value="提交"/>
    </div>
</div>
<!-- 自定义显示列 E -->

<!-- 创建订单 S -->
<div class="reApplyBox popup dn" id="panel2">
    <div class="alBoxCont" style="padding-top: 18px;">
        <div class="alRow clearfix">
            <div class="alRowLeft"></div>
            <div class="alRowRight" id="reserve_hint">

            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>真实姓名:</div>
            <div class="alRowRight">
                <input type="text" class="alInp" name="reserve_realname">
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>手机号码:</div>
            <div class="alRowRight">
                <input type="text" class="alInp" name="reserve_username">
            </div>
        </div>
        <!--<div class="alRow clearfix">-->
            <!--<div class="alRowLeft"><i>&#42</i>收款方式:</div>-->
            <!--<div class="alRowRight">-->
                <!--<select name="reserve_receivetype" autocomplete="off">-->
                    <!--<option value="0" selected="selected">选择方式</option>-->
                    <!--<?php if(is_array($data['receivetype'])): foreach($data['receivetype'] as $key=>$v): ?>-->
                        <!--<option value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></option>-->
                    <!--<?php endforeach; endif; ?>-->
                <!--</select>-->
            <!--</div>-->
        <!--</div>-->
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>订金:</div>
            <div class="alRowRight forecastingTips">
                <input type="text" class="alInp" name="reserve_subscription">
                <span>不得少于100元</span>
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft">&nbsp;</div>
            <div class="alRowRight">
                <input type="submit" class="alSubmit" id="reserve_submit" value="提交">
            </div>
        </div>
    </div>
</div>
<!-- 创建订单 E -->

<!-- 用户转出 S -->
<div id="panel3" class="panel3 dn">
    <div class="panelConcent" style="height: 440px;">
        <div class="div clearfix" style="margin-top: 0px">
            <select name="allocation_roleselect" autocomplete="off">
                <option value="0">全部用户组</option>
                <?php if(is_array($data['departmentAll']['data'])): foreach($data['departmentAll']['data'] as $k=>$v): ?><option value="$v['departmentname_id']" disabled><?php echo ($v["departmentname"]); ?></option>
                    <?php if(is_array($data['roleAll']['data'])): foreach($data['roleAll']['data'] as $k2=>$v2): if($v['department_id'] == $v2['department_id']): ?><option value="<?php echo ($v2['id']); ?>">&nbsp;&nbsp;├─ <?php echo ($v2["name"]); ?></option><?php endif; endforeach; endif; endforeach; endif; ?>
            </select>
            <input type="text" name="allocation_realname" value="" placeholder="输入姓名">
            <button class="nsSearchSubmit">搜索</button>
        </div>

        <div class="Capacity" style="height: 344px;">
            <div class="overflow">
                <dl class="proTit clearfix channelname" >
                    <dt class="wOne proSequence clearfix"><span>姓名</span></dt>
                    <dt class="wTwo">所属中心</dt>
                    <dt class="wThr " >
	                    <p>线上推广</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wFou">
	                    <p>招聘网站</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wFiv">
	                    <p>在线简历</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wSix">
	                    <p>线下院校</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wSev">
	                    <p>朋友/亲戚</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wEig">
	                    <p>自然网络</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wNin">操作</dt>
                </dl>
                <div id="allocation_body">

                </div>


            </div>
        </div>

    </div>
</div>
<div id="search_body" style="display: none">
</div>
<!-- 用户转出 E -->

<!-- 放弃 S -->
<div class="reApplyBox popup dn" id="panel4" style="height: 280px;">
    <div class="alBoxCont">
		<div class="alRow clearfix clearinfo">
			<div class="alRowLeft">真实姓名:</div>
			<div class="alRowRight realname"></div>
		</div>
		<div class="alRow clearfix clearinfo">
			<div class="alRowLeft">手机号码:</div>
			<div class="alRowRight mobile"></div>
		</div>
        <div class="alRow clearfix">
            <div class="alRowLeft alGiveUp"><i>&#42</i>放弃原因:</div>
            <div class="alRowRight" style="width: 360px;">
                <select name="abandon_attitude_id" autocompolet="off">
                    <option value="0" selected="selected">请选择放弃原因</option>
                    <?php if(is_array($data['attitude'])): foreach($data['attitude'] as $k=>$v): if(($v['num'] > 2) && ($v['num'] != 10)): ?><option value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></option><?php endif; endforeach; endif; ?>
                </select>
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>备注:</div>
            <div class="alRowRight" style="height: 116px;">
                <textarea name="abandon_remark" class="abandon_remark" cols="30" rows="10"></textarea>
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft">&nbsp;</div>
            <div class="alRowRight">
                <input type="submit" class="alSubmit" id="abandon_submit" value="提交">
            </div>
        </div>
    </div>
</div>
<!-- 放弃 E -->

<div class="dn">
<!-- 确认客户到访 S -->
<div class="manuallyVisit popup" id="manually">
	<div class="myTips">客户第一次上门到访</div>
	<div class="myRow clearfix">
		<span>真实姓名：</span>
		<em></em>
	</div>
	<div class="myRow clearfix">
		<span>手机号码：</span>
		<em></em>
	</div>
	<div class="myRow clearfix">
		<span>固定电话：</span>
		<em></em>
	</div>
	<div class="myRow clearfix">
		<span>QQ：</span>
		<em></em>
	</div>
	<input type="button" class="myBtns" id="visit_submit" value="提交">
</div> 
<!-- 确认客户到访 E -->

<!-- 客户到访提醒 S -->
<div class="visitReminds popup" id="automatic">
	<div class="vsTips">客户已到访，请到前台接待</div>
	<div class="vsRow clearfix">
		<span>真实姓名：</span>
		<em></em>
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

<!-- 标记重点 S -->
<div class="keyPop dn">
    <div class="kpTop"></div>
    <div class="kpMiddle clearfix">
        <div class="kpMiddleLeft"></div>
        <div class="kpMiddleCenter">
            <div class="kpCenterTitle">标记重点客户</div>
            <!--<div class="kpCenterTitle">标记普通客户</div>-->
            <div class="kpCenterCont">
                <p>将该客户标记为重点客户。</p>
                <!--<p>将该客户标记为普通客户。</p>-->
                <div class="kpRow clearfix">
                    <div class="leftPortion l">真实姓名：</div>
                    <div class="sectionMark l">
                        <span class="key_realname">--
                            <i><img src="/Public/images/star-client.png"></i>
                                <!--<img src="/Public/images/star_ordinary.png">-->
                        </span>
                    </div>
                </div>
                <div class="kpRow clearfix">
                    <div class="leftPortion l">手机号码：</div>
                    <div class="sectionMark l key_mobile">--</div>
                </div>
            </div>
        </div>
        <div class="kpMiddleRight"></div>
    </div>
    <div class="kpBottom"></div>
</div>
<!-- 标记重点 E -->

<!--  发短信外层 S  -->
<!--<div class="">-->
	
	<!--  发短信 S  -->
	<div class="setMsgBox dn" id="setMsgBox">
		<div class="msgRows nonMandatory clearfix">
			<span>真实姓名</span>
			<em class="msg_realname">--</em>
		</div>
		<div class="msgRows nonMandatory clearfix">
			<span>手机号码</span>
			<em class="msg_username">--</em>
		</div>
		<div class="msgRows clearfix">
			<span><i>*</i>短信模版</span>
			<select id="setSelect" class="msg_template">
				<option selected="selected" value="0">请选择短信模版</option>
			</select>
			<input type="button" class="setSMSTemplate" value="设置短信模版" />
		</div>
		<div class="msgRows clearfix">
			<span><i>*</i>短信模版</span>
			<div class="msgTxtBox" style="width: 366px;">
				<textarea class="msgTxt" style="float: left;"></textarea>
				<p style="float: left;">由于服务方规定短信内容不能含有“培训”“老师”及课程培训内容，请避免这些内容，以免客户短信无法接收。</p>
			</div>
		</div>
		<div class="msgRows clearfix">
			<span>&nbsp;</span>
			<div class="msgBtnBox">
				<input type="button" class="msgBtnConfirm" value="发送" />
				<input type="button" class="msgBtnCancel" value="取消" />
			</div>
		</div>
	</div>
	<!--  发短信 E  -->
<!--</div>-->
<!--  发短信外层 E  -->

<!--  外层box S  -->
<!--<div class="bigBox">-->
	<!--  设置短信模版 S  -->
	<div class="setMsgTemplate dn">
		<div class="temTit clearfix">
			<span>设置短信模版</span>
			<i></i>
		</div>
		<div class="addBox clearfix">
			<input type="button" class="addTemBtn" value="添加新模板" />
		</div>
		<div class="temList">
			<p class="notMsg">赶紧定制属于你的短信模板吧...</p>
			<table cellpadding="0" cellspacing="0" class="setMsgTemplate_list">

			</table>
		</div>
	</div>
	<!--  设置短信模版 E  -->
	
	<!-- 添加短信模版 S -->
	<div class="addMsgBox addMsg dn">
		<div class="addMsgTit">
			<span>添加短信模板</span>
			<i class="addclose"></i>
		</div>
		<div class="addMsgCont" style="margin-bottom: 0px;">
			<div class="addMsgRows clearfix">
				<span><i>*</i>短信模板名称:</span>
				<input type="text" class="addMsgInp l" name="create_name" placeholder="模板名称" />
			</div>
			<div class="addMsgRows clearfix">
				<span><i>*</i>短信内容:</span>
				<div class="editMsgBox l">
					<ul class="clearfix">
						<li>
							<input type="button" class="btnsName btns_btn" data-value="{username}" value="客户姓名" />
						</li>
                        <li>
                            <input type="button" class="btnsPhone btns_btn" data-value="{myname}" value="我的姓名" />
                        </li>
                        <li>
                            <input type="button" class="btnsPhone btns_btn" data-value="{myphone}" value="我的手机" />
                        </li>
					</ul>
					<textarea class="editTxt" name="create_template"></textarea>
                </div>
                <p style="float: left; width: 370px; margin-left: 117px;line-height: 21px;color: #d00202; font-size: 12px;">由于服务方规定短信内容不能含有“培训”“老师”及课程培训内容，请避免这些内容，以免客户短信无法接收。</p>
            </div>
			<div class="addMsgRows clearfix">
				<span>&nbsp;</span>
				<input type="button" class="nextBtn l" value="预览，并进行下一步">
			</div>
		</div>
	</div>
	<!-- 添加短信模版 E -->

    <!-- 修改短信模版 S -->
    <div class="editMsg dn">
        <div class="addMsgTit">
            <span>修改短信模板</span>
            <i class="editclose"></i>
        </div>
        <div class="addMsgCont" style="margin-bottom: 0px;">
            <div class="addMsgRows clearfix">
                <span><i>*</i>短信模板名称:</span>
                <input type="text" class="addMsgInp l" name="edit_name" placeholder="模板名称" />
            </div>
            <div class="addMsgRows clearfix">
                <span><i>*</i>短信内容:</span>
                <div class="editMsgBox l">
                    <ul class="clearfix">
                        <li>
                            <input type="button" class="btnsName btns_btn" data-value="{username}" value="客户姓名" />
                        </li>
                        <li>
                            <input type="button" class="btnsPhone btns_btn" data-value="{myname}" value="我的姓名" />
                        </li>
                        <li>
                            <input type="button" class="btnsPhone btns_btn" data-value="{myphone}" value="我的手机" />
                        </li>
                    </ul>
                    <textarea class="editTxt" name="edit_template"></textarea>
                </div>
            </div>
            <p style="float: left; width: 370px; margin-left: 117px;line-height: 21px;color: #d00202; font-size: 12px;">由于服务方规定短信内容不能含有“培训”“老师”及课程培训内容，请避免这些内容，以免客户短信无法接收。</p>
            <div class="editMsgRows clearfix">
                <span>&nbsp;</span>
                <input type="button" class="nextBtn l edit" value="预览，并进行下一步">
                <input type="hidden" name="edit_template_id" >
            </div>
        </div>
    </div>
    <!-- 添加短信模版 E -->

    <!-- 模版预览 S -->
    <div class="templatePreview dn">
        <div class="tpTit">
            <span>短信模板预览</span>
            <i></i>
        </div>
        <div class="tpCont">
            <div class="tpRows clearfix">
                <span>短信模板名称:</span>
                <em class="show_templatename">公开课通知</em>
            </div>
            <div class="tpRows clearfix">
                <span>短信内容:</span>
                <textarea class="tpEditTxt l show_template"></textarea>
            </div>
            <div class="tpRows clearfix">
                <span>&nbsp;</span>
                <div class="tpBtns">
                    <input type="button" class="prevBtn" value="返回上一步修改">
                    <input type="button" class="createTemplate_subtn" style="background: #53b567 none repeat scroll 0 0;border: medium none;color: #fff;cursor: pointer;height: 30px;line-height: 30px;padding: 0 20px;" value="保存">
                </div>
            </div>
        </div>
    </div>
    <!-- 模版预览 E -->
<!--</div>-->
<!--  外层box E  -->



<!--双击-->
<form id="hrefForm" action='' method="get"  target="_blank" >
</form>

<script type="text/javascript" src="/Public/js/pjax/js/jquery.pjax.js"></script>

<script src="/Public/js/layer/layer.js"></script>
<script src="/Public/js/jquery.lib.min.js"></script>
<script src="/Public/js/glDatePicker/glDatePicker.js"></script>
<script src="/Public/js/mouseEnter.js"></script>
<script src="/Public/js/organization.js"></script>
<script src="/Public/js/placeholder.js"></script>
<script src="/Public/js/addFile.js"></script>
<script src="/Public/js/ClientList.js?v=2016090511"></script>
<script src="/Public/js/common_ajax.js?v=201608231"></script>
<script src="/Public/js/userList.js?v=20160831"></script>

<script>
$(function(){

    //IS IE?
    if(navigator.userAgent.indexOf("MSIE")>0)
    {
        if(navigator.userAgent.indexOf("MSIE 6.0")>0)
        {
            $('#subForm').attr('onsubmit',' ');
        }
        if(navigator.userAgent.indexOf("MSIE 7.0")>0)
        {
            $('#subForm').attr('onsubmit',' ');
        }
        if(navigator.userAgent.indexOf("MSIE 8.0")>0)
        {
            //	alert("ie8");
            var url_from = $('#subForm').attr('action');
            var newstr=url_from.replace("pjax_userlist","main");
            $('#subForm').attr('onsubmit',' ').attr('action',newstr);
        }
    }
    //pjax
    $(document).pjax('a', '#pjax_userlist' ,{fragment:'#pjax_userlist', timeout:8000});
    $('#subForm').click(function (event) {
        $.pjax.submit(event, '#pjax_userlist', {fragment:'#pjax_userlist', timeout:6000});
    });
    //pjax链接点击后显示加载动画
    $(document).on('pjax:send', function() {
        //加载层-默认风格 loading
        layer.load(2);
    });
    //pjax链接加载完成后隐藏加载动画
    $(document).on('pjax:complete', function() {
        //此处 关闭loading
        layer.closeAll('loading');
    });
    //放弃自动填充备注
    $(':input[name="abandon_attitude_id"]').change(function(){
        var remarkObj = $('.abandon_remark');
        if(remarkObj.val()=='' && $(this).val()!=10 && $(this).val()!=0){
            var text = ' '+$(':input[name="abandon_attitude_id"]').find("option:selected").text()+' ';
            remarkObj.val(text);
        }else if(remarkObj.val()!='' && $(this).val()!=10 && $(this).val()!=0){
            var remarkText = remarkObj.val();
            var text = $(':input[name="abandon_attitude_id"]').find("option:selected").text();
            remarkObj.val(remarkText.replace(/([^"]*) ([^"]*) ([^"]*)/g, "$1 "+text+" $3"));
        }else{
            $('.abandon_remark').val('');
        }
    });
});


</script>
</body>
</html>