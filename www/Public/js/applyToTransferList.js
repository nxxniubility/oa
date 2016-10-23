/* 筛选条件选中项 */
$(function(){
	//  选中切换
	$(".clickli").click(function(){
		$(this).siblings("li").removeClass("twoCurr");
		$(this).addClass("twoCurr");
		$(".timeBox").css("visibility","visible");
	});

	//自定义日期
	$('.afTime').glDatePicker({onClick:function(el, cell, date, data) {
		el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
		if(el.hasClass("endTiem")){
			var start_time=el.parents(".timeBox").find(".start").val();
			var start_name=el.parents(".timeBox").find(".start").attr("name");
			var end_time=el.val();
			var end_name=el.attr("name");
			location.href=el.attr('data-url')+start_time+'@'+end_time;
		}
	}});
	
	//  编辑/删除
	$(".proSelect").on('click', function() {
	    $(this).next().toggle();
	});
	
	//  背景
	$('.atList').find('dl').hover(function(){
		if($(this).hasClass('setTit')){
			return false;			
		}else{
			$(this).addClass('on');
		}
	},function(){
		if($(this).hasClass('setTit')){
			return false;			
		}else{
			$(this).removeClass('on');
		}
	});
	
	//  last
	$('.atList').find('dl').last().css('borderBottom','none');
});

//  双击打开详情
$(document).on('dblclick',".content_li",function(){
    var el = $(this).find('.hrefDetail');
    $('#hrefForm').attr('action', el.attr('href')).submit();
});

