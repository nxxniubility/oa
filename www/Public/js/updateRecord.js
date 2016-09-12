//  操作
$(document).on('click', '.proSelect',
    function(event){
    $(this).next().toggle().closest('dl').siblings().find('.otherOperation').hide();
    //阻止点击document对当前事件的影响
    event.stopPropagation();
});

 $(document).click(function() {
	$('.otherOperation').hide();
});

//  ID下hover效果
$('.proListDetails').hover(function(){
	$(this).find('.detailBox').show();
},function(){
	$(this).find('.detailBox').hide();
});

$('.proContMiddle dl:last-child').css('borderBottom','none');
