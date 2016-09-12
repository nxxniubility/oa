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

//  操作
$(document).on('click', '.proSelect',function(event){
    $(this).next().toggle().closest('dl').siblings().find('.otherOperation').hide();
    //阻止点击document对当前事件的影响
    event.stopPropagation();
});

 $(document).click(function() {
	$('.otherOperation').hide();
});