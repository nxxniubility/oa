/*重新申请转入*/
/*$('#reApply').click(function() {

    var obj;
    if ('{$applyUserDetails[0].reapply}'){
        obj =$("#popup1");
    }else{
        obj =$("#popup2");
    }
    $.colorbox({
        inline: true,
        href: obj,//obj,
        overlayClose: false,
        title: "申请转入客户"
    });
});*/

//关闭colorbox弹窗
$('.naEtermine').on('click',function(){
	$('.notApply').colorbox.close();
});

/*转介绍显示*/
$('.singleBox').each(function(){
	var $self = $(this);
	$self.find('label').on('click',function(){
		var status=parseInt($(this).find("input").val());
        	$(this).find("input").prop("checked", true);
            $(this).siblings("input").prop("checked", false);
            if(status){
            	$(this).closest('.alRow').next().fadeIn();
            }else{
            	$(this).closest('.alRow').next().fadeOut();
            }
	})
});
