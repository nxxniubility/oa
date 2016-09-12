var curIndex=0;
var indexOpen = '';
$(function(){
	$(".tab div").click(function(){
		var index=$(this).index();
		if(index!=curIndex){
			$(".tab div").siblings().removeClass("cur").eq(index).addClass("cur");
			$(".content").removeClass("active").eq(index).addClass("active");
			if(index==2){
				//日期
				var myDate = new Date();
                $(".tabTime .afTime").glDatePicker({
                      selectableDateRange: [
                          {
						   from: new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate()),
						   to: new Date(2036, 0, 1)
                          }
					  ]
                    }
				);
			}
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

/*创建订单*/
$('.btn_reserve').click(function(e){
    e.stopPropagation();
    var data = {
        type : 'ishint',
        user_id:$(':input[name="temp_user_id"]').val()
    };
    //获取提示
    common_ajax2(data, createOrder_href, 'no', getHint);
    function getHint(reflag){
        $('#reserve_hint').empty();
        if(reflag.code!=0){
            $('#reserve_hint').html('<span style="color: red; margin-left: 118px;">'+reflag.msg+'</span>');
        }else{
            $('#reserve_hint').html('<span style="color:green; margin-left: 118px;">'+reflag.msg+'</span>');
        };
    };
    layer.open({
        type: 1, 					//  页面层
        title: '创建订单', 				//	不显示标题栏
        area: ['490px', '340px'],
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
$('.btn_allocation').click(function(e){
    e.stopPropagation();
    if($(this).hasClass('apply')){
        $(':input[name="allocation_flag"]').val(2);
    }else{
        $(':input[name="allocation_flag"]').val(1);
    }
    indexOpen = layer.open({
        type: 1, 					//  页面层
        title: '选择操作者', 				//	不显示标题栏
        area: ['1000px', '490px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $("#panel3")	//  加载主体内容
    });
    $('.Capacity').attr('data-type','allocation');
    getSystemUser(1,'allocation');
});
/*放弃*/
$('.btn_abandon').click(function(e){
    e.stopPropagation();
    $('#panel4').find('.realname').text($(':input[name="dn_realname"]').val());
    $('#panel4').find('.mobile').text($(':input[name="dn_username"]').val());
    layer.open({
        type: 1, 					//  页面层
        title: '放弃客户', 				//	不显示标题栏
        area: ['600px', '400px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $("#panel4")	//  加载主体内容
    });
});
/*申请转入*/
$('.btn_apply').click(function(e){
    e.stopPropagation();
    if($('#apply_remak').length>0){
        // 实例化编辑器
        apply_remak = UE.getEditor('apply_remak',{
            toolbars: [
                ['fullscreen', 'source','fontsize','fontfamily', 'undo', 'redo','underline', 'bold','simpleupload']
            ],
            initialFrameWidth:358,
            initialFrameHeight:116,
            pasteplain:true,
            autoHeightEnabled:false,
            enableAutoSave:false,
            elementPathEnabled:false,
            autoFloatEnabled:false
        });
    }
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

/*赎回客户*/
$('.btn_recover').click(function(e){
    e.stopPropagation();
    layer.open({
        type: 1, 					//  页面层
        title: '赎回客户', 				//	不显示标题栏
        area: ['600px', '580px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        zIndex: 99,
        content: $("#popup2")	//  加载主体内容
    });
    setTimeout(function(){
		var myDate = new Date();               
		$('.reApplyBox .afTime').glDatePicker({ selectableDateRange: [
			  { 
				   from: new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate()),
				   to: new Date(2036, 0, 1) }
			  ]}
		);		
	},1000);
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

//回访方式 跟进结果
$('#waytype li,#attitude_id li').click(function(){
    //var txt = ue2.getContentTxt();
    //if($(this).parent().attr('id')=='attitude_id'){
    //    if(txt.length==0){
    //        ue2.setContent($(this).text());
    //    }
    //}
    $(this).addClass('curr').siblings('li').removeClass('curr');
});