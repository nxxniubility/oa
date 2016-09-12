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
		if(el.hasClass("end")){
			var start_time=el.parents(".timeBox").find(".start").val();
			var start_name=el.parents(".timeBox").find(".start").attr("name");
			var end_time=el.val();
			var end_name=el.attr("name");

			location.href=el.attr('data-url')+'?dateStart='+start_time+'&dateEnd='+end_time;
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
	
	//  详情
	$('.atList dl').each(function(index, e) {
		
		$(e).hover(function(e){
			$('.detailLink').hide();
			var e = e || window.event;	
			var table_w=$('.atList').width();
			var _x=(e.pageX?e.pageX:e.clientX)-parseInt($('.atList dl').offset().left)+80;
			if((_x-table_w)>-50)
			{
				_x=_x-100;
			}
			var _y=parseInt($(this).offset().top)-parseInt($('.atList dl').offset().top)+9;
			$(this).find('.detailLink').css({"left":_x,"top":_y});
			$(this).find('.detailLink').show();
	
		},function(e){
	
			$(this).find('.detailLink').hide();
	
		});
		
	});
	$('.detailLink').on("mouseover",function(e){
	
		var e = e || window.event;
		e.stopPropagation();
		e.preventDefault();
		return false;
	});
	$('.detailLink').on("mouseenter",function(e){
		var e = e || window.event;
		e.stopPropagation();
		e.preventDefault();
		return false;
	});
	$('.detailLink').on("mouseleave",function(e){
		var e = e || window.event;
		e.stopPropagation();
		e.preventDefault();
		return false;
	});
	$('.detailLink').on("mouseout",function(e){
		var e = e || window.event;
		e.stopPropagation();
		e.preventDefault();
		return false;
	});
});


