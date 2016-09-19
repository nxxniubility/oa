// 开始时间
$(document).ready(function(){
	setTimeout(function(){
        var myDate = new Date();
        ymd = myDate.getFullYear()+'/'+(myDate.getMonth()+1)+'/'+myDate.getDate();
		$(".startime").val(ymd).glDatePicker({});
    },500)
});
// 结束时间
$(document).ready(function(){
	setTimeout(function(){
        var myDate = new Date();
        ymd = myDate.getFullYear()+'/'+(myDate.getMonth()+1)+'/'+myDate.getDate();
		$(".endtime").val(ymd).glDatePicker({});
    },500)
});