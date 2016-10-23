/* 筛选条件选中项 */
$(function(){
	//  显示编辑/删除
	$(".proSelect").on('click', function() {
	    $(this).next().toggle();
	});
	
	//  行hover状态下背景
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
	
	$(".clickli").click(function(){
		$(this).siblings("li").removeClass("twoCurr");
		$(this).addClass("twoCurr");
		$(this).siblings(".selectbox ").css("visibility","visible");
	});
	
	//  最后last去除border
	$('.atList').find('dl').last().css('borderBottom','none');
	
	$(".afTime").glDatePicker({onClick:function(el, cell, date, data) {
		el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
		if(el.parent("li").hasClass("start")){
			var start_time=el.val();
			var end_time=el.parents("ul").find(".end input").val();
			var url = el.parents("ul").find(".end input").attr('data-url');
			if(end_time.length>0){
				location.href=url+start_time+'@'+end_time;
			}
		}else if(el.parent("li").hasClass("end")){
			var start_time=el.parents("ul").find(".start input").val();
			var end_time=el.val();
			if(!start_time){
				var myDate = new Date();
				start_time = myDate.getFullYear()+'/'+myDate.getMonth()+'/'+myDate.getDate();
			}
			location.href=el.attr('data-url')+start_time+'@'+end_time;
		}
    }});
});


//  双击打开详情
$(document).on('dblclick',".content_li",function(){
    var el = $(this).find('.hrefDetail');
    $('#hrefForm').attr('action', el.attr('href')).submit();
});

