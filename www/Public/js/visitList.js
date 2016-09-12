

//  列表边框控制
$('#viTable tr:last-child').find('td').css('borderBottom','none');

//
////  客户是回库状态弹窗 star
//$('.addAccount').on('click', function(){
//    $.colorbox({
//        inline: true,
//        href: $("#popup1"),
//        overlayClose: false,
//        title: "到访客户接待提醒"
//    });
//});
/*$('.viConfirm').on('click',function(){
	$('#popup1').colorbox.close();
});
//  客户是回库状态弹窗 end

//  到访客户接待提醒 star
$('.addAccount1').on('click', function(){
    $.colorbox({
        inline: true,
        href: $("#popup2"),
        overlayClose: false,
        title: "到访客户接待提醒"
    });
});
$('.viCancel').on('click',function(){
	$('#popup2').colorbox.close();
});
//  到访客户接待提醒 end

//  操作者是本中心 star
$('.addAccount2').on('click', function(){
    $.colorbox({
        inline: true,
        href: $("#popup3"),
        overlayClose: false,
        title: "到访客户接待提醒"
    });
});
$('.centerBtn').on('click',function(){
	$('#popup3').colorbox.close();
});
//  操作者是本中心 end

//  操作者是非本中心 star
$('.addAccount3').on('click', function(){
    $.colorbox({
        inline: true,
        href: $("#popup4"),
        overlayClose: false,
        title: "到访客户接待提醒"
    });
});
$('.nonCenterBtn').on('click',function(){
	$('#popup4').colorbox.close();
});
//  操作者是非本中心 end

//  重新分配 star
$('.viReassign, .viReassign2').on('click', function(){
    $.colorbox({
        inline: true,
        href: $("#viReassign"),
        overlayClose: false,
        title: "到访客户接待提醒"
    });
});
$('.rnConfirm').on('click',function(){
	$('#viReassign').colorbox.close();
});*/
$('#rnTable tr:last-child').find('td').css('borderBottom','none');
//  重新分配 end

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

//===================================================================
//  实际到访时间
$('#afTimeStar, #afTimeEnd').glDatePicker({
    onClick:function(el, cell, date, data) {
        el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
        if(el.hasClass("start")){
            var start_time=el.val();
            var end_time=el.siblings(".end").val();
            var url = el.siblings(".end").attr('data-url');
            if(end_time.length>0){
                location.href=url+start_time+'@'+end_time;
            }
        }else if(el.hasClass("end")){
            var start_time=el.siblings(".start").val();
            var end_time=el.val();
            if(!start_time){
                var myDate = new Date();
                start_time = myDate.getFullYear()+'/'+myDate.getMonth()+'/'+myDate.getDate();
            }
            location.href=el.attr('data-url')+start_time+'@'+end_time;
        }
    },
});

//=======================================================
$('#viTable tbody  tr').each(function(index, e) {
    $(e).hover(function(e){
        var e = e || window.event;
        var table_w=$('#table2_div').width();
        var _x=(e.pageX?e.pageX:e.clientX)+80;
        var _y=parseInt($(this).offset().top)+9;
        if((_x-table_w)>-50)
		{
			_x=_x-200;
		}

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
