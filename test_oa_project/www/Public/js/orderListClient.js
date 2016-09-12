//  筛选条件展开
$(".arrowFather1").click(function(){
	$(".arrowFather1").hide();
	$(".frame").show();
	$(".arrowFather").show();
	$(".frame1").hide();
	$('.confirmBtn').removeClass('dn');
	//  筛选条件之日期选择
	$(".createdTime, .completeTime").glDatePicker({});
});
//  筛选条件收起
$(".arrowFather").click(function(){
	$(".frame").hide();
	$(".arrowFather").hide();
	$(".frame1").show();
	$(".arrowFather1").show();
	$('.confirmBtn').addClass('dn');
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
    $('.frame .on_hover').each(function(k,obj){
        var val = $(obj).html();
        if(val!='全部'){
            str += '<li><a href="javascript:;">'+$(obj).parent().parent().siblings('span').html()+''+val+'</a></li>';
        }
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

//  每行显示“详情”
$('#forTable tbody  tr').each(function(index, e) {			
    $(e).hover(function(e){
		var e = e || window.event;
		
		var _x=(e.pageX?e.pageX:e.clientX)+9;
		var _y=parseInt($(this).offset().top)+9;  
		
  
	   $(this).find('.detailLink').css({"left":_x,"top":_y});
	   $(this).find('.detailLink').show();				 
		 			 
	},function(e){	
		
	  	$(this).find('.detailLink').hide();
		
	});
});
$('.detailLink').on("mouseover",function(e){

	 var e = e || window.event;	
	 e.stopPropagation();
	 e.preventDefault(); 
	 return false;
});
$('.detailLink').on("mouseenter",function(e){
	 var e = e || window.event;	
	 e.stopPropagation();
	 e.preventDefault(); 
	 return false;
});
$('.detailLink').on("mouseleave",function(e){
	 var e = e || window.event;	
	 e.stopPropagation();
	 e.preventDefault(); 
	 return false;
});
$('.detailLink').on("mouseout",function(e){
	 var e = e || window.event;	
	 e.stopPropagation();
	 e.preventDefault(); 
	 return false;
});

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
$('.afterRefund, .returnBtn').on('click', function(){
    $.colorbox({
        inline: true,
        href: $(".returnBox"),
        overlayClose: false,
        title: "退回缴费"
    });
    
    //  退款日期选择
    setTimeout(function(){
		$(".refundTime").glDatePicker({});
    },1000)
});
//  关闭colorbox弹窗
$('.raConfirm').on('click',function(){
	$('.returnBox').colorbox.close();
});

//  收款
$('.receivablesBtn').on('click', function(){
    $.colorbox({
        inline: true,
        href: $(".receivablesBox"),
        overlayClose: false,
        title: "确认收款"
    });
    
    //  收款日期选择
    setTimeout(function(){
		$(".receivablesTime").glDatePicker({});
    },1000)
});
//  关闭colorbox弹窗
$('.receivablesConfirm').on('click',function(){
	$('.receivablesBox').colorbox.close();
});

//  审核
$('.toExamine').on('click', function(){
    $.colorbox({
        inline: true,
        href: $(".auditOrderBox"),
        overlayClose: false,
        title: "确认收款"
    });
    
    //  收款日期选择
    setTimeout(function(){
		$(".auditOrderTime").glDatePicker({});
    },1000)
});
//  关闭colorbox弹窗
$('.auditPass, .auditNotPassed').on('click',function(){
	$('.auditOrderBox').colorbox.close();
});