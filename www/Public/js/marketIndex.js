/*//初始化-职位
if(market_zone_id!=''){
	if($('#zone_'+market_zone_id).text().length>0){
		$('.region-name em').text($('#zone_'+market_zone_id).text());
	}
};
if(market_role_id!=''){
	var temp_role_id = market_role_id.split(',');
	var temp_role_names = '';
	$.each(temp_role_id, function(k, v){
		if(temp_role_names==''){
			temp_role_names += $('#sale'+v).attr('data-name');
		}else{
			temp_role_names += ','+$('#sale'+v).attr('data-name');
		}
	});
	if(temp_role_names.length>13){
		temp_role_names = temp_role_names.substring(0,13)+'...';
	}
	$('.position-name em').text(temp_role_names);
};
*/

//  遮罩层-全局
var mask = $('#mask');

//  显示地区选择弹层
$(document).on('click', '.region-name', function(){
	mask.show();
	$(this).parent().find('.region-selection').removeClass('dn');
	//  中心赋值
	centralAssignment();
	//  城市赋值
	cityAssignment();
	//  重置地区
	areaReset();
	//  点遮罩关闭
	layerClose();
});
//  中心赋值
function centralAssignment(){
	$(document).on('click', '.center-list a', function(){
		var txt = $.trim($(this).text()),
			regionShow = $(this).closest('.region-selection'),
			finalZone = $(this).closest('.region');
			
		finalZone.find('.region-name em').text(txt);
		finalZone.find(':input[name="zone_id"]').val($(this).attr('data-value'));
		regionShow.addClass('dn');
		mask.hide();
	});
}
//  城市赋值
function cityAssignment(){
	$(document).on('click', '.selection-center span', function(){
		if($(this).attr('data-value')!=''){
			var coreTxt = $.trim($(this).text()),
				regionShow = $(this).closest('.region-selection'),
				finalZone = $(this).closest('.region');

			finalZone.find('.region-name').text(coreTxt);
			regionShow.addClass('dn');
			mask.hide();
			$(':input[name="zone_id"]').val($(this).attr('data-value'));
		};
	});
}

//  地区重置
function areaReset(){
	$(document).on('click', '.reset_btn', function(){
		$(this).closest('.region').find('.region-name em').text('请选择统计区域');
		$(this).closest('.region-selection').addClass('dn');
		$(this).next('input[name="zone_id"]').val('');
		mask.hide();
	});
}


//  部门/职位 选择弹层
$(document).on('click', '.position-name', function(){
	mask.show();
	$(this).parent().find('.position-selection').removeClass('dn');
	var role_ids = $(':input[name="role_id"]').val();
	if(role_ids!=''){
		role_ids = role_ids.split(',');
		$(':input[name="sale_inp"]').prop('checked',false);
		$.each(role_ids,function(k,v){
			$(':input[name="sale_inp"][value="'+v+'"]').prop('checked',true);
		});
	}
	//  职位赋值
	positionChoose();
	//  点遮罩关闭
	layerClose();
	//  点取消关闭
	cancelClose();
});

//  展开部门职位
openPosition();
//  展开部门职位
function openPosition(){
	var _this = $('.position-department'),
		_arrow = _this.find('i'),
		_other = _this.parent().find($('.position-choose'));
		
	_this.click(function(){
		var maxLength = $(this).parent().parent().find('li').length,
			_index = $(this).parents('li').index();
		if( _index + 1 == maxLength){
			//  同父级下显示/隐藏部门职位
			if($(this).parent().find(_other).css('display') == 'none'){
				$(this).parent().find(_other).slideDown(500).parent().find(_arrow).addClass('up');				//  自身未显示则向下展开(带箭头指向)	
				$(this).parent().siblings().find(_other).slideUp(500).parent().find(_arrow).removeClass('up');	//  点击其他则收起上一个展开项
			}else {
				$(this).parent().find(_other).slideUp(500).parent().find(_arrow).removeClass('up');
			}
			$(this).toggleClass('border-btm');
		}else {
			if($(this).parent().find(_other).css('display') == 'none'){
				$(this).parent().find(_other).slideDown(500).parent().find(_arrow).addClass('up');
				$(this).parent().siblings().find(_other).slideUp(500).parent().find(_arrow).removeClass('up');
			}else {
				$(this).parent().find(_other).slideUp(500).parent().find(_arrow).removeClass('up');
			}
			//$(this).parent().siblings().find(_other).slideUp(500).parent().find(_arrow).removeClass('up');
			$(this).parents('.position_list').find('li').eq(maxLength-1).find(_this).removeClass('border-btm');
		}
	});
}
//  职位赋值
function positionChoose(){
	$(document).on('click', '.confirm', function(){
		var _this = $('.posiiton-list'),
			_checkbox = $(':input[name="sale_inp"]:checked'),
			//all_checkfalse = $('#all_select').is(':checked'),
			position_close = $('.cancel'),
			_role_ids = '',
			_role_names = '';
		
		//  子项全选与不全选val赋值
		_checkbox.each(function(){
			if(_role_ids==''){
				_role_ids =  $(this).val();
				_role_names = $(this).attr('data-name');
			}else{
				_role_ids +=  ','+$(this).val();
				_role_names += ','+$(this).attr('data-name');
			}
		});
		$(this).next('input').val(_role_ids);
		
		/*//  假如全选，则限制显示前十三个字
		if(all_checkfalse){
			if(_role_names.length>13){
				_role_names = _role_names.substring(0,13)+'...';
			}
			$('.position_name em').text(_role_names);
		}else {		//  假如没全选，有两种情况
			
			//  全选按钮没全选，但职位则有选择
			if (!all_checkfalse && _checkbox[0] ){
				if(_role_names.length>13){
					_role_names = _role_names.substring(0,13)+'...';
				}
				$('.position_name em').text(_role_names);
			}else {		//  全选按钮和职位都没有选
				$('.position_name em').text('请选择职位');			
			}
		}
		*/
		
		//$('.position_name em').text(_role_names);
		position_close.closest('.position-selection').addClass('dn');
		mask.hide();
	});

	/*//  全选
	$(document).on('click', '#all_select', function(){
		if($(this).is(':checked')){
			$(':input[name="sale_inp"]').prop('checked',true);
		}else{
			$(':input[name="sale_inp"]').prop('checked',false);
		};
	});*/
};

/*//  子项控制全选按钮：全选与不全选
$(document).ready(function(){
	$('input[name="sale_inp"]').on('click', function(){
		allChk();
	});
});*/

/*//  各职位子项与职位总全选按钮关联
function allChk(){ 
    var chknum = $('input[name="sale_inp"]').length;//选项总个数 
    var chk = 0; 
    $('input[name="sale_inp"]').each(function () {   
        if($(this).is(':checked')){ 
            chk++; 
        } 
    }); 
    if(chknum==chk){//全选 
        $("#all_select").prop("checked",true); 
    }else{//不全选 
        $("#all_select").prop("checked",false); 
    } 
}
*/
//  关闭职位弹层
function cancelClose(){
	var _close = $('.cancel');
	_close.click(function(){
		_close.closest('.position-selection').addClass('dn');
		mask.hide();
	});
}
//  点遮罩关闭地区\职位layer
function layerClose(){
	$(document).on('click', '#mask', function(){
		$('.region-selection, .position-selection').addClass('dn');
		mask.hide();
	});
}

// 开始时间
$(document).ready(function(){
	//var _daytime = market_daytime.split(','),
	var	my_date = new Date();
	setTimeout(function(){
		$(".startime").glDatePicker({
			selectableDateRange: [
	            {
	            	from: new Date(1990, 1, 1) ,
	            	to: new Date(my_date.getFullYear(), my_date.getMonth(), my_date.getDate()-1)
	            }
	        ]
		});
    },500)
});
// 结束时间
$(document).ready(function(){
	//var _daytime = market_daytime.split(','),
	var	my_date = new Date();
	setTimeout(function(){
		$(".endtime").glDatePicker({
			selectableDateRange: [
	            {
	            	from: new Date(1990, 1, 1) ,
	            	to: new Date(my_date.getFullYear(), my_date.getMonth(), my_date.getDate()-1)
	            }
	        ]
		});
    },500)
});

//  切换
var curIndex=0;	//  初始化

//  地区选择城市切换
$(document).on('click', '.region-list li', function(){
	var index=$(this).index();
	if(index!=curIndex){
		$(".region-list li").siblings().removeClass("cur").eq(index).addClass("cur");
		$(".selection-center").removeClass("active").eq(index).addClass("active");
		curIndex=index; //  当前下标赋予变量
	}
});



function daytime(){
	var startime = $(':input[name="startime"]').val();
	var endtime = $(':input[name="endtime"]').val();
	if(!DateDiff(startime,endtime)){
		layer.msg('开始日期不能大于结束日期', {icon:2});return false;
	}else if(DateDiff(startime,endtime)>62){
		layer.msg('日期选择区间不能大于62天', {icon:2});return false;
	}
}
//换算时间区间天数
function  DateDiff(sDate1,  sDate2){    //sDate1和sDate2是2006-12-18格式
	var  aDate,  oDate1,  oDate2,  iDays
	aDate  =  sDate1.split("/")
	if(aDate.length<3){
		aDate  =  sDate1.split("-")
	}
	oDate1  =  new  Date(aDate[1]  +  '/'  +  aDate[2]  +  '/'  +  aDate[0])    //转换为12/18/2006格式
	aDate  =  sDate2.split("/")
	if(aDate.length<3){
		aDate  =  sDate2.split("-")
	}
	oDate2  =  new  Date(aDate[1]  +  '/'  +  aDate[2]  +  '/'  +  aDate[0])
	if(Math.abs(oDate1)>Math.abs(oDate2)){
		return false;
	}
	iDays  =  parseInt(Math.abs(oDate1  -  oDate2)  /  1000  /  60  /  60  /24)    //把相差的毫秒数转换为天数
	return  iDays + 1
}

