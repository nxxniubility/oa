var curIndex=0;
$(function(){
	$(".tab div").click(function(){
		var index=$(this).index();
		if(index!=curIndex){
			$(".tab div").siblings().removeClass("cur").eq(index).addClass("cur");
			$(".content").removeClass("active").eq(index).addClass("active");
			/*if(index==2){
				//日期
                $(".afTime").glDatePicker();
			}*/
		    curIndex=index;
		}
	});
	
	//行内单选
    $(".l-row").each(function() {
        var wrapper = $(this);
        wrapper.find(".b-radio-wrapper").click("on", function() {
            $(this).find('a').addClass('b-radio-checked').siblings("input").prop("checked", true);
            $(this).siblings().find("a").removeClass("b-radio-checked").siblings("input").prop("checked", false);
        });
    });
	
   
});

/*申请预报*/
$('.btn_reserve').click(function(){
    layer.open({
        type: 1, 					//  页面层
        title: '申请预报', 				//	不显示标题栏
        area: ['600px', '330px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $("#panel2"),	//  加载主体内容
        scrollbar: false
    });
});
/*转出*/
$('.btn_allocation').click(function(){
    layer.open({
        type: 1, 					//  页面层
        title: '选择操作者', 				//	不显示标题栏
        area: ['750px', '580px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $("#panel3")	//  加载主体内容
    });
    getSystemUser();
});
/*放弃*/
$('.btn_abandon').click(function(){
    layer.open({
        type: 1, 					//  页面层
        title: '放弃客户', 				//	不显示标题栏
        area: ['600px', '330px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $("#panel4")	//  加载主体内容
    });
});
/*申请转入*/
$('.btn_apply').click(function(){
    layer.open({
        type: 1, 					//  页面层
        title: '申请转入客户', 				//	不显示标题栏
        area: ['600px', '580px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $("#popup1")	//  加载主体内容
    });
});
//$('#fcTransferredTo').on('click', function() {
//    $.colorbox({
//        inline: true,
//        href: $("#popup1"),
//        overlayClose: false,
//        title: "申请转入客户"
//    });
//});
/*赎回申请*/
$('#rtBtn').on('click', function() {
    $.colorbox({
        inline: true,
        href: $("#popup2"),
        overlayClose: false,
        title: "赎回客户"
    });
});

$(':input[name="attitude_id"]').change(function(){
    if($(this).val()==2){
        $('#uptime').html('<i>*</i>承诺回访&nbsp;:&nbsp;');
    }else{
        $('#uptime').html('<i>*</i>下次回访&nbsp;:&nbsp;');
    }
});
/*转介绍显示*/
$('.singleBox').each(function(){
	var $self = $(this);
	$self.find('label').on('click',function(){
		var status=parseInt($(this).find("input").val());
        	$(this).find("input").prop("checked", true);
            $(this).siblings("input").prop("checked", false);
            if(status){
            	$(this).closest('.alRow').next().fadeIn();
            }else{
            	$(this).closest('.alRow').next().fadeOut();
            }
	})
});

//关闭colorbox弹窗
$('.rtSubmit').on('click',function(){
	$('.redemption').colorbox.close();
});