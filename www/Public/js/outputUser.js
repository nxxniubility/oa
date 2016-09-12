/**
 * Created by Administrator on 2016/5/19.
 */
$(".arrowFather1").click(function(){
	$(".arrowFather1").hide();
	$(".frame").show();
	$(".arrowFather").show();
	$(".frame1").hide();
});
$(".arrowFather").click(function(){
	$(".frame").hide();
	$(".arrowFather").hide();
	$(".frame1").show();
	$(".arrowFather1").show();
});
$(".proContMiddle dl").addClass("hover")

$('#userDefined').click(function(){
    //$(".panel").show()
    layer.open({
        type: 1, 					//  页面层
        title: '自定义显示列', 				//	不显示标题栏
        area: ['600px', '540px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $(".panelConcentNew")	//  加载主体内容
    });
});
/*申请预报*/
$('.btn_reserve').click(function(){
    $(':input[name="temp_user_id"]').val($(this).parent('ul').attr('data-value'));
    layer.open({
        type: 1, 					//  页面层
        title: '申请预报', 				//	不显示标题栏
        area: ['600px', '330px'],
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
$('.btn_allocation').click(function(){
    $(':input[name="temp_user_id"]').val($(this).parent('ul').attr('data-value'));
    layer.open({
        type: 1, 					//  页面层
        title: '选择操作者', 				//	不显示标题栏
        area: ['1000px', '580px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $("#panel3")	//  加载主体内容
    });
    getSystemUser();
});
/*放弃*/
$('.btn_abandon').click(function(){
    $(':input[name="temp_user_id"]').val($(this).parent('ul').attr('data-value'));
    layer.open({
        type: 1, 					//  页面层
        title: '放弃客户', 				//	不显示标题栏
        area: ['600px', '330px'],
        closeBtn:2,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        shift: 1, 					//	出现动画
        content: $("#panel4")	//  加载主体内容
    });
});

//  确认客户到访
$('.btn_visited').on('click', function(){
    $.colorbox({
        inline: true,
        href: $("#manually"),
        overlayClose: false,
        title: "确认客户到访"
    });
});
//  确认客户到访

//  客户到访提醒
/*$('.btn_visited').on('click', function(){
    $.colorbox({
        inline: true,
        href: $("#automatic"),
        overlayClose: false,
        title: "客户到访提醒"
    });
});*/
//  客户到访提醒


$(".panel>.panelConcent>p>b").click(function(){$(".panel").hide()})

$(".panel1>.panelConcent>p>b").bind("click",function(){$(".panel1").hide()});

$(".giveUp em").bind("click",function(){$(".panel1").show()});

$(".panel2>.panelConcent>p>b").bind("click",function(){$(".panel2").hide()});

$(".apply em").bind("click",function(){$(".panel2").show()});


$(".panel3 .Capacity .wSev i").click(function(){$(".panel3").hide()});

$(".panel3>.panelConcent>p>b").bind("click",function(){$(".panel3").hide()});

$(".out em").bind("click",function(){$(".panel3").show()});

// $(".edit em").bind("click",function(){
//     if ($(".edit span").hasClass("setEdit")) {
//         $(".edit span").removeClass("setEdit");
//         $(".edit span").addClass("setEdit1");
//         $(".edit em").html("标为普通");
//     }else{
//         $(".edit span").removeClass("setEdit1");
//         $(".edit span").addClass("setEdit")
//         $(".edit em").html("标为重点");
//     };
// });

$(".column_name").click(function(){
    if($(this).is(':checked')){
        $(this).parents('.wOne').siblings('.wThr').children('input').attr("disabled",false);
    }else{
        $(this).parents('.wOne').siblings('.wThr').children('input').attr("disabled",true);
    }
});
$('.details li').click(function(){
    if(!$(this).hasClass('clickli') && !$(this).hasClass('selectbox1')){
        $(this).parent().children('li').removeClass('active');
        $(this).parent().children('.selectbox1').css('visibility','hidden');
    }
    $(this).parent().find('a').removeClass('on_hover');
    $(this).children('a').addClass('on_hover');
    
    frame();
});
$(".clickli").click(function(){
	$(this).siblings("li").removeClass("active");
	$(this).addClass("active");
	$(this).siblings(".selectbox1").css("visibility","visible");
	$(this).siblings('.selectbox1').children('.afTime').glDatePicker({onClick:function(el, cell, date, data) {
			el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
		    if(el.parent("li").hasClass("start")){
                var start_time=el.val();
                var end_time=el.parents("ul").find(".end input").val();
                if(!end_time ||　end_time=='结束时间'){
                    el.parent('li').parent('ul').siblings('input').val(start_time+'@time');
                }else{
					var at=el.parent('li').parent('ul').siblings('input').val();
					at=at.split('@');
					el.parent('li').parent('ul').siblings('input').val(start_time+'@'+(at[1]?at[1]:'time'));						
				}
            }else if(el.parent("li").hasClass("end")){
                var start_time=el.parents("ul").find(".start input").val();
                var end_time=el.val();
                if(!start_time){
                    start_time=0;
                }
                el.parent('li').parent('ul').siblings('input').val(start_time+'@'+end_time);
            }
		}});
	
});
$(function(){
	$('.clickli').each(function(index,domEle){
		if($('.clickli').eq(index).siblings('.start').css('visibility')=="visible"){
			$('.clickli').eq(index).siblings('.selectbox1').children(".afTime").glDatePicker({onClick:function(el, cell, date, data) {
				el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
			   if(el.parent("li").hasClass("start")){
                    var start_time=el.val();
                    var end_time=el.parents("ul").find(".end input").val();
                    if(!end_time||　end_time=='结束时间'){
                        $(this).parent('ul').siblings('input').val(start_time+'@time');
                    }else{
						var at=$(this).parent('ul').siblings('input').val();
						at=at.split('@');
						$(this).parent('ul').siblings('input').val(start_time+'@'+(at[1]?at[1]:'time'));						
					}
                }else if(el.parent("li").hasClass("end")){
                    var start_time=el.parents("ul").find(".start input").val();
                    var end_time=el.val();
                    if(!start_time){                       
                        start_time =0;
                    }
                    $(this).parent('ul').siblings('input').val(start_time+'@'+end_time);
                }
			}});
		}
	});
});

function frame(){
    var str = '';
    $('.frame .on_hover').each(function(k,obj){
        str += '<li><a href="javascript:;">'+$(obj).parent().parent().siblings('span').html()+''+$(obj).html()+'</a></li>';
    });
    $('.frame1').children('ul').html(str);
    return false;
};


function listBody(data_column){
    if(data_column!=0){
        //插入数据
        $.each(data_column,function(k,v){
            $('.title_'+ v.columnname).attr('isShow','true').attr('sort',v.sort);
            $('.content_'+ v.columnname).attr('isShow','true').attr('sort',v.sort);
            $('.column_'+ v.columnname).attr('checked',true).parents('.wOne').siblings('.wThr').children('input').val(v.sort).attr("disabled",false);
        });
        //过滤
        $('.tr_title,.tr_content').each(function(i){
            if($(this).attr('isShow')!='true'){
                $(this).remove();
            };
        });
        //排序
        var sortTitle = $('#title .tr_title').sort(function(a, b) {
            return $(a).attr('sort') - $(b).attr('sort');
        });
        $('#title').empty().append(sortTitle);
        $('.content_li').each(function(){
            var _thisObj = $(this);
            var sortContent = _thisObj.find('.tr_content').sort(function(a, b) {
                return $(a).attr('sort') - $(b).attr('sort');
            });
            _thisObj.empty().append(sortContent);
        });
    };
    if($('.tr_title').length<=8){
        $('.tr_title,.tr_content').css('width',(parseInt($('.listBody').width())/$('.tr_title').length)+'px');
        $('.listBody').css('overflow-x','hidden');
    }
    return false;
};
$('.content_li,.box_li,.dispost_li').mousemove(function(){
	$('.on_li').removeClass('on_li');
    var num = $(this).index();
    $('.content_li').eq(num).addClass('on_li');
    $('.box_li').eq(num).addClass('on_li');
    $('.dispost_li').eq(num).addClass('on_li');
}).mouseout(function(){
    var num = $(this).index();
    $('.content_li').eq(num).removeClass('on_li');
    $('.box_li').eq(num).removeClass('on_li');
    $('.dispost_li').eq(num).removeClass('on_li');
});

