//  闲
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
var hint_tips = '';
function getMsgHint_ajax(){
    common_ajax2(null,getMsgHint_url,'no',_hintbox,1);
    function _hintbox(redata){
        if(redata.code==0){
            //消息数量
            if($('#poll_total_msg').text()!=redata.data.unread_count){
                if($('#poll_total_msg').text()<redata.data.unread_count){
                    hint_tips = layer.tips('有未读新消息哦！', '.message', {
                        tips: 3,
                        time:0
                    });
                };
                $('#poll_total_msg').attr('flag', 'yes').text(redata.data.unread_count);
                msg_icon();
            };
            //消息内容提示窗
            if(redata.data.read_msg){
                var content = redata.data.read_msg.content;
                if(redata.data.read_msg.href){
                    content += '<a href="'+redata.data.read_msg.href+'" style="color:#0055aa" target="_blank">查看详情</a>';
                };
                $('.hint_bady').children('.ct_msg').html(content);
                //公告层
                layer.open({
                    type: 1
                    ,title: '【'+redata.data.read_msg.msgtype_name+'】'+redata.data.read_msg.title //不显示标题栏
                    ,closeBtn: false
                    ,area: '330px;'
                    ,id: 'hone_open_id' //设定一个id，防止重复弹出
                    ,btn: ['查看更多', '关闭']
                    ,moveType: 1 //拖拽模式，0或者1
                    ,shade:0
                    ,content: $('.hint_bady')
                    ,success: function(layero){
                        var btn = layero.find('.layui-layer-btn');
                        btn.css('text-align', 'center');
                        btn.find('.layui-layer-btn0').attr({
                            href : msgList_url,
                            target: 'main'
                        });
                    }
                });
            };
        };
    };
};

//获取消息列表
function getMsgList_ajax(){
    layer.close(hint_tips);
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
                            html += '<p> <a href="javascript:;" onclick="getMsgIngo('+v.message_id+',this)" data-href="'+v.href+'" data-content="'+v.content+'">【'+v.msgtype_name+'】'+v.title+'</a> <span>'+v.create_time.substr(5,11)+'</span> </p>';
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

//查看消息详细
function getMsgIngo(id,thisObj){
    var content = $(thisObj).attr('data-content');
    if($(thisObj).attr('data-href').length>0){
        content += '<a href="'+$(thisObj).attr('data-href')+'" style="color:#0055aa" target="_blank">查看详情</a>';
    };
    $('.hint_bady').children('.ct_msg').html(content);
    //公告层
    layer.open({
        type: 1
        ,title: $(thisObj).html() //不显示标题栏
        ,closeBtn: false
        ,area: '330px;'
        ,id: 'hone_open_id' //设定一个id，防止重复弹出
        ,btn: ['查看更多', '取消']
        ,moveType: 1 //拖拽模式，0或者1
        ,shade:0
        ,content: $('.hint_bady')
        ,success: function(layero){
            var btn = layero.find('.layui-layer-btn');
            btn.css('text-align', 'center');
            btn.find('.layui-layer-btn0').attr({
                href : msgList_url,
                target: 'main'
            });
        }
    });
    $('#poll_total_msg').text($('#poll_total_msg').text()-1).attr('flag','yes');
    var data = {
        message_id : id,
        type : 'getInfo'
    };
    common_ajax2(data,msgList_url,'no',_redata,1);
    function _redata(){};
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