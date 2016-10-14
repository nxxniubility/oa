$(function(){
    selectbox();
});
$('#atStar, #atEnd, #afBirthday, #afBirthday2').glDatePicker();

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
            var data_id = $(this).attr('data_id');
            $(this).parent('.ddoption').toggle();
            $(this).parent("dl").find(".select_title").text(data_name).attr('data_id',data_id);
            $(this).parent().parent().find(".select_title").text(data_name);
            //$(this).parents("dl").next().val(data_value);
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select dd").parent("dl").find("dt"));
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
        });
    $(".typeselect").delegate("dd", "click",
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
    $(".region").delegate("dd", "click",
        function() {
            $('#zonename_title').hide();
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_level = $(this).attr('data-level');
            var data_name = $(this).text();
            $(this).parent('.ddoption').toggle();
            $(this).parent("dl").find(".select_title").text(data_name);
            $(this).parent().parent().find(".select_title").text(data_name);
            $(':input[name="zone_id"]').val(data_value);
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select dd").parent("dl").find("dt"));
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
            if(data_level!=1){
                $.ajax({
                    url:url_ZoneSelect,
                    dataType:'json',
                    type:'post',
                    data:{zone_id:data_value},
                    success:function(reflag){
                        $('.city').show();
                        if(reflag.code==0){
                            $('.city').html(reflag.data);
                        }
                    }
                })
            }else{
                $('.city').hide();
                $('.area').hide();
            }
        });
    $(".city").delegate("dd", "click",
        function() {
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_level = $(this).attr('data-level');
            var data_name = $(this).text();
            $(this).parent('.ddoption').toggle();
            $(this).parent("dl").find(".select_title").text(data_name);
            $(this).parent().parent().find(".select_title").text(data_name);
            $(':input[name="zone_id"]').val(data_value);
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select dd").parent("dl").find("dt"));
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
            if(data_level!=2){
                $.ajax({
                    url:url_ZoneSelect,
                    dataType:'json',
                    type:'post',
                    data:{zone_id:data_value},
                    success:function(reflag){
                        $('.area').show();
                        if(reflag.code==0){
                            $('.area').html(reflag.data);
                        }
                    }
                })
            }else{
                $('.area').hide();
            }
        });
    $(".area").delegate("dd", "click",
        function() {
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_level = $(this).attr('data-level');
            var data_name = $(this).text();
            $(this).parent('.ddoption').toggle();
            $(this).parent("dl").find(".select_title").text(data_name);
            $(this).parent().parent().find(".select_title").text(data_name);
            $(':input[name="zone_id"]').val(data_value);
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
    $('.nsClose').on('click', function() {
        layer.closeAll(); 			// 关闭
    });
});

//$('.nsDeparMiddle dd.wNsOne').find('input[type="checkbox"]').click(function(){
//	$(this).attr('checked','checked').closest('dl').siblings().find('dd.wNsOne input[type="checkbox"]').removeAttr('checked');
//});

