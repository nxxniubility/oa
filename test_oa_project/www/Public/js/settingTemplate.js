$(function(){
	headForm();
});

//  操作
$(document).on('click', '.proSelect',
    function(event){
    $(this).next().toggle().closest('dl').siblings().find('.otherOperation').hide();
    //阻止点击document对当前事件的影响
    event.stopPropagation();
});

 $(document).click(function() {
	$('.otherOperation').hide();
});

/*添加新/编辑模版*/
/*$('.newPlan').on('click',function(){
	layer.open({
		type: 1,					//  页面层
		title: false,				//	不显示标题栏
		area: ['584px','513px'],	
		shade: .6,					//	遮罩
		time: 0,					//  关闭自动关闭
		shadeClose: true,			//	遮罩控制关闭层
		closeBtn: false,			//	不显示关闭按钮
		shift: 1,					//	出现动画
		content: $(".newTemplate") 	//  加载主体内容
	});
	$('.setClose, .setSubmit, .setCancel').on('click',function(){
		layer.closeAll(); 		// 关闭
	});
});*/


//  添加模版的表头部分
function headForm(){
	$('.newRight .headForm li:odd').css('margin-right','0');
}


