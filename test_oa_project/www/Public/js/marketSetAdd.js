//  员工定位
function employeeType(){
	$(document).on('click','.employee-type span',function(){
		$(this).addClass('selected').siblings().removeClass('selected');
	});
}
employeeType();
