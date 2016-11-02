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
$('.nsSelectPost').on('click', function() {
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
    $('.nsClose, .nsDetermine').on('click', function() {
        layer.closeAll(); 			// 关闭
    });
});
$('.nsSelectPostMan').on('click', function() {
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

$(".nsRight label").click(function(){
    if($(this).find(".man:checked").val()==undefined) {
        $(".nsNone").hide();
        $(".nssNone").show();
    }else{
        $(".nssNone").hide();
        $(".nsNone").show();
    }
})
$(".nssNone").hide();

$(".nsRight label").click(function(){
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
