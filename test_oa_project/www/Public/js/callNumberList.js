//  添加号码
$('.add_number').on('click', function(){
    layer.open({
        type: 1, 					//  页面层
        title: '添加号码', 		    //	不显示标题栏
        area: ['450px', 'auto'],
        closeBtn: 1,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: false, 			//	遮罩控制关闭层
        shift: 5, 					//	出现动画-5 闪现
        content: $(".call_addbox"),	    //  加载主体内容
        scrollbar: false
    });
	$('.btn_cancel').on('click',function(){
		layer.closeAll();
	});
});

