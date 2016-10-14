//  操作
$(document).on('click', '.orgSelect', function(event){
    $(this).next().toggle().closest('tr').siblings().find('.otherOperation').hide();
    //  阻止点击document对当前事件的影响
    event.stopPropagation();
});
//  点击页面任意地方隐藏otherOperation
$(document).click(function() {
	$('.otherOperation').hide();
});