//  筛选条件展开
$(document).on("click",".arrowFather1",function(){
	$(".arrowFather1").hide();
	$(".frame").slideDown(900);
	$(".arrowFather").show();
	$(".frame1").hide();
	
	//  筛选条件之日期选择
	$(".createdTime, .completeTime").glDatePicker({});
});
//  筛选条件收起
$(document).on("click",".arrowFather",function(){
	$(".frame").slideUp(900);
	$(".arrowFather").hide();
	$(".frame1").show(900);
	$(".arrowFather1").show();
    frame();
});

//  筛选条件之单选
$('.details').find('a').click(function(){
    $(this).parent().parent().find('a').removeClass('on_hover');
    $(this).addClass('on_hover');
    frame();
});

//  筛选条件之多选
$('.details2').find('a').click(function(){
	if($(this).hasClass('on_hover')){
		$(this).removeClass('on_hover');
	}else {
		$(this).addClass('on_hover');
	}
	
});

//  筛选条件之单选（赋值?）
function frame(){
    var str = '';
    $('.frame').children('.clearfix').each(function(k,obj){
        if($(obj).children('ul').attr('id')=='order_status'){
            str += '<li><a href="javascript:;">'+$(obj).children('span').html()+'';
            $(obj).find('.on_hover').each(function(k2,obj2){
                str += $(obj2).html()+' ';
            });
            str +='</a></li>';
        }else{
            var title = $(obj).children('span').text();
            var val = $(obj).find('.on_hover').text();
            if(title.length>1 && title!=''){
                if(val==''){
                    if($(obj).find('.select_title').length>0){
                        val = $(obj).find('.select_title').text();
                    }else if($(obj).find('input').length>0){
                        var timeS = $(obj).find('input').eq(0).val();
                        var timeE = $(obj).find('input').eq(0).val();
                        if(timeS!='' && timeE!=''){
                            val = timeS+' '+timeE;
                        }else{
                            val = ' 全部';
                        };
                    }else{
                        val = ' 全部';
                    };
                };
                if(val!=' 全部' && val!='全部' && val!='全部区域' && val!='全部职位' && val!='全部所属人'){
                    str += '<li><a href="javascript:;">'+$(obj).children('span').html()+''+val+'</a></li>';
                };
            };
        };
    });
    $('.frame1').children('ul').html(str);
    return false;
};

//  列表
$('#forTable tr:last-child').find('td').css('borderBottom','none');
$('.otherIcon li:last-child').css('borderBottom','none');

//  列表操作
$(document).on('click', '.forSelect',function(event){
    $(this).next().toggle().closest('tr').siblings().find('.otherOperation').hide();
    //阻止点击document对当前事件的影响
    event.stopPropagation();
});

 $(document).click(function() {
	$('.otherOperation').hide();
});


/*var  fee_logs_id; //预报的记录ID
//  确定收款
$('.receivablesBtn').on('click', function(){
    var _data = $(this).attr('data-value');
    _data = _data.split('::');
    fee_logs_id = _data[0];
    for(var i=0;i<3;i++){
        $("#receivables .reRow").eq(i).children('em').text(_data[i+1]);
    }
    $.colorbox({
        inline: true,
        href: $("#receivables"),
        overlayClose: false,
        title: "确认预报收款"
    });
});

$('.reCencel').on('click',function(){
	$("#receivables").colorbox.close();
});


//  退回申请
$('.returnBtn').on('click', function(){
    var _data = $(this).attr('data-value');
    _data = _data.split('::');
    fee_logs_id = _data[0];
    for(var i=0;i<3;i++){
        $("#returnApplication .raRow").eq(i).children('em').text(_data[i+1]);
    }
    $.colorbox({
        inline: true,
        href: $("#returnApplication"),
        overlayClose: false,
        title: "退回预报申请"
    });
});
$('.raCencel').on('click',function(){
	$("#returnApplication").colorbox.close();
});

//  退回预报款项
$('.refundBtn').on('click', function(){
    var _data = $(this).attr('data-value');
     _data = _data.split('::');
     fee_logs_id = _data[0];
     for(var i=0;i<3;i++){
     $("#refundPay .refundRow").eq(i).children('em').text(_data[i+1]);
     }
    $.colorbox({
        inline: true,
        href: $("#refundPay"),
        overlayClose: false,
        title: "退回预报款项"
    });
});
$('.refundCencel').on('click',function(){
    $("#refund").colorbox.close();
});*/


selectbox();
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

//当前下拉菜单状态
function selectStatus(obj) {
    if (obj.parent().find("dd").is(":hidden")) {
        otherSelectStatus(); //
        obj.parent().removeClass("zindex4").parent().find(".on").removeClass("on");
        obj.find(".arrow").removeClass("arrow_on");
    } else {
        otherSelectStatus(); //
        obj.parent().find("dd,.select_title2").show();
        obj.parent().find("dd,.select_title3").show();
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
    $("[class^=select]").find("dd,.select_title3").hide();
    $("[class^=select]").find("dl").removeClass("zindex4");
}

//  退款
$(document).on('click','.afterRefund, .returnBtn', function(){
    //提示信息
    var data_id = $(this).parents('td').attr('data-id');
    var data = $(this).parents('td').attr('data-value');
    data = data.split('==');
    $(".returnBox").find('.add').each(function(i){
        $(this).children('em').text(data[i]);
    });
    $('input[name="order_id"]').val(data_id);
    $.colorbox({
        inline: true,
        href: $(".returnBox"),
        overlayClose: false,
        title: "退回缴费"
    });
    
    //  退款日期选择
    setTimeout(function(){
        var myDate = new Date();
        ymd = myDate.getFullYear()+'/'+(myDate.getMonth()+1)+'/'+myDate.getDate();
        $(".refundTime").val(ymd).glDatePicker({
            selectableDateRange: [
                {
                    from: new Date(myDate.getFullYear(), (myDate.getMonth()-1), (myDate.getDate()-7)),
                    to: new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate())
                }
            ]
        });
    },1000)
});
////  关闭colorbox弹窗
//$('.raConfirm').on('click',function(){
//	$('.returnBox').colorbox.close();
//});

//  退订金
$(document).on('click','.depositBtn', function(){
    //提示信息
    var data_id = $(this).parents('td').attr('data-id');
    var data = $(this).parents('td').attr('data-value');
    data = data.split('==');
    $(".depositBox").find('.add').each(function(i){
        $(this).children('em').text(data[i]);
    });
    $('input[name="order_id"]').val(data_id);
    $.colorbox({
        inline: true,
        href: $(".depositBox"),
        overlayClose: false,
        title: "退回订金"
    });

    //  退订金日期选择
    setTimeout(function(){
        var myDate = new Date();
        ymd = myDate.getFullYear()+'/'+(myDate.getMonth()+1)+'/'+myDate.getDate();
        $(".depositBoxTime").val(ymd).glDatePicker({
            selectableDateRange: [
                {
                    from: new Date(myDate.getFullYear(), (myDate.getMonth()-1), (myDate.getDate()-7)),
                    to: new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate())
                }
            ]
        });
    },1000)
});
//  关闭colorbox弹窗
//$('.raConfirm').on('click',function(){
//	$('.depositBox').colorbox.close();
//});
//  收款
$(document).on('click','.receivablesBtn', function(){
    //提示信息
    var data_id = $(this).parents('td').attr('data-id');
    var data = $(this).parents('td').attr('data-value');
    data = data.split('==');
    $(".receivablesBox").find('.add').each(function(i){
        $(this).children('em').text(data[i]);
    });
    $('input[name="order_id"]').val(data_id);
    $.colorbox({
        inline: true,
        href: $(".receivablesBox"),
        overlayClose: false,
        title: "确认收款",
        onCleanup:function(){ 
        	var _this = $('#cboxLoadedContent').find($('input[name="receivables_cost"]')), 
        		_select = $('#cboxLoadedContent').find('.payment_method'),
        		_dl = _select.find('dl'),
        		_dt = _select.find('dt'),
        		_dtTxt = _dt.find('.select_title'),
        		_dtArrow = _dt.find('.arrow'),
        		_dd = _select.find('dd'),
        		_val  = _this.val();
        	if(_dtTxt.text() != '选择方式'){
        		_dtTxt.text('选择方式');
        		_select.removeClass('on zindex4');
        		_dt.removeClass('on');
        		_dtArrow.removeClass('arrow_on');
        		_dd.hide();
        	}
        	if(_val > 0){
        		_val = '';
        		_this.val('') ; 
        	}
        },
    });
    
    //  收款日期选择
    setTimeout(function(){
        var myDate = new Date();
        ymd = myDate.getFullYear()+'/'+(myDate.getMonth()+1)+'/'+myDate.getDate();
		$(".receivablesTime").val(ymd).glDatePicker({
            selectableDateRange: [
                {
                    from: new Date(myDate.getFullYear(), (myDate.getMonth()-3), (myDate.getDate()-7)),
                    to: new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate())
                }
            ]
        });
    },1000)
});
////  关闭colorbox弹窗
//$('.receivablesConfirm').on('click',function(){
//	$('.receivablesBox').colorbox.close();
//});

//  审核
$(document).on('click','.toExamine', function(){
    //提示信息
    var data_id = $(this).parents('td').attr('data-id');
    var data = $(this).parents('td').attr('data-value');
    data = data.split('==');
    $(".auditOrderBox").find('.add').each(function(i){
        $(this).children('em').text(data[i]);
    });
    $('input[name="order_id"]').val(data_id);
    var data = {
        type : 'ishint',
        order_id:data_id
    };
    //获取提示
    common_ajax2(data, auditOrder_href, 'no', getHint);
    function getHint(reflag){
        $('#audit_hint').empty();
        if(reflag.code!=0){
            $('#audit_hint').css('color','red').html(reflag.msg);
        }else{
            $('#audit_hint').css('color','green').html(reflag.msg);
        };
    };
    $.colorbox({
        inline: true,
        href: $(".auditOrderBox"),
        overlayClose: false,
        title: "确认收款"
    });

    //  收款日期选择
    setTimeout(function(){
        var myDate = new Date();
        ymd = myDate.getFullYear()+'/'+(myDate.getMonth()+1)+'/'+myDate.getDate();
        $(".auditOrderTime").val(ymd).glDatePicker({
            selectableDateRange: [
                {
                    from: new Date(myDate.getFullYear(), (myDate.getMonth()-1), (myDate.getDate()-7)),
                    to: new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate())
                }
            ]
        });
    },1000)
});

//
////  关闭colorbox弹窗
//$('.auditPass, .auditNotPassed').on('click',function(){
//	$('.auditOrderBox').colorbox.close();
//});