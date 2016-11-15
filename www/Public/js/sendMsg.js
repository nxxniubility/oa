$(function(){
	selectbox();
	selectbox2();
	
	//  选择部门和职位弹窗全选
	chkAll();
});
//下拉框
function selectbox() {
    $(document).bind({
        click: function() {
            $(".selectbox dt").parent().find("ul").removeClass("s");
        }
    });
    $(document).on('click', '.select dt',
        function(){
            $(this).parent().find("dd").toggle();
            $(this).parent().find(".ddoption").toggle();
            selectStatus($(this));
            if ($(this).attr("class") == "on") {
                if ($(this).parent().find("ul").height() > 200) {
                    $(this).parent().find("ul").addClass("s");
                };
            } else {
                $(this).parent().find("ul").removeClass("s");
            }
            return false;
        });

    $(document).on('click', '.select dd',
        function(){
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_name = $(this).text();
            $(this).parent('.ddoption').toggle();
            $(this).parent("dl").find(".select_title").text(data_name);
            $(this).parent().parent().find(".select_title").text(data_name);
            $(this).parents("dl").next().val(data_value);
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select dd").parent("dl").find("dt"));
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
        });
    $(document).click(function() {
        $(".select dd").hide();
        selectStatus($(".select dt"));
    });

}
//下拉框
function selectbox2() {
    $(document).bind({
        click: function() {
            $(".selectbox2 dt").parent().find("ul").removeClass("s");

            $(".selectbox2 dt").find(".select_title2").hide();

        }
    });

    $(document).on( "click", ".dt_btn", function() {
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_value_name = $(this).attr('data-name');
            var data_name = $(this).text();
            $(this).parents("dl").find(".select_title").text(data_name);
            $(':input[name="'+data_value_name+'"]').val(data_value);
            $(this).parents('.ddoption').toggle();
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select dd").parent("dl").find("dt"));
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
        });
    $(document).on('click', '.select2 dt', function(){
            if ($(this).is(".caption")) return false;
            if ($(this).is(".caption2")) return false;
            $(this).parent().find("dd").toggle();
            $(this).parent().find(".ddoption").toggle();
            $(this).parent().find(".select_title2").toggle();
            $(this).parent().find(".select_title3").toggle();
            selectStatus($(this));
            if ($(this).attr("class") == "on") {
                if ($(this).parent().find("ul").height() > 200) {
                    $(this).parent().find("ul").addClass("s");
                };
            } else {
                $(this).parent().find("ul").removeClass("s");
            }
            return false;
        });
    $(document).on('click', '.select2 dd',
        function(){
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_name = $(this).text();
            var data_value_name = $(this).attr('data-name');
            $(this).parent("dl").find(".select_title").text(data_name);
            $(this).parent("dl").find(".select_title2").toggle();
            $(this).parent("dl").find(".select_title3").toggle();
            $(this).parent().parent().find(".select_title").text(data_name);
            $(':input[name="'+data_value_name+'"]').val(data_value);
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select2 dd").parent("dl").find("dt"));
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
        });
    $(document).click(function() {
        $(".select2 dd").hide();
        selectStatus($(".select2 dt"));
    });

}

//当前下拉菜单状态
function selectStatus(obj) {
    if (obj.parent().find("dd").is(":hidden")) {
        otherSelectStatus(); //
        obj.parent().removeClass("zindex4").parent().find(".on").removeClass("on");
        obj.find(".arrow").removeClass("arrow_on");
    } else {
        otherSelectStatus(); //
        obj.parent().find("dd,.select_title2").show();
        obj.addClass("on");
        obj.parent().addClass("on zindex4");
        obj.find(".arrow").addClass("arrow_on");

    }
}
//其他下拉菜单状态
function otherSelectStatus() {
    $("[class^=select]").parent().find(".on").removeClass("on");
    $("[class^=select]").find(".arrow").removeClass("arrow_on");
    $("[class^=select]").find("dd,.select_title2").hide();
    $("[class^=select]").find("dl").removeClass("zindex4");
}

/*添加新/编辑模版*/
$(document).on('click', '.nsSelectPost', function() {
    layer.open({
        type: 1, 					//  页面层
        title: false, 				//	不显示标题栏
        area: ['600px', '580px'],
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        closeBtn: false, 			//	不显示关闭按钮
        shift: 1, 					//	出现动画
        content: $(".department")	//  加载主体内容
    });
    $(document).on('click', '.nsClose, .nsDetermine', function() {
        layer.closeAll(); 			// 关闭
    });
});
$(document).on('click', '.nsSelectPostMan', function() {
    getSystem ($(':input[name="role_id"]').val(),$(':input[name="zone_id"]').val());
    layer.open({
        type: 1,                    //  页面层
        title: false,               //  不显示标题栏
        area: ['600px', '580px'],
        shade: .6,                  //  遮罩
        time: 0,                    //  关闭自动关闭
        shadeClose: true,           //  遮罩控制关闭层
        closeBtn: false,            //  不显示关闭按钮
        shift: 1,                   //  出现动画
        content: $(".departmentMan")    //  加载主体内容
    });
    $('.nsClose, .nsDetermine').on('click', function() {
        layer.closeAll();           // 关闭
    });
});

//  启用星期选择
weekChk();
function weekChk(){
	$(document).on('click', '.week_box div', function(){
		var _this = $(this);
		if(_this.hasClass('cur')){
			_this.removeClass('cur');
		}else {
			_this.addClass('cur');
		};
        var _week_text = '';
        if($('.week_box .cur').length>0){
            $('.week_box .cur').each(function(){
                if(_week_text==''){
                    _week_text = $(this).attr('data-value');
                }else{
                    _week_text += ','+$(this).attr('data-value');
                };
            });
            if(_week_text!=''){
                $(':input[name="week_text"]').val(_week_text);
            };
        }else{
            $(':input[name="week_text"]').val('');
        }
	});
}

//  启用星期选择
banChk();
function banChk(){
    $(document).on('click', '.ban_box div', function(){
        var _this = $(this);
        if(_this.hasClass('cur')){
            _this.removeClass('cur');
        }else {
            _this.addClass('cur');
        };
        var banstatus = '';
        if($('.ban_box .cur').length>0){
            $('.ban_box .cur').each(function(){
                if(banstatus==''){
                    banstatus = $(this).attr('data-value');
                }else{
                    banstatus += ','+$(this).attr('data-value');
                };
            });
            if(banstatus!=''){
                $(':input[name="banstatus"]').val(banstatus);
            };
        }else{
            $(':input[name="banstatus"]').val('');
        }
    });
}

//  指定日期初始化
$(".specified-date").asDatepicker({
    mode: 'multiple', 
    calendars: 1,
});

//  指定日期
holiday();
function holiday(){
	$(document).on('click', '.holiday_box div', function(){
		var _this = $(this);
		if(_this.hasClass('cur')){
			_this.removeClass('cur').text('关闭');
		}else {
			_this.addClass('cur').text('开启');
		};
        var _week_text = '';
        if($('.holiday_box .cur').length>0){
            $('.holiday_box .cur').each(function(){
                if(_week_text==''){
                    _week_text = $(this).attr('data-value');
                }else{
                    _week_text += ','+$(this).attr('data-value');
                };
            });
            if(_week_text!=''){
                $(':input[name="holiday"]').val(_week_text);
            };
        }else{
            $(':input[name="holiday"]').val('');
        }
	});	
}

$(document).on('click', ".nsRight label", function(){
    if($(this).find(".man:checked").val()==undefined) {
        $(".nsNone").hide();
        $(".nssNone").show();
    }else{
        $(".nssNone").hide();
        $(".nsNone").show();
    }
})
$(".nssNone").hide();

$(document).on('click', ".nsRight label", function(){
    if($(this).find(".man:checked").val()==undefined) {
        $(".edsNone").hide();
        $(".edssNone").show();
    }else{
        $(".edssNone").hide();
        $(".edsNone").show();
    }
})
// $(".edssNone").hide();
if($(".nsRight label").find(".man:checked").val()==undefined) {
    $('.edsNone').find(':input[name="allocationnum"]').val('');
    $(".edsNone").hide();
    $(".edssNone").show();
}else{
    $(".edssNone").hide();
    $(".edsNone").show();
}

//  选择部门和职位弹窗全选
function chkAll(){
	$(document).on('click', '#chk_all', function(){
	if ($(this).is(':checked')) {
		$(":input[name='nsChk']").prop('checked', true);
	} else {
		$(":input[name='nsChk']").prop('checked', false);
	}
});
}


$(function(){
    var ue = UE.getEditor('editor',{
        toolbars: [
            ['fontsize','fontfamily', 'undo', 'redo','underline', 'bold','insertimage','spechars','justifyleft','justifyright','justifycenter','emotion']
        ],
        initialFrameWidth:460,
        initialFrameHeight:100,       
        enableAutoSave:false,
        elementPathEnabled:false,
        maximumWords:1000,
        autoFloatEnabled:false
    });
    
    //获取职位列表
    common_ajax2('','/SystemApi/Role/getRoleList','no',function(redata){
        if(redata.data.data){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(demo1.innerHTML).render(redata.data.data, function(result){
                    $('#role_li').append(result);
                    $('#search_body').html(result);
                });
            });
        };
    },1);
    //获取区域列表
    common_ajax2('','/SystemApi/Zone/getZoneList','no',function(redata){
        if(redata.data){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(demo2.innerHTML).render(redata.data, function(result){
                    $('#zone_li').html(result);
                    if($.getUrlParam('zone_id')){
                        var _zone_title = $('#zone_li').find('.fxDone[data-value="'+$.getUrlParam('zone_id')+'"]').text();
                        $('#zone_li').find('.select_title').text(_zone_title);
                        $(':input[name="zone_id"]').val($.getUrlParam('zone_id'));
                    };
                });
            });
        };
    },1);
    //获取通知类型
    common_ajax2('','/SystemApi/Message/getMsgType','no',function(redata){
        if(redata.data){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(demo4.innerHTML).render(redata.data, function(result){
                    $('#msg_type').html(result);
                    if($.getUrlParam('msgtype')){
                        var _msgtype_title = $('#msg_type').find('.fxDone[data-value="'+$.getUrlParam('msgtype')+'"]').text();
                        $('#msg_type').find('.select_title').text(_msgtype_title);
                        $(':input[name="msgtype"]').val($.getUrlParam('msgtype'));
                    };
                });
            });
        };
    },1);
    //选择部门职位
    $(document).on('click', '.nsDetermineRole', function(){
        var role_id = '';
        var role_name = ''
        $('.nsMiddle').find('.nssNone').remove();
        for(var i=0;i<$(':input[name="nsChk"]:checked').length;i++){
            if(i==0){
                role_id += $(':input[name="nsChk"]:checked').eq(i).val();
                role_name += $(':input[name="nsChk"]:checked').eq(i).parent().siblings('.wNsTwo').text()+'/'+$(':input[name="nsChk"]:checked').eq(i).parent().siblings('.wNsThr').text();
            }else{
                role_id += ','+$(':input[name="nsChk"]:checked').eq(i).val();
                role_name += '，'+$(':input[name="nsChk"]:checked').eq(i).parent().siblings('.wNsTwo').text()+'/'+$(':input[name="nsChk"]:checked').eq(i).parent().siblings('.wNsThr').text();
            }
            $('.dn .nssNone').children('.nsLeft').html('<i>&#42</i>'+$(':input[name="nsChk"]:checked').eq(i).parent().siblings('.wNsThr').text()+':');
            $('.nsNone').before( $('.dn .nssNone').clone());
            
        }
        $(':input[name="role_id"]').val(role_id);
        $(':input[name="role_name"]').val(role_name);
        $(':input[name="system_name"]').val('');
        $(':input[name="system_user_id"]').val('');
    });

    //获取员工列表
    $(document).on('click', '.nsSelectPostMan', function(){
        data = {
            role_ids:$(':input[name="role_id"]').val(),
            zone_id:$(':input[name="zone_id"]').val(),
        };
        common_ajax2(data,'/SystemApi/SystemUser/getSystemUserList','no',function(redata){
            if(redata.data.data){
                layui.use('laytpl', function(){
                    var laytpl = layui.laytpl;
                    laytpl(demo3.innerHTML).render(redata.data.data, function(result){
                        $('#sysuser_li').append(result);
                        $('#search_body1').html(result);
                    });
                });
            };
        },1);
    });
    $(document).on('click', '.nsDetermineSystem', function(){
        var system_id = '';
        var system_name = ''
        for(var i=0;i<$(':input[name="nsChk2"]:checked').length;i++){
            if(i==0){
                system_id += $(':input[name="nsChk2"]:checked').eq(i).val();
                system_name += $(':input[name="nsChk2"]:checked').eq(i).parent().siblings('.wNsTwo').text();
            }else{
                system_id += ','+$(':input[name="nsChk2"]:checked').eq(i).val();
                system_name += '，'+$(':input[name="nsChk2"]:checked').eq(i).parent().siblings('.wNsTwo').text();
            }
        }
        $(':input[name="system_name"]').val(system_name);
        $(':input[name="system_user_id"]').val(system_id);
    });
    //搜索职位相关-检索
    $(document).on('click', '.nsSearchSubmitRole', function(){
        $(':input[name="nsChk"]').attr('checked',false);
        var val = $(':input[name="nsSelectSearch"]').val();
        var d_val = $('.nsSelectSearch_d').text();
        $('.nsDeparMiddle .department_content').remove();
        if(val.length>0 || d_val!='全部'){
            $('#search_body .department_content').each(function(i){
                var zmnumReg=new RegExp( val ,'gim');
                var zmnumReg2=new RegExp( d_val ,'gim');
                var name=$(this);
                if(d_val!='全部'){
                    if( zmnumReg.test(name.children('.wNsThr').text()) && zmnumReg2.test(name.children('.wNsTwo').text()) ){
                        $('.department_title').after(name.clone());
                    }
                }else{
                    if( zmnumReg.test(name.children('.wNsThr').text()) ){
                        $('.department_title').after(name.clone());
                    }
                }
            });
        }else{
            $('.department_title').after( $('#search_body .department_content').clone() );
        }
    });
    $(document).on('click', '.nsSubmit', function(){
        var data = {
            title:$(':input[name="title"]').val(),
            content:ue.getContent(),
            href:$(':input[name="href"]').val(),
            zone_id:$(':input[name="zone_id"]').val(),
            msgtype:$(':input[name="msgtype"]').val(),
            role_id:$(':input[name="role_id"]').val(),
            system_user_id:$(':input[name="system_user_id"]').val()
        };
        common_ajax2(data,'/SystemApi/Message/sendMsg',0,function(redata){
            if(redata.code!=0){
                layer.msg(redata.msg,{icon:2});
            }else{
                layer.msg('发送成功',{icon:1});
                window.location.href = "/System/Information/msgList";
            };
        });
    });
    $(document).on('click', '.nsSearchSubmitSystem', function(){
        var search_name = $(this).siblings('.nsSelectSearch').val();
        var role_id = $(':input[name="role_id"]').val();
        var zone_id = $(':input[name="zone_id"]').val();
        getSystem (role_id,zone_id,search_name);
    });

});
function getSystem (role_ids,zone_id,search_name){
    $.ajax({
        url:"{:U('System/Information/sendMsg')}",
        dataType:'json',
        type:'post',
        data:{type:'getSystem',role_id:role_ids,zone_id:zone_id,keyname:search_name},
        success:function(reflag){
            $('.systemli').remove();
            if(reflag.code==0){
                var html = '';
                $.each(reflag.data,function (k,v) {
                    html+= '<dl class="clearfix systemli"><dd class="wNsOne"><input type="checkbox" name="nsChk2" class="nsSelectChk" value="'+v.system_user_id+'"></dd><dd class="wNsTwo">'+v.role_names+'</dd><dd class="wNsThr">'+v.realname+'</dd></dl>';
                })
                $('.sysuser_li').append(html);

                var ids = $(':input[name="system_user_id"]').val();
                if(ids){
                    ids = ids.split(',');
                    for(var i=0;i<ids.length;i++){
                        $('.btn_'+ids[i]).attr('checked',true);
                    }
                }
            }else{
                layer.msg(reflag.msg, {icon:2})
            }
        }
    });
}
