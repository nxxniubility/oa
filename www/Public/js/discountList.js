/**
 * 添加优惠
 * */
$('.addDiscount').on('click', function(){
    layer.open({
        type: 1, 					//  页面层
        title: '添加优惠', 		    //	不显示标题栏
        area: ['540px', 'auto'],
        closeBtn: 2,
        zIndex: 5000,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: false, 			//	遮罩控制关闭层
        shift: 5, 					//	出现动画-5 闪现
        content: $(".preBox"),	    //  加载主体内容
        scrollbar: false
    });
    
    //  添加日期
    setTimeout(function(){
        var myDate = new Date();
        //ymd = myDate.getFullYear()+'/'+ myDate.getMonth()+'/'+myDate.getDate();
        $(".discount-atime").glDatePicker({
            selectableDateRange: [
                {
                    //from: new Date(myDate.getFullYear(), (myDate.getMonth()-1), (myDate.getDate()-7)),
                    //to: new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate())
                    from: new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate()),
					to: new Date(2036, 0, 1)
                }
            ]
        });
    },1000)
    
    
	$('.preCancel').on('click',function(){
        $(':input[name="fbChk"]').prop('checked',false);
		layer.closeAll();
	});
});

/**
 * 修改优惠
 * */
$('.editPreferential').on('click', function(){
    layer.open({
        type: 1,                    //  页面层
        title: '修改优惠',          //  不显示标题栏
        area: ['540px', 'auto'],
        closeBtn: 2,
        zIndex: 5000,
        shade: .6,                  //  遮罩
        time: 0,                    //  关闭自动关闭
        shadeClose: false,          //  遮罩控制关闭层
        shift: 5,                   //  出现动画-5 闪现
        content: $(".preBox2"),      //  加载主体内容
        scrollbar: false
    });
    
    //  修改日期
    setTimeout(function(){
        var myDate = new Date();
        //ymd = myDate.getFullYear()+'/'+ myDate.getMonth()+'/'+myDate.getDate();
        $(".discount-etime").glDatePicker({
            selectableDateRange: [
                {
                    //from: new Date(myDate.getFullYear(), (myDate.getMonth()-1), (myDate.getDate()-7)),
                    //to: new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate())
                    from: new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate()),
					to: new Date(2036, 0, 1)
                }
            ]
        });
    },1000)
    
    $('.preCancel').on('click',function(){
        layer.closeAll();
    });
});
var banBox = '';
/*选择*/
$('.banDiscountList').on('click',function(){

    banBox = layer.open({
        type: 1,                    //  页面层
        title: '禁止选择以下优惠选项',               //  显示标题栏
        area: ['800px','auto'],    
        shade: .6,                  //  遮罩
        time: 0,                    //  关闭自动关闭
        shadeClose: true,           //  遮罩控制关闭层
        closeBtn: 1,            	//  关闭按钮 风格1
        shift: 1,                   //  出现动画
        content: $("#ban")          //  加载主体内容
    });
    if($(this).hasClass('edit')){
        $('#ban .fbCheck').prop('checked',false);
        var editRepeat = $(':input[name="edit_repeat"]').val();
        editRepeat = editRepeat.split(',');
        $.each(editRepeat,function(k,v){
            $('#ban .fbCheck[value="'+v+'"]').prop('checked',true);
        });
        $('.fbBtn').addClass('edit');
    }else{
        $('.fbBtn').removeClass('edit');
    }
        
});



$('.fbBtn').on('click',function(){
    var ids = '';
    $(':input[name="fbChk"]:checked').each(function(){
        if(ids==''){
            ids= $(this).val();
        }else{
            ids += ','+$(this).val();
        };
    });
    layer.close(banBox);       // 关闭
    if($(this).hasClass('edit')){
        $(':input[name="edit_repeat"]').val(ids);
    }else{
        $(':input[name="repeat"]').val(ids);
    }
});

// 限制字符长度
function chkLength(el,size){
	if(el.value.length > size){
		layer.msg('不能超过'+ size +'字数限制.',{icon:2});
	}
	el.value = el.value.substring(0,size);
}