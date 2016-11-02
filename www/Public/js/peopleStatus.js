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

var _key = 0;
function msg_icon(){
    //var _icon_int = setInterval(_icon_animate,200);
    setTimeout(_icon_animate,200);
    function _icon_animate(){
        if(_key==0){
            //clearInterval(_icon_int);
            $('#poll_total_msg').parents('.message').animate({'background-position':'18px'}).animate({'background-position':'22px'});
            _key++;
        }else{
            _key = 0;
        }
    };
};

function getMsgHint_ajax(){
    common_ajax2(null,getMsgHint_url,'no',_hintbox,1);
    function _hintbox(redata){
        if(redata.code==0){
            //消息数量
            if($('#poll_total_msg').text()!=redata.data.unread_count){
                if($('#poll_total_msg').text()<redata.data.unread_count){
                    layer.tips('有未读新消息哦！', '.message', {
                        tips: 3,
                        time:0
                    });
                };
                $('#poll_total_msg').attr('flag', 'yes').text(redata.data.unread_count);
                msg_icon();
            };
            //消息内容提示窗
            if(redata.data.read_msg){
                //公告层
                layer.open({
                    type: 1
                    ,title: redata.data.read_msg.msgtype_name+'提醒：'+redata.data.read_msg.title //不显示标题栏
                    ,closeBtn: false
                    ,area: '300px;'
                    ,shade: 0.8
                    ,id: 'hone_open_id' //设定一个id，防止重复弹出
                    ,btn: ['查看详情', '取消']
                    ,moveType: 1 //拖拽模式，0或者1
                    ,content: '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">'+redata.data.read_msg.content+'</div>'
                    ,success: function(layero){
                        var btn = layero.find('.layui-layer-btn');
                        btn.css('text-align', 'center');
                        if(redata.data.read_msg.href){
                            btn.find('.layui-layer-btn0').attr({
                                href : redata.data.read_msg.href,
                                target: '_blank'
                            });
                        };
                    }
                });
            };
        };
    };
};

//获取消息列表
function getMsgList_ajax(){
    layer.closeAll();
    if($('#poll_total_msg').attr('flag')=='yes'){
        $('#poll_total_msg').attr('flag','no');
        var data = {isread : 1,page:'0,5'};
        common_ajax2(data,getMsgList_url,'no', _reMsgList,1);
        function _reMsgList(reflag){
            if(reflag.code==0){
                $('#poll_total_msg').text(reflag.data.count);
                if(reflag.data.count==0){
                    $('#poll_msg_bady').empty().siblings('.MsgNull').show();
                }else{
                    var html = '';
                    $.each(reflag.data.data,function(k,v){
                        if(k<3){
                            html += '<p> <a href="'+v.message_id+'" style="margin-top: 0px; width: 263px;">【'+v.msgtype_name+'】'+v.title+'</a> <span style="margin-right: 10px;width: 100px;">'+v.create_time.substr(5,11)+'</span> </p>';
                        };
                    });
                    $('#poll_msg_bady').html(html).show().siblings('img').siblings('.MsgNull').hide();
                    $('#poll_msg_more').show();
                };
            };
            $('#poll_msg_bady').siblings('img').hide();
        };
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
                    };
                    //$('#xian').colorbox.close();
                    layer.closeAll();
                }else{
                    $('.peopleStatus .xian').show().siblings('span').hide();
                    if($('.header').hasClass('redBg')){
                        $('.header').removeClass('redBg');
                    }
                    //$('#mang').colorbox.close();
                    layer.closeAll();
                };
                layer.msg(reflag.msg,{icon:1});
            }else{
                layer.msg(reflag.msg,{icon:2});
            };
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