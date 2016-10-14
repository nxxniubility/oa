$(function(){
	sideber();
	content();
	loguot();
	showMsgList();
	$(window).resize(function(){
		content();
	});
});
//侧边菜单
function sideber(){
	var h = $(window).height() -50;
	$(".sidebarbox").height(h);
	
	$(".sideber_title").click(function(){
		if($(this).next().is(":hidden")){
			$(this).find("span.sideber_title_icon").removeClass("sideber_title_icon_on");
			$(this).removeClass("sideber_title_on");
		} else {
			$(this).find("span.sideber_title_icon").addClass("sideber_title_icon_on");
			$(this).addClass("sideber_title_on");
		}
		/*$(this).next().animate({
			height:'toggle',
		});*/
		$(this).next(".sideber_trans").slideToggle();
	});
	
	$('.sidebarbox').bind('mousewheel', function(event,delta,deltaX,deltaY) {
          event.preventDefault();
          var scrollTop = this.scrollTop;
          this.scrollTop = (scrollTop + ((event.deltaY * event.deltaFactor) * -1));
          //IS IE?
		    if(navigator.userAgent.indexOf("MSIE")>0)
		    {
		        if(navigator.userAgent.indexOf("MSIE 8.0")>0)
		        {
		            //	alert("ie8");
		            this.scrollTop = (scrollTop + ((event.deltaY * event.deltaFactor * 50) * -1));
		        }
		    }
          //console.log(event.deltaY, event.deltaFactor, event.originalEvent.deltaMode, event.originalEvent.wheelDelta);
	});
	
	$(".sidebar_btn").click(function(){
		var sidebarbox = $(".sidebarbox");
		if(sidebarbox.width() ==179){
			sidebarbox.width(50);
			$(".sideber_title span.sidebar_manage").hide();
			$(".sideber_title span.icon_setup").hide();
			$(".sideber_trans span.nav-title").hide();
			$('.sidebarbox .sideber_trans a .link_icon').css('margin','11px 16px 10px');
		}else{
			sidebarbox.width(179);
			$(".sideber_title span.sidebar_manage").show();
			$(".sideber_title span.icon_setup").show();
			$(".sideber_trans span.nav-title").show();
			$('.sidebarbox .sideber_trans a .link_icon').css('margin','11px 16px 0 30px');
		}
		content();
	});
	
	$(".sideber_trans li").click(function(){
		$(".sideber_trans li").removeClass("on");
		$(this).addClass("on");
	});
}

//内容区域高宽
function content(){
	var w = $(window).width();
	var h = $(window).height();
	var sideber_w = $(".sidebarbox").width()+2;
	var content_w = w - sideber_w;
	var content_h = h - 50;
	$(".content").width(content_w).height(content_h);
	$(".sidebarbox").height(content_h);
}
//  头部显示消息
function showMsgList() {
	$('.message').mouseover(function(){
		$('.msgbody').show();
	});
	$('.message').mouseout(function(){
		$('.msgbody').hide();
	});
}
//  退出
function loguot() {
	$(".logout_parent").mouseover(function(){
		$(this).children(".logout").show();
		$(this).css({background: "white",color: "#333"});;
		$(this).children('a').css({background: "white",color: "#333"});
	});
	$(".logout_parent").mouseout(function(){
		$(this).children(".logout").hide();
		$(this).css({background: "",color: ""});
		$(this).children('a').css({background: "",color: ""});
	});
}