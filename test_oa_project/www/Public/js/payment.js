//  列表
$('#forTable tr:last-child').find('td').css('borderBottom','none');
$('.otherIcon li:last-child').css('borderBottom','none');

//  操作
$(document).on('click', '.forSelect',function(event){
    $(this).next().toggle().closest('tr').siblings().find('.otherOperation').hide();
    //阻止点击document对当前事件的影响
    event.stopPropagation();
});

 $(document).click(function() {
	$('.otherOperation').hide();
});

var feeId;
//  收款
$('.payReceivablesBtn').on('click', function(){
    var _data = $(this).attr('data-value');
    _data = _data.split('::');
    feeId = _data[0];
    $("#receivables .reRow").eq(0).children('em').text(_data[1]);

    $.colorbox({
        inline: true,
        href: $("#receivables"),
        overlayClose: false,
        title: "确认缴费收款"
    });
});
//  收款

//  退款
$('.payRefundBtn').on('click', function(){
    var _data = $(this).attr('data-value');
    _data = _data.split('::');
    feeId = _data[0];
    for(i=0;i<2;i++){
        $("#returnApplication .raRow").eq(i).children('em').text(_data[i+1]);
    }
    $.colorbox({
        inline: true,
        href: $("#returnApplication"),
        overlayClose: false,
        title: "退回缴费款项"
    });
});
//  退款

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

//=======================================================
$('#forTable tbody  tr').each(function(index, e) {
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