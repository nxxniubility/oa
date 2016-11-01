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
                ['fullscreen', 'source','fontsize','fontfamily', 'undo', 'redo','underline', 'bold','insertimage']
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

    $("#channelchoose").change(function()
    {
        var data_value = $(this).val();
        if (data_value == 1000016) {
            $(".nssNone").show();
        }else{
            $(".nssNone").hide();
        }
    })
    $(".nssNone").hide();
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

//  最后一个tab则加上boreder-right
$(function(){
	$('.wrapBox#user_detail .tab div:last-child').css('borderRight','1px solid #dedede');
});

//  网络电话，正在拨号
$(document).on('click', '.btn_phone', function(){
	
	//  通话结束
	//call_end();
	
	//  拨不通取消或者转拨固话
	//dialing_error();
});


//  通话结束
function call_end(){
	layer.msg('网络电话通话完成，请在“通话记录”内查阅录音记录.', {icon:1,time:3000});
}

//  拨不通取消或者转拨固话
function dialing_error(){
	layer.confirm('客户手机拨打失败，请确认是否继续拨打客户固定电话.', {icon:7,title:'警告'},function(index){
		//  确定继续拨打固化
		layer.msg('正在拨打客户固定电话中...', {icon:6,time:3000});
		
		layer.close(index);
	});
}
