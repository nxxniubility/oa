/**********快速筛选**********/
var choice=function(e,t){
    t===undefined&&(t=900);
    $(e).click(function(){
        $(".arrowFather,.arrowFather1").toggle();
        $(".frame,.frame1").slideToggle(t);
        
        //  实际到访时间
	    setTimeout(function(){
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
		},500);
        
    });
};
choice(".arrowFather1,.arrowFather");

//  重新分配 star
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

//  双击打开详情
$(document).on('dblclick',".content_li",function(){
    var el = $(this).find('.hrefDetail');
    $('#hrefForm').attr('action', el.attr('href')).submit();
});
