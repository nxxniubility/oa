var curIndex=0;
$(function(){
	$(".tab div").click(function(){
		var index=$(this).index();
		if(index!=curIndex){
			$(".tab div").siblings().removeClass("cur").eq(index).addClass("cur");
			$(".content").removeClass("active").eq(index).addClass("active");
			curIndex=index;
		}
	});
	
})