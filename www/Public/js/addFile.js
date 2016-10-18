
$(function(){
	//  模拟select
	selectbox();
	selectbox2();
});

//下拉框
function selectbox() {
    $(document).bind({
        click: function() {
            $(".selectbox dt").parent().find("ul").removeClass("s");
        }
    });
    $(".select").delegate("dt", "click",
        function() {
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

    $(".select").delegate("dd", "click",
        function() {
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

    $(".dt_btn").on( "click",
        function() {
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
    $(document).on('click', '.select2 dt',
        function(){
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
        obj.find(".arrowSelect").removeClass("arrowSelect_on");
    } else {
        otherSelectStatus(); //
        obj.parent().find("dd,.select_title2").show();
        obj.parent().find("dd,.select_title3").show();
        obj.addClass("on");
        obj.parent().addClass("on zindex4");
        obj.find(".arrowSelect").addClass("arrowSelect_on");

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

// 限制字符长度
function chkLength(el,size){
	if(el.value.length > size){
		layer.msg('已超出字数规定上限.',{icon:2});
	}
	el.value = el.value.substring(0,size);
}

//  验证电子邮箱：格式
$(document).ready(function(){
	$('#edit-email').blur(function(){
		chkEmail();
	});
});
function chkEmail(){
	var	reg = (/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/),
		_email = $('#edit-email'),
		_tip = $('.email-tips');
　　if (!_email.val().match(reg)){
		_tip.show()
			.html('请输入正确的邮箱格式');
		_email.focus();
　　}else{
		_tip.hide()
			.html('');
	}
}
