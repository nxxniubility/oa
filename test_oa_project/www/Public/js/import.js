/*选择PC/moblie模版*/
$('#pcUploadBtn').on('click',function(){
	$('#pcTemSearchBtn').trigger('click');
	layer.open({
		type: 1,					//  页面层
		title: false,				//	不显示标题栏
		area: ['800px','569px'],	
		shade: .6,					//	遮罩
		time: 0,					//  关闭自动关闭
		shadeClose: true,			//	遮罩控制关闭层
		closeBtn: false,			//	不显示关闭按钮
		shift: 1,					//	出现动画
		content: $("#pcChooseTemplate") 	//  加载主体内容,
		
	});
	$('.imChooseBtn').on('click',function(){
		$(':input[name="pcPagesType_id"]').val($(':input[name="pcPageid"]:checked').val());
	});
	$('.imClose, .imChooseBtn').on('click',function(){
		layer.closeAll(); 		// 关闭
	});
	
});
/*选择PC/moblie模版*/
$('#mUploadBtn').on('click',function(){
	$('#mTemSearchBtn').trigger('click');
	layer.open({
		type: 1,					//  页面层
		title: false,				//	不显示标题栏
		area: ['800px','569px'],	
		shade: .6,					//	遮罩
		time: 0,					//  关闭自动关闭
		shadeClose: true,			//	遮罩控制关闭层
		closeBtn: false,			//	不显示关闭按钮
		shift: 1,					//	出现动画
		content: $("#mChooseTemplate") 	//  加载主体内容,
	});
	$('.imChooseBtn').on('click',function(){
		$(':input[name="mPagesType_id"]').val($(':input[name="mPageid"]:checked').val());
	});
	$('.imClose, .imChooseBtn').on('click',function(){
		layer.closeAll(); 		// 关闭
	});
});
$('.imChooseBtn').on('click',function(){
	layer.closeAll(); 		// 关闭
	var str = '';
	if($(this).hasClass('PCpage')){
		str = $(':input[name="pcPageid"]:checked').parents('.clearfix').find('.theme').text();
		$('#pcUploadBtn').siblings('span').html(str);
	}else{
		str = $(':input[name="mPageid"]:checked').parents('.clearfix').find('.theme').text();
		$('#mUploadBtn').siblings('span').html(str);
	}
});

$('.contBox').bind('mousewheel', function(event) {
      event.preventDefault();
      var scrollTop = this.scrollTop;
      this.scrollTop = (scrollTop + ((event.deltaY * event.deltaFactor) * -1));
});

