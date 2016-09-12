$(function(){
	hoverBg();
});

//  编辑/删除
$(".proSelect").on('click', function() {
    $(this).next().toggle();
});

//  列表hover背景/last一列
function hoverBg(){
	//  背景
	$('.setContList').find('dl').hover(function(){
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
	$('.setContList').find('dl').last().css('borderBottom','none');
}
