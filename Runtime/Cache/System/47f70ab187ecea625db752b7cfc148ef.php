<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
    <title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
    <link rel="stylesheet" href="/Public/css/common.css">
    <link rel="stylesheet" href="/Public/js/glDatePicker/glDatePicker.default.css">
    <link rel="stylesheet" href="/Public/css/external.min.css">
    <link rel="stylesheet" href="/Public/css/visitList.css">
</head>
<body>
<div class="wrapBox">
    <div class="imCont">
        <div class="imContTop clearfix">
            <div class="topTit l">到访列表</div>
            <div class="topRight r">
                <a href="<?php echo U('System/User/addUser',array('type'=>'visit'));?>" class="addAccount">添加用户</a>
            </div>
        </div>
        <div class="viCondition clearfix">
            <span>实际到访时间：</span>
            <input type="text" class="afTime start" id="afTimeStar" value="<?php echo ($data['request']['visittime_val'][0]); ?>" placeholder="开始时间">
            <em>至</em>
            <input type="text" class="afTime end" id="afTimeEnd" value="<?php echo ($data['request']['visittime_val'][1]); ?>" data-url="<?php echo U('System/Visit/visitList').'?visittime=';?>" placeholder="截止时间">

            <form action="<?php echo U('System/Visit/visitList');?>" method="get" onsubmit="layer.load(2);">
            	<select class="visSelect" name="keyname">
            		<option value="username" <?php echo $data['request']['keyname']=='username'?'selected="selected"':'';?> >手机号码</option>
            		<option value="qq" <?php echo $data['request']['keyname']=='qq'?'selected="selected"':'';?>>QQ</option>
            		<option value="tel" <?php echo $data['request']['keyname']=='tel'?'selected="selected"':'';?>>固定电话</option>
            	</select>
	            <input type="text" class="viInp" name="search" value="<?php echo ($data['request']['search']); ?>" placeholder="请输入手机、固定电话或QQ搜索">
	            <input type="submit" class="viSearchBtn" value="搜索">
            </form>
        </div>
        <div class="viMiddle">
            <table cellpadding="0" cellspacing="0" id="viTable">
                <tr class="viHeader">
                    <th class="optionsTh">选中</th>
                    <th>真实姓名</th>
                    <th>手机号码</th>
                    <th>QQ</th>
                    <th>固定电话</th>
                    <th>渠道</th>
                    <th>所属人</th>
                    <th>承诺到访</th>
                    <th>实际到访</th>
                    <th class="operatingTh">操作</th>
                </tr>
                <?php if(is_array($data['userAll'])): foreach($data['userAll'] as $key=>$v): ?><tr>
                        <td class="optionsThTd"><input type="checkbox" class="viChk" name="viCheckBox"></td>
                        <td><?php echo ($v["realname"]); ?></td>
                        <td><?php echo !empty($v['username'])?(decryptPhone($v['username'],C('PHONE_CODE_KEY'))):'--';?></td>
                        <td><?php echo $v['qq']!=0?$v['qq']:'--';?></td>
                        <td><?php echo $v['tel']!=0?$v['tel']:'--';?></td>
                        <td><?php echo ($v["channelname"]); ?></td>
                        <td><?php echo ($v['status']!=160 && !empty($v['system_realname']))?$v['system_realname']:'--';?></td>
                        <td><?php echo ($v['attitude_id']==2 && $v['nextvisit']!=0)?(date('Y-m-d H:i:s',$v['nextvisit'])):'--';?></td>
                        <td><?php echo $v['visittime']!=0?(date('Y-m-d H:i:s',$v['visittime'])):'--';?></td>
                        <td>
                            <?php if(empty($v['visittime']) || $v['visittime']==0): ?><a href="javascript:getStatus('<?php echo ($v["user_id"]); ?>');" class="confirmVisit">确认到访</a>
                            <?php else: ?>
                                <a href="javascript:;" class="confirmVisit">已到访</a><?php endif; ?>
                            <a  href="<?php echo U('System/User/detailUser',array('id'=>$v['user_id']));?>"  class="detailLink" target="_blank">详情</a>
                        </td>
                    </tr><?php endforeach; endif; ?>

            </table>
        </div>

        <div class="clearfix">
            <div class="collegaPage">
                <?php echo ($data['paging']); ?>
            </div>
        </div>
    </div>
</div>

<!-- ====================== 弹窗系列     ======================= -->
<div class="dn">

    <!-- 客户是回库状态 S -->
    <div class="backToLibrary popup" id="popup1">
        <div class="btlTitle">该客户将分配给<em class="newSystem"></em>，请通知他前来接待客户</div>
        <div class="btlBtnBox clearfix">
            <input type="button" class="viReassign l getsystembtn" value="重新分配">
            <input type="button" class="viConfirm l newDispostSub" value="确定">
        </div>
    </div>
    <!-- 客户是回库状态 E -->

    <!-- 到访客户接待提醒 S -->
    <div class="receptionRemind popup" id="popup2">
        <p>当前所有销售都是忙线状态，</p>
        <p>请通知销售主管协调人员接待客户。</p>
        <div class="recepBtnBox clearfix">
            <input type="button" class="viReassign2 l getsystembtn" value="重新分配">
            <input type="button" class="viCancel l" value="确定">
        </div>
    </div>
    <!-- 到访客户接待提醒 S -->

    <!-- 操作者是本中心 S -->
    <div class="theCentre popup" id="popup3">
        <div class="centerTips">该客户跟进操作者是<em class="newSystem"></em>，请通知他前来接待客户</div>
        <input type="button" class="centerBtn newDispostSub" value="确定">
    </div>
    <!-- 操作者是本中心 E -->

    <!-- 操作者是非本中心 S -->
    <div class="theNonCentre popup" id="popup4">
        <p class="nonCenterTips">该客户跟进操作者是<em class="newSystem"></em>，</p>
        <p>请通知本中心主管安排接待员前来接待</p>
        <input type="button" class="nonCenterBtn" value="确定">
    </div>
    <!-- 操作者是非本中心 E -->

    <!-- 重新分配 S -->
    <div class="viReassign popup" id="viReassign">
        <div class="rnSearch clearfix">
            <div class="selectbox l">
                <dl class="select">
                    <dt>
                        <div class="select_title l nsSelectSearch_d">全部</div>
                        <div class="arrow r"></div>
                    </dt>
                    <dd class="fxDone">全部</dd>
                    <dd class="fxDone">销售部</dd>
                </dl>
            </div>
            <input type="text" class="rnSearchInp l" name="nsSelectSearch">
            <input type="button" class="rnSearchBtn l nsSearchSubmit" value="搜索">
        </div>
        <div class="rnTableBox">
            <table cellpadding="0" cellspacing="0" id="rnTable">
                <tr class="rnHeader">
                    <th class="rnPosition">所属职位</th>
                    <th>姓名</th>
                    <th>忙闲状态</th>
                    <th>操作</th>
                </tr>
            </table>
        </div>
    </div>
    <!-- 重新分配 E -->
    <input type="hidden" name="temp_user">
    <input type="hidden" name="temp_system">
    <div id="search_body" style="display: none">

    </div>
</div>

<script src="/Public/js/jquery-1.9.1.min.js"></script>
<script src="/Public/js/glDatePicker/glDatePicker.js"></script>
<script src="/Public/js/jquery.lib.min.js"></script>
<script src="/Public/js/placeholder.js"></script>
<script src="/Public/js/visitList.js"></script>
<script src="/Public/js/layer/layer.js"></script>
<script src="/Public/js/common_ajax.js"></script>
<script>
    $(function(){
        //数据提交
       $('.newDispostSub').click(function(){
           var temp_user = $(':input[name="temp_user"]').val();
           var temp_system = $(':input[name="temp_system"]').val();
           subtn(temp_user,temp_system);
       });
        //  重新分配 star
        $('.getsystembtn').on('click', function(){
            getSystemList();
        });
        //搜索职位相关-检索
        $('.nsSearchSubmit').on('click', function(){
            var val = $(':input[name="nsSelectSearch"]').val();
            var d_val = $('.nsSelectSearch_d').text();
            $('#rnTable .system_ajax').remove();
            if(val.length>0 || d_val!='全部'){
                $('#search_body .system_ajax').each(function(i){
                    var zmnumReg=new RegExp( val ,'gim');
                    var zmnumReg2=new RegExp( d_val ,'gim');
                    var name=$(this);
                    if(d_val!='全部'){
                        if(val!=''){
                            if( zmnumReg.test(name.children('.rnRealname').text()) && zmnumReg2.test(name.children('.rnPosition').text()) ){
                                $('#rnTable').append(name.clone());
                            }
                        }else{
                            if( zmnumReg2.test(name.children('.rnPosition').text()) ){
                                $('#rnTable').append(name.clone());
                            }
                        }
                    }else{
                        if( zmnumReg.test(name.children('.rnRealname').text()) ){
                            $('#rnTable').append(name.clone());
                        }
                    }
                });
            }else{
                $('#rnTable').append( $('#search_body .system_ajax').clone() );
            };
        });
    });
    //获取客户信息状态
    function getStatus(uid){
        layer.load(2);
        $(':input[name="temp_user"]').val(uid);
        $.ajax({
            url:"<?php echo U('System/Visit/visitList');?>",
            type:'post',
            dataType:'json',
            data:{user_id:uid,type:'getUser'},
            success:function(reflag){
                layer.closeAll('loading');
                if(reflag.code==1){
                    //未分配
                    $(':input[name="temp_system"]').val(reflag.data.system_user_id);
                    $('.newSystem').html(reflag.data.realname);
                    $.colorbox({
                        inline: true,
                        href: $("#popup1"),
                        overlayClose: false,
                        title: "到访客户接待提醒"
                    });
                }else if(reflag.code==2){
                    //本中心
                    $(':input[name="temp_system"]').val(reflag.data.system_user_id);
                    $('.newSystem').html(reflag.data.realname);
                    $.colorbox({
                        inline: true,
                        href: $("#popup3"),
                        overlayClose: false,
                        title: "到访客户接待提醒"
                    });
                }else if(reflag.code==3){
                    //非本中心客户
                    $(':input[name="temp_system"]').val(reflag.data.system_user_id);
                    $('.newSystem').html(reflag.data.zonename+'&nbsp;&nbsp;'+reflag.data.realname);
                    $.colorbox({
                        inline: true,
                        href: $("#popup4"),
                        overlayClose: false,
                        title: "非本中心客户"
                    });
                }else if(reflag.code==4){
                    //无所属销售人员
                    $(':input[name="temp_system"]').val(reflag.data.system_user_id);
                    $('.newSystem').html(reflag.data.realname);
                    $.colorbox({
                        inline: true,
                        href: $("#popup1"),
                        overlayClose: false,
                        title: "无所属销售人员"
                    });
                }else if(reflag.code==5){
                    //本中心销售忙线
                    $.colorbox({
                        inline: true,
                        href: $("#popup2"),
                        overlayClose: false,
                        title: "本中心销售忙线"
                    });
                }
            },
            error:function(){
                layer.closeAll('loading');
            }
        })
    }
    //获取员工列表
    function getSystemList(role_id){
        $('.system_ajax').remove();
        $.ajax({
            url: "<?php echo U('System/Visit/visitList');?>",
            type: 'post',
            dataType: 'json',
            data: {role_id: role_id, type: 'getSystemList'},
            success: function (reflag) {
                if(reflag.code==0){
                    var html = '';
                    var html_if = '';
                    $.each(reflag.data.data, function(k,v){
                        if(v.engaged_status && v.engaged_status==1){
                            html_if = '<td><a href="javascript:;" class="rnXian">忙线</a></td> <td><a href="javascript:;" onclick="subtn(0,'+ v.system_user_id+')" class="rnConfirm">确定</a></td>';
                        }else{
                            html_if = '<td><a href="javascript:;" class="rnMang">空闲</a></td> <td><a href="javascript:;" onclick="subtn(0,'+ v.system_user_id+')" class="rnConfirm">确定</a></td>';
                        };
                        html += '<tr class="system_ajax"> <td class="rnPosition">'+ v.role_names+'</td> <td class="rnRealname">'+ v.realname+'</td> '+html_if+' </tr>';
                    });
                    $('#rnTable').append(html);
                    $('#search_body').empty().append(html);
                };
            }
        });
        $.colorbox({
            inline: true,
            href: $("#viReassign"),
            overlayClose: false,
            title: "分配员工列表"
        });
    }
    function subtn(temp_user,temp_system){
        if(temp_user==0){
            if($(':input[name="temp_user"]').val().length>0){
                temp_user = $(':input[name="temp_user"]').val();
            }else{
                layer.msg('请重新选择要确认到访的客户',{icon:2});
            }
        }
        var data = {
            user_id:temp_user,
            system_user_id:temp_system,
            type:'submit'
        };
        common_ajax(data,'','reload');
    }
</script>
</body>
</html>