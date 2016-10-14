/**
 * Created by Administrator on 2016/5/19.
 */

$(".arrowFather1").click(function(){
	$(".arrowFather1").hide();
	$(".frame").show();
	$(".frame1").hide();
});
$(".arrowFather").click(function(){
	$(".frame").hide();
	$(".frame1").show();
	$(".arrowFather1").show();
});
$('.details').find('li').click(function(){
	//$(this).addClass('active').siblings().removeClass('active');
	if(!$(this).hasClass('selectbox1')){
		if($(this).hasClass('clickli')){
			$(this).siblings(".selectbox1").css("visibility","visible");
		}else {
			$(this).siblings(".selectbox1").css("visibility","hidden");
		}
	}
});
$(".clickli").click(function(){
	$(this).siblings("li").removeClass("active");
	$(this).addClass("active");
	$(this).siblings(".selectbox1").css("visibility","visible");
});
$(".proContMiddle dl").addClass("hover");

function userDefined(){$(".panel").show()}
$(".panel>.panelConcent>p>b").click(function(){$(".panel").hide()});

$(".panel1>.panelConcent>p>b").bind("click",function(){$(".panel1").hide()});

$(".giveUp em").bind("click",function(){$(".panel1").show()});

$(".panel2>.panelConcent>p>b").bind("click",function(){$(".panel2").hide()});

$(".apply em").bind("click",function(){$(".panel2").show()});


$(".panel3 .Capacity .wSev i").click(function(){$(".panel3").hide()});

$(".panel3>.panelConcent>p>b").bind("click",function(){$(".panel3").hide()});

$(".out em").bind("click",function(){$(".panel3").show()});
$(".afTime").glDatePicker({onClick:function(el, cell, date, data) {
	el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
	if(el.parent("li").hasClass("end")){
		var start_time=el.parents("ul").find(".start input").val();
		var end_time=el.val();
		console.log(start_time+'---'+end_time);
		//location.href="";
	}
}});

$(".Capacity .overflow .wOne input").click(function(){
    $.each($(".Capacity .overflow .wOne input"),function(i,n){
        if(this.checked){
            $($(".Capacity .overflow .wThr input")[i]).attr("disabled",false);
        }
    })
});

