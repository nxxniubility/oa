$(function(){
    selectbox();
    //选择模板
    $(".toggleBtn").click(function(){
    	$(".snCont").hide();
    	$(".snMain").show();
    });
    //放弃选择
    $(".snOptout").click(function(){
    	$(".snCont").show();
    	$(".snMain").hide();
    	$(".smallListBoxCon").html('<span class="snNothing">请选择导航</span>');
    });
    //点击选择复印
    $(document).on('click',".templateUl li",function(){
    	var src=$.trim($(this).find("img").attr("src"));
    	var alt=$.trim($(this).find("img").attr("alt"));
    	var snId=$.trim($(this).find(".snId").text());
    	var str='<li class="smallListBoxLi'+snId+'"><a href="javascript:;"><div class="boxClose" onclick="removeMy(this)"></div>'+
		'<div class="boxId">'+snId+'</div><div class="picBox"><img src="'+src+'" alt="'+alt+'"></div>'+
		'<div class="btomBox"><input type="text" class="btomInp" placeholder="请填写导航名称"></div></a></li>';
		if($(".smallListBoxCon").children(".snNothing").length){
			if(!$(".smallListBoxLi"+snId).length){
				 $(".smallListBoxCon").html(str);
				 $(".smallListBoxCon li input").focus(function(){
					 if(!$(".smallListBoxLi"+snId).find(".btomBox").hasClass("btomBoxBg")){
						 $(".smallListBoxLi"+snId).find(".btomBox").addClass("btomBoxBg")
					 }
				 }).blur(function(){
					 if($(".smallListBoxLi"+snId).find(".btomBox").hasClass("btomBoxBg")){
						 $(".smallListBoxLi"+snId).find(".btomBox").removeClass("btomBoxBg")
					 }
				 });
				 $(window).scrollTop($(".smallListBoxLi"+snId).position().top);
			}
		}else{
			if(!$(".smallListBoxLi"+snId).length){
				 $(".smallListBoxCon").append(str);
				 $(".smallListBoxCon li input").focus(function(){
					 if(!$(this).parents("li").find(".btomBox").hasClass("btomBoxBg")){
						 $(this).parents("li").find(".btomBox").addClass("btomBoxBg")
					 }
				 }).blur(function(){
					 if($(this).parents("li").find(".btomBox").hasClass("btomBoxBg")){
						 $(this).parents("li").find(".btomBox").removeClass("btomBoxBg")
					 }
				 });
				 $(window).scrollTop($(".smallListBoxLi"+snId).position().top);
			}
			
		}
    });
    
});

//移除自己
function removeMy(obj){
	var smallObj=$(obj).parents(".smallListBoxCon");
	$(obj).parents("li").remove();
	if(smallObj.children("li").length<1){
		smallObj.html('<span class="snNothing">请选择导航</span>');
	}
}

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
            $(this).parents("dl").next().val(data_value);
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
$('#serviceCategory').on('click', function() {
    $.colorbox({
        inline: true,
        href: $("#popup2"),
        title: "管理服务分类"
    });
    if($(':input[name="terminal_id"]').val().length!=0){
        $('#pagesType .old_body').hide();
        $('#pagesType').find('.terminal_'+$(':input[name="terminal_id"]').val()).show();
    }else{
        layer.msg('请先选择终端分类', {icon:2});
        $('#cboxClose').trigger('click');
    }
});
$('#terminalCategory').on('click', function() {
    $.colorbox({
        inline: true,
        href: $("#popup1"),
        title: "管理终端分类"
    });
});

//删除终端分类名称
//$(".newTemplate").find(".delCategory").live("click", function() {
//    $(this).closest(".newRow").remove();
//    orderCategoryNo($(this));
//});
//新增终端分类名称
$(".newTemplate").find(".newBtn").on("click", function() {
    var newRow = $(".newRowModel").clone();
    $(newRow).removeClass("newRowModel");
    $(this).closest(".newRow").before(newRow);
    orderCategoryNo($(this));
});

function orderCategoryNo(obj) {
    var spans = $(obj).closest(".newTemplate").find(".newCategory").find(".newLeft").find(".lbl");
    var rCnt = spans.length;
    for (var i = 0; i < rCnt; i++) {
        var num = i + 1;
        $(spans[i]).text(($(obj).closest("form").attr("id") == "form2" ? "服务分类名称" : "终端分类名称") + num + "：");
    }
}


//列表编辑备注
$(".li-box").find(".mEdit").on("click", function() {
    $(this).parent().find(".edit1").addClass("dn");
    $(this).parent().find(".editBox").removeClass("dn");
    $(this).parent().find(".editBox").find("textarea").focus();
});
//列表编辑取消备注
$(".li-box").find(".cancel").on("click", function() {
    $(this).closest(".li-box").find(".edit1").removeClass("dn");
    $(this).closest(".editBox").addClass("dn");
});
