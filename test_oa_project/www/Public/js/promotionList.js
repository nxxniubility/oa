//  操作
/*$(".proSelect").on('click', function() {
    $(this).next().toggle();
});*/
$(document).on('click', '.proSelect',
    function(event){
    $(this).next().toggle().closest('dl').siblings().find('.otherOperation').hide();
    //阻止点击document对当前事件的影响
    event.stopPropagation();
});

 $(document).click(function() {
	$('.otherOperation').hide();
});


$('.proContMiddle dl:last-child').css('borderBottom','none');
