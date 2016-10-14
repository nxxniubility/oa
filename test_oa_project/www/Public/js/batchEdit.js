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
		content: $("#pcChooseTemplate") 	//  加载主体内容
	});
	$('.imClose').on('click',function(){
		layer.closeAll(); 		// 关闭
	});

	$('#pcSure').on('click',function(){
		var pc_pages_id = $(':input[name="selectCategory1"]:checked').val();
		var subject=$(':input[name="selectCategory1"]:checked').attr('data-subject');
		if ( $(':input[name="selectCategory1"]:checked').length>0) {
			$(':input[name="pc_pages_id"]').val(pc_pages_id);
			$('#pcUploadBtn').next('span').html(subject);
			layer.closeAll(); 		// 关闭
		}else{
			layer.msg('未选择模板',{icon:2});
		}
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
		content: $("#mChooseTemplate") 	//  加载主体内容
	});
	$('.imClose').on('click',function(){
		layer.closeAll(); 		// 关闭
	});
	$('#mSure').on('click',function(){
		var m_pages_id = $(':input[name="selectCategory2"]:checked').val();
		var subject=$(':input[name="selectCategory2"]:checked').attr('data-subject');
		if ( $(':input[name="selectCategory2"]:checked').length>0) {
			$(':input[name="m_pages_id"]').val(m_pages_id);
			$('#mUploadBtn').next('span').html(subject);
			layer.closeAll(); 		// 关闭
		}else{
			layer.msg('未选择模板',{icon:2});
		}
	});
});

$('.contBox').bind('mousewheel', function(event) {
      event.preventDefault();
      var scrollTop = this.scrollTop;
      this.scrollTop = (scrollTop + ((event.deltaY * event.deltaFactor) * -1));
});
