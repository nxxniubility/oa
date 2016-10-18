//关闭colorbox弹窗
$('.naEtermine').on('click', function() {
    layer.closeAll(); 			// 关闭
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

//添加预转出人
$(document).on('click', '.btn_apply_tosystem', function() {
    var index = layer.open({
        type: 1, 					//  页面层
        title: '选择操作者', 			//	不显示标题栏
        area: ['1000px', '490px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $("#panel3")	//  加载主体内容
    });
    getSystemUser(1, 'apply');
    //添加预转出人
    $(document).on('click', '.apply_tosystemuser_submit', function() {
        $(':input[name="apply_to_system_user_id"]').val($(this).attr('data-value'));
        $(':input[name="apply_to_system_user_name"]').val($(this).siblings('.wOne').text());
        layer.close(index);
    });
});