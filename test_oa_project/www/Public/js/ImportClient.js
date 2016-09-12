/* 筛选条件选中项 */
$(function(){	
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

	$(".toggle .tLeft").click(function(){
		$(".lost").hide();$(".win").show();

	})

	$(".toggle .tRight").click(function(){
		$(".win").hide();$(".lost").show();
	})


});



