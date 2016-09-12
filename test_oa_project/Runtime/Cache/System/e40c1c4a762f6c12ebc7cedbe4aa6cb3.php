<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
    <title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
    <link rel="stylesheet" href="/Public/css/common.css">
    <link rel="stylesheet" href="/Public/css/external.min.css">
    <link rel="stylesheet" href="/Public/css/addClient.css">
</head>
<body>
<div class="wrapBox">
    <div class="newEmployeesCont">
        <div class="nsContTop clearfix">
            <div class="topTit l">
                <span class="masterList"><?php echo !empty($data['request']['type'])?'到访列表':'客户列表';?></span>
                <span><em>&gt;</em>添加客户</span>
            </div>
            <div class="topRight r">
                <a href="javascript:history.go(-1);" class="return">返回</a>
            </div>
        </div>
        <div class="nsMiddle">
            <div class="nsRow clearfix">
                <div class="nsLeft2">真实姓名:</div>
                <div class="nsRight2">
                    <input type="text" class="nsInp" name="realname">
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2">手机号码:</div>
                <div class="nsRight2">
                    <input type="tel" class="nsInp" name="username">
                    <span class="nsSpTip">手机号码/固定电话/QQ至少填写一项</span>
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2">固定电话:</div>
                <div class="nsRight2 clearfix">
                    <input type="tel" class="nsInp" name="tel">
                    <span class="nsSpTip">手机号码/固定电话/QQ至少填写一项</span>
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2">QQ:</div>
                <div class="nsRight2 clearfix">
                    <input type="tel" class="nsInp" name="qq">
                    <span class="nsSpTip">手机号码/固定电话/QQ至少填写一项</span>
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2">邮箱:</div>
                <div class="nsRight2">
                    <input type="text" class="nsInp" name="email">
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2"><i>&#42</i>信息质量:</div>
                <div class="nsRight2">
                    <div class="selectbox2 l">
                        <dl class="select2">
                            <dt>
                            <div class="select_title l">请选择信息质量</div>
                            <div class="arrow r"></div>
                            </dt>
                            <dd class="fxDone" data-value="1">A</dd>
                            <dd class="fxDone" data-value="2">B</dd>
                            <dd class="fxDone" data-value="3">C</dd>
                            <dd class="fxDone" data-value="4">D</dd>
                        </dl>
                        <input type="hidden" name="infoquality">
                    </div>
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2"><i>&#42</i>所属渠道:</div>
                <div class="nsRight2">
                    <div class="selectbox2 l">
                        <dl class="select2">
                            <dt>
                                <div class="select_title l">选择渠道数据</div>
                                <div class="arrow r"></div>
                            </dt>
                            <?php if(is_array($data['channel']['data'])): foreach($data['channel']['data'] as $k=>$v): ?><dt class="caption">
                                    <div class="select_title2 l " data-value="<?php echo ($v["channel_id"]); ?>"><?php echo ($v["channelname"]); ?></div>
                                </dt>
                                <!--<dd class="fxDone" data-value="<?php echo ($v["channel_id"]); ?>"><?php echo ($v["channelname"]); ?></dd>-->
                                <?php if(!empty($v['children'])): if(is_array($v['children'])): foreach($v['children'] as $key=>$v2): ?><dd class="fxDone" data-value="<?php echo ($v2["channel_id"]); ?>">&nbsp;&nbsp;├─ <?php echo ($v2["channelname"]); ?></dd><?php endforeach; endif; endif; endforeach; endif; ?>
                        </dl>
                        <input type="hidden" name="channel_id">
                    </div>
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2">搜索词:</div>
                <div class="nsRight2">
                    <input type="text" class="nsInp" name="searchkey">
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2">咨询页面:</div>
                <div class="nsRight2">
                    <input type="text" class="nsInp" name="interviewurl">
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2"><i>&#42</i>意向课程:</div>
                <div class="nsRight2">
                    <div class="selectbox2 l">
                        <dl class="select2" style="z-index: 999;">
                            <dt>
                                <div class="select_title l">请选择课程</div>
                                <div class="arrow r"></div>
                            </dt>
                            <dd class="fxDone" data-value="0">无</dd>
                            <?php if(is_array($data['course'])): foreach($data['course'] as $k=>$v): ?><dd class="fxDone" data-value="<?php echo ($v["course_id"]); ?>"><?php echo ($v["coursename"]); ?></dd><?php endforeach; endif; ?>
                        </dl>
                        <input type="hidden" name="course_id">
                    </div>
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2"><i>&#42</i>转介绍:</div>
                <div class="nsRight2 clearfix">
                    <label for="radioMan" class="manLabel">
                        <input type="radio" name="nsSex" class="nsRadio man" id="radioMan" value="1">
                        <span>是</span>
                    </label>
                    <label for="radioWoman">
                        <input type="radio" name="nsSex" class="nsRadio woMan" id="radioWoman" checked="checked" value="0">
                        <span>否</span>
                    </label>
                </div>
            </div>
            <div class="nsRow clearfix nsNone">
                <div class="nsLeft2"><i>&#42</i>转介绍人手机号码:</div>
                <div class="nsRight2">
                    <input type="text" class="nsInp" name="introducermobile">
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2"><i>&#42</i>备注:</div>
                <div class="nsRight2">
                    <script id="editor3" name="advantage"  type="text/plain" ><?php echo (htmlspecialchars_decode($curUpdateInfo[0]['upbody'])); ?></script>
                </div>
            </div>
            <div class="nsRow clearfix">
                <div class="nsLeft2">&nbsp;</div>
                <div class="nsRight2">
                    <input type="submit" class="nsSubmit tt" value="提交">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 选择部门 S -->
<div class="department">
    <div class="nsTop clearfix">
        <span>系统提醒</span>
        <div class="nsClose"></div>
    </div>
    <div class="nsCont" style="margin: 32px 0;">
        手机号码/固定电话/QQ至少填写一项
    </div>
</div>
<!-- 选择部门 E -->

<div class="dn">
    <!-- 重新分配 S -->
    <div class="backToLibrary popup" id="popup1">
        <div class="btlTitle">该客户将分配给<em class="newSystem"></em>，请通知他前来接待客户</div>
        <div class="btlBtnBox clearfix">
            <input type="button" class="viReassign l getsystembtn" value="重新分配">
            <input type="button" class="viConfirm l newDispostSub" value="确定">
        </div>
    </div>
    <!-- 重新分配 S -->
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
    <div id="search_body" style="display: none">

    </div>
</div>

<script src="/Public/js/jquery-1.9.1.min.js"></script>
<script src="/Public/js/jquery.lib.min.js"></script>
<script src="/Public/js/layer/layer.js"></script>
<script src="/Public/js/addClient.js"></script>
<script src="/Public/js/common_ajax.js"></script>
<!-- 投递简历弹出框1 工作地址不符提示 S -->
<script type="text/javascript" src="/Public/js/ueditor/ueditor.simple.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="/Public/js/ueditor/ueditor.all.min.js"></script>
<script>
    var ue = UE.getEditor('editor3',{
        serverUrl:  "/System/Ueditor/index",
        toolbars: [
            ['fullscreen', 'source','fontsize','fontfamily', 'undo', 'redo','underline', 'bold','simpleupload', 'spechars','justifyleft','justifyright','justifycenter','emotion']
        ],
        initialFrameWidth:480,
        initialFrameHeight:200,        
        maximumWords:1000,
        enableAutoSave:false,
        elementPathEnabled:false,
        autoFloatEnabled:false
    });

    $(function(){
        var re_type = "<?php echo !empty($data['request']['type'])?$data['request']['type']:'';?>";
        $(document).on('click','#cboxClose',function(){
            window.location.href="<?php echo U('System/Visit/visitList');?>";
        });
        $('.nsSubmit').click(function(){
            if($(':input[name="nsSex"]:checked').val()==1){
                if($(':input[name="introducermobile"]').val().length==0){
                    layer.msg('转介绍必须填写转介绍人手机号码', {icon:2});
                }
            }
            var data = {
                realname:$(':input[name="realname"]').val(),
                username:$(':input[name="username"]').val(),
                tel:$(':input[name="tel"]').val(),
                qq:$(':input[name="qq"]').val(),
                email:$(':input[name="email"]').val(),
                infoquality:$(':input[name="infoquality"]').val(),
                channel_id:$(':input[name="channel_id"]').val(),
                searchkey:$(':input[name="searchkey"]').val(),
                interviewurl:$(':input[name="interviewurl"]').val(),
                course_id:$(':input[name="course_id"]').val(),
                introducermobile:$(':input[name="introducermobile"]').val(),
                remark:ue.getContent()
            };
            if(re_type=='visit'){
                layer.load(2);
                $.ajax({
                    url: "<?php echo U('System/User/addUser',array('type'=>'visit'));?>",
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (reflag) {
                        layer.closeAll('loading');
                        if(reflag.code==0){
                            $(':input[name="temp_user"]').val(reflag.data);
                            getSystemList();
                        }else{
                            layer.msg(reflag.msg,{icon:2});
                        }
                    }
                });
            }else{
                common_ajax(data);
            }
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
                            html_if = '<td><a href="javascript:;" class="rnXian">忙线</a></td> <td><a href="javascript:;" onclick="layer.msg(\'该员工正处于忙线状态\',{icon:2});" class="rnConfirm">确定</a></td>';
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
        common_ajax(data,"<?php echo U('System/Visit/visitList');?>");
    }
</script>
</body>
</html>