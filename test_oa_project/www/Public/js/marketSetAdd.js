//  员工定位
function employeeType(){
	$(document).on('click','.employee-type span',function(){
		$(this).addClass('selected').siblings().removeClass('selected');
	});
}
employeeType();

/*$(document).on('click', '.general-term', function(){
	layer.open({
		type: 1, 				//  页面层
		title: false, 			//	不显示标题栏
		area: ['585px','auto'],
		shade: .6, 				//	遮罩
		time: 0, 			//  关闭自动关闭
		shadeClose: true, 		//	遮罩控制关闭层
		closeBtn: false, 		//	不显示关闭按钮
		shift: 5, 				//	出现动画
		content: $('.formula-layer') 		//  加载主体内容
	});
	
	$('.close-icon').on('click', function() {
		layer.closeAll();
	})
});*/
