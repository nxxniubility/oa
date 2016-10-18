/**
 * Created by Administrator on 2016/5/19.
 */
// $(".arrowFather1").click(function(){

// 	$(".arrowFather1").slideUp(90);
// 	$(".frame").slideDown(900);
// 	$(".arrowFather").slideDown(90);
// 	$(".frame1").slideUp(900);
// });
// $(".arrowFather").click(function(){

// 	$(".frame").slideUp(900);
// 	$(".arrowFather").slideUp(90);
// 	$(".frame1").slideDown(900);
// 	$(".arrowFather1").slideDown(90);
// });

/**********快速筛选**********/
var choice=function(e,t){
    t===undefined&&(t=900);
    $(e).click(function(){
        $(".arrowFather,.arrowFather1").toggle();
        $(".frame,.frame1").slideToggle(t);
    })
};
choice(".arrowFather1,.arrowFather");



$(".proContMiddle dl").addClass("hover");

$('#userDefined').click(function(){
    //$(".panel").show()
    layer.open({
        type: 1, 					//  页面层
        title: '自定义显示列', 				//	不显示标题栏
        area: ['600px', '575px'],
        closeBtn:2,
        shade: 0, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $(".panelConcentNew")	//  加载主体内容
    });
});
/*创建订单*/
$(document).on('click', '.btn_reserve', function(){
    var data = {
        type : 'ishint',
        user_id:$(':input[name="temp_user_id"]').val()
    };
    //获取提示
    common_ajax2(data, createOrder_href, 'no', getHint);
    function getHint(reflag){
        $('#reserve_hint').empty();
        if(reflag.code!=0){
            $('#reserve_hint').html('<span style="color: red; margin-left: 118px;">'+reflag.msg+'</span>');
        }else{
            $('#reserve_hint').html('<span style="color:green; margin-left: 118px;">'+reflag.msg+'</span>');
        };
    };

    $(':input[name="temp_user_id"]').val($(this).parent('ul').attr('data-value'));
    $(':input[name="reserve_realname"]').val($(this).parent('ul').attr('data-realname'));
    $(':input[name="reserve_username"]').val($(this).parent('ul').attr('data-username'));
    layer.open({
        type: 1, 					//  页面层
        title: '创建订单', 				//	不显示标题栏
        area: ['490px', '340px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $("#panel2"),	//  加载主体内容
        scrollbar: false
    });
});

/******************
 *  短信部分 star  *
 ******************/
/**
 * 发短信
 * */
var sms_template_list = '';
var msgBox = '';
$(document).on('click','.btn_msg', function(){
    $(':input[name="temp_user_id"]').val($(this).parent('ul').attr('data-value'));
    var realname = $(this).parents('ul').attr('data-realname');
    var username = $(this).parents('ul').attr('data-username');
    $('.msg_realname').text(realname);
    $('.msg_username').text(username);
    $('.msg_myname').text(myname);
    $('#setMsgBox').find('.msgTxt').val('');
    //获取模版
    var data = {
        type:'getTemplate'
    };
    common_ajax2(data, sendSms_href, 'no', template);
    //$.colorbox({
    //    inline: true,
    //    href: $("#setMsgBox"),
    //    overlayClose: false,
    //    title: "发送短信"
    //});
    msgBox = layer.open({
        type: 1, 					//  页面层
        title: '发送短信', 				//	不显示标题栏
        area: ['569px', 'auto'],
        closeBtn: 1,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: false, 			//	遮罩控制关闭层
        shift: 5, 					//	出现动画-5 闪现
        content: $("#setMsgBox"),	//  加载主体内容
        scrollbar: false
    });


});
function template(reflag){
    $('.msg_template').empty();
    if(reflag.code==0){
        var strHtml = '<option selected="selected" value="0">请选择短信模版</option>';
        $.each(reflag.data,function(k,v){
            strHtml += '<option value="'+ v.sms_template_id+'" data-value="'+ v.template+'">'+ v.templatename+'</option>';
        });
        $('.msg_template').html(strHtml);
        sms_template_list = reflag.data;
    };
};
$('.msgBtnCancel').on('click',function(){
    layer.close(msgBox);
	$('#setMsgBox').colorbox.close();
});

//短信发送
$('.msgBtnConfirm').on('click',function(){
    var phone = $(this).parents('.setMsgBox').find('.msg_username').text();
    var sendTxt = $(this).parents('.setMsgBox').find('.msgTxt').val();
    if(sendTxt.length==0){
        layer.msg('短信内容不能为空', {icon:2});
        return false;
    }else if(sendTxt.length>200){
        layer.msg('短信内容不能大于200字符', {icon:2});
        return false;
    }else if(checkQuote(sendTxt)){
        layer.msg('模板名称或者模板内容 请避免敏感词', {icon:2});
        return false;
    }
    var data = {
        type : 'send',
        phone : phone,
        sendTxt : sendTxt,
        user_id : $(':input[name="temp_user_id"]').val()
    };
    common_ajax2(data,sendSms_href, 'no');
    //$('#setMsgBox').colorbox.close();
});

/**
 * 下拉默认提示语选择
 * */
$('#setSelect').change(function(){
	var opVal = $(this).children('option:selected').val(),
        opData = $(this).children('option[value="'+opVal+'"]').attr('data-value'),
        msgTxt = $(this).parent().next().find('.msgTxt'),
        username = $('#setMsgBox').find('.msg_realname').text(),
        userphone = $('#setMsgBox').find('.msg_username').text();

	if(opVal == 0){
		msgTxt.val('');
	}else {
        opData = opData.replace(/{username}/i, username);
        opData = opData.replace(/{myname}/i, myname);
        opData = opData.replace(/{myphone}/i, myphone);
        msgTxt.val(opData);
	}
});

var setBox = '';
var addBox = '';
var tpBox = '';
/**
 * 设置短信模版
 * */
$('.setSMSTemplate').on('click', function(){
    if(sms_template_list.length>0){
        var strHtml = '<tr id="fixedPosition"> <th class="thName">短信模板名称</th> <th>创建时间</th> <th>操作</th> </tr>';
        var strHtml2 = '';
        $.each(sms_template_list,function(k,v){
            strHtml2 += '<tr class="template_'+ v.sms_template_id+'"> <td class="tdName">'+ v.templatename+'</td> <td>'+ v.create_time+'</td> <td data-id="'+v.sms_template_id+'"> <span onclick="edit_template('+v.sms_template_id+',\''+v.templatename+'\',this)" data-value="'+v.template+'">修改</span> <span onclick="del_template('+v.sms_template_id+')">删除</span> </td> </tr>';
        });
        if(strHtml2==''){
            $('.notMsg').show();
            $('.setMsgTemplate_list').hide();
        }else{
            $('.setMsgTemplate_list').html(strHtml+strHtml2).show();
            $('.notMsg').hide();
        }
    }

    $(':input[name="create_name"]').val('');
    $(':input[name="create_template"]').val('');
    $('.show_templatename,.show_template').text('');

	//  关闭发送短信弹窗
	//$('#setMsgBox').colorbox.close();

    setBox = layer.open({
        type: 1, 					//  页面层
        title: false, 				//	不显示标题栏
        area: ['584px', 'auto'],
        closeBtn: 0,
        shade: 0, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: false, 			//	遮罩控制关闭层
        shift: 5, 					//	出现动画-5 闪现
        content: $(".setMsgTemplate"),	//  加载主体内容
        scrollbar: false
    });
    //  设置短信模版关闭
	$('.setMsgTemplate .temTit').find('i').on('click',function(){
		layer.close(setBox);
        //获取模版
        var data = {
            type:'getTemplate'
        };
        common_ajax2(data, sendSms_href, 'no', template);
	});

	//  点击“添加新模板”和“返回上一步修改”，弹窗的切换 .prevBtn
	$('.addTemBtn').on('click',function(){
        if($(this).hasClass('addTemBtn')){
            $(':input[name="create_name"]').val('');
            $(':input[name="create_template"]').val('');
        }
        $('.show_templatename,.show_template').html('');
		//  关闭设置短信模版弹窗
        layer.close(setBox);
        layer.close(addBox);
        addBox = '';

        if($(this).hasClass('edit')){
            var box = $('.editMsg');
        }else{
            var box = $('.addMsg');
        }
        addBox = layer.open({
		    type: 1, 					//  页面层
		    title: false, 				//	不显示标题栏
		    area: ['584px', 'auto'],
		    closeBtn: 0,
            shade : 0, 					//	遮罩
		    time: 0, 					//  关闭自动关闭
		    shadeClose: false, 			//	遮罩控制关闭层
		    shift: 5, 					//	出现动画-5 闪现
		    content: box,	//  加载主体内容
		    scrollbar: false
		});
	});
});
////  模板保存
$('.prevBtn').on('click',function(){
    layer.close(tpBox);
});
//  点击“预览，并进行下一步”，弹窗的切换
$('.nextBtn').on('click',function(){
    if(!$(this).hasClass('edit')){
        if($(':input[name="create_name"]').val().length==0 || $(':input[name="create_template"]').val().length==0){
            layer.msg('模板名称或者模板内容 不能为空', {icon:2});
            return false;
        }else if(checkQuote($(':input[name="create_template"]').val())){
            layer.msg('模板名称或者模板内容 请避免敏感词', {icon:2});
            return false;
        }
        var t_name = $(':input[name="create_name"]').val(),
            t_template = $(':input[name="create_template"]').val();
        $('.prevBtn').removeClass('edit');
        $('.createTemplate_subtn').removeClass('edit');
    }else{
        if($(':input[name="edit_name"]').val().length==0 || $(':input[name="edit_template"]').val().length==0){
            layer.msg('模板名称或者模板内容 不能为空', {icon:2});
            return false;
        }else if(checkQuote($(':input[name="edit_template"]').val())){
            layer.msg('模板名称或者模板内容 请避免敏感词', {icon:2});
            return false;
        }
        var t_name = $(':input[name="edit_name"]').val(),
            t_template = $(':input[name="edit_template"]').val();
        $('.prevBtn').addClass('edit');
        $('.createTemplate_subtn').addClass('edit');
    }
    //  关闭设置短信模版弹窗
    //layer.close(addBox);
    var username = $('#setMsgBox').find('.msg_realname').text(),
        userphone = $('#setMsgBox').find('.msg_username').text();
    //模版内容转换
    t_template = t_template.replace(/{username}/i, username);
    t_template = t_template.replace(/{myname}/i, myname);
    t_template = t_template.replace(/{myphone}/i, myphone);
    //赋值
    $('.show_templatename').text(t_name);
    $('.show_template').text('【泽林】'+t_template);

    tpBox = layer.open({
        type: 1, 					//  页面层
        title: false, 				//	不显示标题栏
        area: ['584px', 'auto'],
        closeBtn: 0,
        shade: 0, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: false, 			//	遮罩控制关闭层
        shift: 5, 					//	出现动画-5 闪现
        content: $(".templatePreview"),	//  加载主体内容
        scrollbar: false
    });
    //  预览模版关闭
    $('.templatePreview .tpTit').find('i').on('click',function(){
        layer.close(tpBox);
    });
});
//  添加短信模版关闭
$(document).on('click','.addclose,.editclose',function(){
    layer.close(addBox);
    //获取模版
    var data = {
        type:'getTemplate'
    };
    common_ajax2(data, sendSms_href, 'no', template);
});

//创建模版
$('.createTemplate_subtn').on('click',function(){
    if(!$(this).hasClass('edit')){
        var data = {
            type:'createTemplate',
            templatename:$(':input[name="create_name"]').val(),
            template:$(':input[name="create_template"]').val()
        };
    }else{
        var data = {
            type:'editTemplate',
            sms_template_id:$(':input[name="edit_template_id"]').val(),
            templatename:$(':input[name="edit_name"]').val(),
            template:$(':input[name="edit_template"]').val()
        };
    }
    common_ajax2(data,sendSms_href, 'no', createFlag);
    function createFlag(reflag){
        if(reflag.code==0){
            layer.close(setBox);
            layer.close(addBox);
            layer.close(tpBox);
            layer.msg(reflag.msg, {icon:1});
            //获取模版
            var data = {
                type:'getTemplate'
            };
            common_ajax2(data, sendSms_href, 'no', template);
        }else{
            layer.msg(reflag.msg, {icon:2});
        }
    }
});

//修改短信模版
function edit_template(id,name,obj){
    //  点击“添加新模板”和“返回上一步修改”，弹窗的切换
    var templateVal = $(obj).attr('data-value');
    $(':input[name="edit_name"]').val(name);
    $(':input[name="edit_template"]').val(templateVal);
    $('.show_templatename,.show_template').html('');
    $(':input[name="edit_template_id"]').val(id);
    //  关闭设置短信模版弹窗
    layer.close(setBox);

    addBox = layer.open({
        type: 1, 					//  页面层
        title: false, 				//	不显示标题栏
        area: ['584px', 'auto'],
        closeBtn: 0,
        shade: 0, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: false, 			//	遮罩控制关闭层
        shift: 5, 					//	出现动画-5 闪现
        content: $(".editMsg"),	//  加载主体内容
        scrollbar: false
    });
    //  模板保存
    $('.saveBtn').on('click',function(){
        layer.closeAll();
    });

}

//删除短信模版
function del_template(id){
    var data = {
        type:'delTemplate',
        sms_template_id : id
    };
    common_ajax2(data,sendSms_href, 'no', del_template_list);

    function del_template_list(reflag){
        if(reflag.code==0){
            $('.template_'+id).remove();
            layer.msg(reflag.msg, {icon:1});
        }else{
            layer.msg(reflag.msg, {icon:2});
        }
    }
};

//  光标定位赋值
$(".btns_btn").on('click',function(){
    $(this).parents('.clearfix').siblings(".editTxt").insertAtCaret($(this).attr('data-value'));
});






/*****************
 *  短信部分 end  *
 *****************/



//  确认客户到访
$(document).on('click', '.btn_visited', function(){
    $(':input[name="temp_user_id"]').val($(this).parent('ul').attr('data-value'));
    $('#manually .myRow').eq(0).children('em').html($(this).parent('ul').attr('data-realname'));
    $('#manually .myRow').eq(1).children('em').html($(this).parent('ul').attr('data-username'));
    $('#manually .myRow').eq(2).children('em').html($(this).parent('ul').attr('data-tel'));
    $('#manually .myRow').eq(3).children('em').html($(this).parent('ul').attr('data-qq'));
    $.colorbox({
        inline: true,
        href: $("#manually"),
        overlayClose: false,
        title: "确认客户到访"
    });
});
//  确认客户到访

//  客户到访提醒
/*$('.btn_visited').on('click', function(){
    $.colorbox({
        inline: true,
        href: $("#automatic"),
        overlayClose: false,
        title: "客户到访提醒"
    });
});*/
//  客户到访提醒
$(".panel>.panelConcent>p>b").click(function(){$(".panel").hide()});

$(".panel1>.panelConcent>p>b").bind("click",function(){$(".panel1").hide()});

$(".giveUp em").bind("click",function(){$(".panel1").show()});

$(".panel2>.panelConcent>p>b").bind("click",function(){$(".panel2").hide()});

$(".apply em").bind("click",function(){$(".panel2").show()});


$(".panel3 .Capacity .wSev i").click(function(){$(".panel3").hide()});

$(".panel3>.panelConcent>p>b").bind("click",function(){$(".panel3").hide()});

$(".out em").bind("click",function(){$(".panel3").show()});

// $(".edit em").bind("click",function(){
//     if ($(".edit span").hasClass("setEdit")) {
//         $(".edit span").removeClass("setEdit");
//         $(".edit span").addClass("setEdit1");
//         $(".edit em").html("标为普通");
//     }else{
//         $(".edit span").removeClass("setEdit1");
//         $(".edit span").addClass("setEdit")
//         $(".edit em").html("标为重点");
//     };
// });

$(".column_name").click(function(){
    if($(this).is(':checked')){
        $(this).parents('.wOne').siblings('.wThr').children('input').attr("disabled",false);
    }else{
        $(this).parents('.wOne').siblings('.wThr').children('input').attr("disabled",true);
    }
});
//筛选选择
$('.details').find('a').click(function(){
    //是否赋值？
    var data_value = $(this).attr('data-value');
    var data_id = $(this).parents('ul').attr('data-id');
    var data_num = $(this).attr('data-num');
    //是否多选？
    if($(this).hasClass('multiple')){
        if($(this).hasClass('on_hover')){
            $(this).removeClass('on_hover');
        }else{
            $(this).addClass('on_hover');
        }
        data_value = '';
        $(this).parents('ul').find('.on_hover').each(function(){
            if(data_value==''){
                data_value += $(this).attr('data-value');
            }else{
                data_value += ','+$(this).attr('data-value');
            };
        });
    }else{
        $(this).parent().parent().find('a').removeClass('on_hover');
        $(this).addClass('on_hover');
    };
    if($(this).parent().parent().hasClass('specialul')){
        $(".specialul").css("height","51px");
    };
    //是否隐藏时间？
    if($(this).parent().hasClass('clickli')){
        if($(this).parent().parent().hasClass('specialul')){
            $(".specialul").css("height","102px");
        };
        $(':input[name="'+data_id+'"]').val('1@0');
        $(this).parent().siblings(".selectbox1").css("visibility","visible");
        $(this).parent().siblings('.selectbox1').children('.afTime').val('').glDatePicker({
            onClick:function(el, cell, date, data) {
                el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
                if(el.parent("li").hasClass("start")){
                    var start_time=el.val();
                    var end_time=el.parents("ul").find(".end input").val();
                    if(end_time.length>0){
                        $(':input[name="'+data_id+'"]').val(data_num+'@'+start_time+'@'+end_time);
                    }
                }else if(el.parent("li").hasClass("end")){
                    var start_time=el.parents("ul").find(".start input").val();
                    var end_time=el.val();
                    if(!start_time){
                        var myDate = new Date();
                        start_time = myDate.getFullYear()+'/'+myDate.getMonth()+'/'+myDate.getDate();
                    }
                    $(':input[name="'+data_id+'"]').val(data_num+'@'+start_time+'@'+end_time);
                };
            }
        });
    }else{
        $(':input[name="'+data_id+'"]').val(data_value);
        if($(this).parent().siblings(".selectbox1").length>0){
            $(this).parent().siblings(".selectbox1").css('visibility', 'hidden');

        };
    };
});
//筛选渠道获取列表
$(':input[name="channel_sele"],:input[name="zone_sele"],:input[name="role_sele"],:input[name="system_sele"],:input[name="system_type_sele"]').change(function(){
    var data_id = $(this).attr('data-id');
    $(':input[name="'+data_id+'"]').val($(this).val());
    if( data_id!='channel_id' && data_id!='system_user_id' && data_id!='system_type' ){
        getsystem();
    }
});
//表单提交搜索
$('#subSearch').on('click',function(){
    frame();
});

//全选
$(document).on('click', '#batchDeleteChk', function(){
	if ($(this).is(':checked')) {
		$(":input[name='librayChk']").prop('checked', true);
	} else {
		$(":input[name='librayChk']").prop('checked', false);
	}
});

$(function(){
	$('.clickli').each(function(index,domEle){
		if($('.clickli').eq(index).siblings('.start').css('visibility')=="visible"){
			$('.clickli').eq(index).siblings('.selectbox1').children(".afTime").glDatePicker({onClick:function(el, cell, date, data) {
				el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
			    if(el.parent("li").hasClass("start")){
			        var start_time=el.val();
			        var end_time=el.parents("ul").find(".end input").val();
			        var url = el.parents("ul").find(".end input").attr('data-url');
			        if(end_time.length>0 &&　end_time!='结束时间'){
			            location.href=url+start_time+'@'+end_time;
			        }
			    }else if(el.parent("li").hasClass("end")){
					var start_time=el.parents("ul").find(".start input").val();
					var end_time=el.val();
			        if(!start_time){
			            var myDate = new Date();
			            start_time = myDate.getFullYear()+'/'+myDate.getMonth()+'/'+myDate.getDate();
			        }
					location.href=el.attr('data-url')+start_time+'@'+end_time;
				}
			}});
		}
	});
});

function frame(){
    var str = '';
    $('.frame .on_hover').each(function(k,obj){
        var val = $(obj).html();
        if(val!='全部'){
            str += '<li><a href="javascript:;">'+$(obj).parent().parent().siblings('span').html()+''+val+'</a></li>';
        }
    });
    $('.frame1').children('ul').html(str);
    return false;
};


function listBody(data_column){
    if(data_column!=0){
        //插入数据
        $.each(data_column,function(k,v){
            $('.title_'+ v.columnname).attr('isShow','true').attr('sort',v.sort);
            $('.content_'+ v.columnname).attr('isShow','true').attr('sort',v.sort);
            $('.column_'+ v.columnname).attr('checked',true).parents('.wOne').siblings('.wThr').children('input').val(v.sort).attr("disabled",false);
        });
        //过滤
        $('.tr_title,.tr_content').each(function(i){
            if($(this).attr('isShow')!='true'){
                $(this).remove();
            };
        });
        //排序
        var sortTitle = $('#title .tr_title').sort(function(a, b) {
            return $(a).attr('sort') - $(b).attr('sort');
        });
        $('#title').empty().append(sortTitle);
        $('.content_li').each(function(){
            var _thisObj = $(this);
            var sortContent = _thisObj.find('.tr_content').sort(function(a, b) {
                return $(a).attr('sort') - $(b).attr('sort');
            });
            _thisObj.empty().append(sortContent);
        });
    }else{
        $(':input[name="Fruit"]').prop('checked',true).parents('.wOne').siblings('.wThr').children('input').attr("disabled",false);
    };
    if($('.tr_title').length<=10){
        $('.tr_title,.tr_content').css('width',(parseInt($('.listBody').width())/$('.tr_title').length)+'px');
        $('.listBody').css('overflow-x','hidden');
    }
    return false;
};
$('.content_li,.box_li,.dispost_li').mousemove(function(){
	$('.on_li').removeClass('on_li');
    var num = $(this).index();
    $('.content_li').eq(num).addClass('on_li');
    $('.box_li').eq(num).addClass('on_li');
    $('.dispost_li').eq(num).addClass('on_li');
}).mouseout(function(){
    var num = $(this).index();
    $('.content_li').eq(num).removeClass('on_li');
    $('.box_li').eq(num).removeClass('on_li');
    $('.dispost_li').eq(num).removeClass('on_li');
});

//  操作
$(document).on('click','.proSelect', function(event){
    //阻止点击document对当前事件的影响
    event.stopPropagation();
    $(this).next().toggle().closest('tr').siblings().find('.otherOperation').hide();
});
$(document).click(function() {
	$('.otherOperation').hide();
});
/**//**
 * 检查输入的字符是否具有特殊字符
 * 输入:str  字符串
 * 返回:true 或 flase; true表示包含特殊字符
 * 主要用于注册信息的时候验证
 */
function checkQuote(str){
    var items = new Array( "培训", "课程", "贷款", "老师");
    str = str.toLowerCase();
    for (var i = 0; i < items.length; i++) {
        if (str.indexOf(items[i]) >= 0) {
            return true;
        };
    };
    return false;
};