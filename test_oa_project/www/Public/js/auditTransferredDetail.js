$(function(){
	//���ڵ�ѡ
    $(".l-row").each(function() {
        var wrapper = $(this);
        wrapper.find(".b-radio-wrapper").click("on", function() {
        	var status=parseInt($(this).find('a').siblings("input").val());
        	$(this).find('a').addClass('b-radio-checked').siblings("input").prop("checked", true);
            $(this).siblings().find("a").removeClass("b-radio-checked").siblings("input").prop("checked", false);
            if(status){
            	$(".reason").hide();
            }else{
            	$(".reason").show();
            	//����༭��������
            }
        });
    });
	
   
})