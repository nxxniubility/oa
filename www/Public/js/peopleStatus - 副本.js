//  闲
/*$('.xian').on('click',function(){
	 $.colorbox({
        inline: true,
        href: $("#xian"),
        overlayClose: false,
        title: "标记忙状态"
    });
});
$('.xCancel').on('click',function(){
	$('#xian').colorbox.close();
});*/

$('.xian').on('click',function(){
	layer.open({
        type: 1, 					//  页面层
        title: '标记忙状态', 				//	不显示标题栏
        area: ['406px', 'auto'],
        closeBtn: 1,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: false, 			//	遮罩控制关闭层
        shift: 5, 					//	出现动画-5 闪现
        content: $("#xian"),	//  加载主体内容
        scrollbar: false
    });
})
$('.xCancel').on('click',function(){
    layer.closeAll();
});
//  闲 end

//  忙状态超过30min
/*$('.chao').on('click',function(){
	 $.colorbox({
        inline: true,
        href: $("#chao"),
        overlayClose: false,
        title: "标记忙状态"
    });
});
$('.chCancel').on('click',function(){
	$('#chao').colorbox.close();
});*/
$('.chao').on('click',function(){
	layer.open({
        type: 1, 					//  页面层
        title: '标记忙状态', 				//	不显示标题栏
        area: ['404px', 'auto'],
        closeBtn: 1,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: false, 			//	遮罩控制关闭层
        shift: 5, 					//	出现动画-5 闪现
        content: $("#chao"),	//  加载主体内容
        scrollbar: false
    });
})
$('.chCancel').on('click',function(){
    layer.closeAll();
});
//  忙状态超过30min end

//  忙
/*$('.mang').on('click',function(){
	 $.colorbox({
        inline: true,
        href: $("#mang"),
        overlayClose: false,
        title: "标记忙状态"
    });
});
$('.mCancel').on('click',function(){
	$('#mang').colorbox.close();
});*/
$('.mang').on('click',function(){
	layer.open({
        type: 1, 					//  页面层
        title: '标记闲状态', 				//	不显示标题栏
        area: ['406px', 'auto'],
        closeBtn: 1,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: false, 			//	遮罩控制关闭层
        shift: 5, 					//	出现动画-5 闪现
        content: $("#mang"),	//  加载主体内容
        scrollbar: false
    });
})
$('.mCancel').on('click',function(){
    layer.closeAll();
});
//  忙 end


function getEngagedStatus_ajax(){
    $.ajax({
        url:ajax_url,
        type: 'post',
        dataType: 'json',
        data: '',
        success: function (reflag) {
            if(reflag.code==10){
                $('.peopleStatus .mang').show().siblings('span').hide();
                if(!$('.header').hasClass('redBg')){
                    $('.header').addClass('redBg');
                }
                /*$.colorbox({
                    inline: true,
                    href: $("#automatic"),
                    overlayClose: false,
                    title: "客户到访通知"
                });*/
                layer.open({
			        type: 1, 					//  页面层
			        title: '客户到访通知', 				//	不显示标题栏
			        area: ['404px', 'auto'],
			        closeBtn: 1,
			        shade: .6, 					//	遮罩
			        time: 0, 					//  关闭自动关闭
			        shadeClose: false, 			//	遮罩控制关闭层
			        shift: 5, 					//	出现动画-5 闪现
			        content: $("#automatic"),	//  加载主体内容
			        scrollbar: false
			    });
                //alert('到访提示'+ '、、、、、、、');
                $('#automatic .vsRow').eq(0).children('em').html(reflag.data.realname);
                if(reflag.data.username==0){
                    reflag.data.username=' ';
                }
                $('#automatic .vsRow').eq(1).children('em').html(reflag.data.username);
                if(reflag.data.tel==0){
                    reflag.data.tel=' ';
                }
                $('#automatic .vsRow').eq(2).children('em').html(reflag.data.tel);
                if(reflag.data.qq==0){
                    reflag.data.qq=' ';
                }
                $('#automatic .vsRow').eq(3).children('em').html(reflag.data.qq);
                $('#automatic a').attr('onclick',"editUrl_iframe("+reflag.data.user_id+")");
            }else if(reflag.code==2){
                $('.peopleStatus .mang').show().siblings('span').hide();
                if(!$('.header').hasClass('redBg')){
                    $('.header').addClass('redBg');
                }
                $('.code2_msg').html(reflag.msg);
                /*$.colorbox({
                    inline: true,
                    href: $("#chao"),
                    overlayClose: false,
                    title: "忙线状态提示"
                });*/
				layer.open({
			        type: 1, 					//  页面层
			        title: '忙线状态提示', 				//	不显示标题栏
			        area: ['404px', 'auto'],
			        closeBtn: 1,
			        shade: .6, 					//	遮罩
			        time: 0, 					//  关闭自动关闭
			        shadeClose: false, 			//	遮罩控制关闭层
			        shift: 5, 					//	出现动画-5 闪现
			        content: $("#chao"),	//  加载主体内容
			        scrollbar: false
			    });
                //alert('忙提示'+ '、、、、、、、');
            }else if(reflag.code==3){
                $('.peopleStatus .mang').show().siblings('span').hide();
                if(!$('.header').hasClass('redBg')){
                    $('.header').addClass('redBg');
                }
            }else if(reflag.code==100){
                window.location.href=reflag.data;
            }else{
                $('.peopleStatus .xian').show().siblings('span').hide();
                if($('.header').hasClass('redBg')){
                    $('.header').removeClass('redBg');
                }
            }
            return false;
        },
        error:function(){
            $('.peopleStatus .xian').show().siblings('span').hide();
            if($('.header').hasClass('redBg')){
                $('.header').removeClass('redBg');
            }
            return false;
        }
    });
    return false;
}

//获取消息列表
function getMsgList_ajax(){
    var data = {
        type:'getMsgList'
    };
    common_ajax2(data,ajax_url,'no',reMsgList,'no');
    function reMsgList(reflag){
        var old_count = $('#poll_total_msg').text();
        if(reflag.code==0){
            $('#poll_total_msg').text(reflag.data.count);
            if(reflag.data.count==0){
                $('#poll_msg_bady').empty().siblings('.MsgNull').show();
            }else if(old_count<reflag.data.count){
                var html = '';
                $.each(reflag.data.data,function(k,v){
                    if(k==0 && v.msgtype==11 && v.read==1){

                    };
                    if(k<3){
                        html += '<p> <a href="'+v.system_user_msg_id+'" style="margin-top: 0px; width: 263px;">'+v.content+'</a> <span style="margin-right: 10px;width: 100px;">'+v.create_time+'</span> </p>';
                    };
                });
                $('#poll_msg_bady').html(html).show().siblings('img').siblings('.MsgNull').hide();
                $('#poll_msg_more').show();
            };
        };
        $('#poll_msg_bady').siblings('img').hide();
    };
};

//异步修改
function editStatus(status){
    layer.load(2);
    $.ajax({
        url: ajax_url,
        type: 'post',
        dataType: 'json',
        data: {type:'editStatus',status:status},
        success: function (reflag) {
            layer.closeAll('loading');
            if(reflag.code==0){
                if(status==1){
                    $('.peopleStatus .mang').show().siblings('span').hide();
                    if(!$('.header').hasClass('redBg')){
                        $('.header').addClass('redBg');
                    }
                    //$('#xian').colorbox.close();
                    layer.closeAll();
                }else{
                    $('.peopleStatus .xian').show().siblings('span').hide();
                    if($('.header').hasClass('redBg')){
                        $('.header').removeClass('redBg');
                    }
                    //$('#mang').colorbox.close();
                    layer.closeAll();
                }
                layer.msg(reflag.msg,{icon:1});
            }else{
                layer.msg(reflag.msg,{icon:2});
            }
            return false;
        }
    });
}

function editUrl_iframe(uid){
    $("iframe[name=main]").attr("src",detailUser_url+"?id="+uid);
    setTimeout(function(){
        //$('#automatic').colorbox.close();
        layer.closeAll();
    },800);
}