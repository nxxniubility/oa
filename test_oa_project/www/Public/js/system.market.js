//初始化
if(market_zone_id!=''){
	$('.city_title').text($('#zone_'+market_zone_id).text());
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
	$('.position_name').text(temp_role_names);
};
$(function(){
	$('.chart_tab').children('li').eq(0).trigger('click');
})
//  遮罩层-全局
var mask = $('#mask');

//  显示地区选择弹层
$(document).on('click', '.city_title', function(){
	mask.show();
	$(this).parent().find('.seach_city_show').removeClass('dn');
	areaChoose();
});
//  显示职位选择弹层
$(document).on('click', '.position_name', function(){
	mask.show();
	$(this).parent().find('.search_position_show').removeClass('dn');
	var role_ids = $(':input[name="role_id"]').val();
	if(role_ids!=''){
		role_ids = role_ids.split(',');
		$(':input[name="sale_inp"]').prop('checked',false);
		$.each(role_ids,function(k,v){
			$(':input[name="sale_inp"][value="'+v+'"]').prop('checked',true);
		});
	}
	if($(':input[name="sale_inp"]:checked').length==$(':input[name="sale_inp"]').length){
		$('#all_select').prop('checked',true);
	}
	openPosition();
	close_layer();
	positionChoose();
});

//  关闭职位弹层
function close_layer(){
	var $close = $('.cancel');
	$close.click(function(){
		$close.closest('.search_position_show').addClass('dn');
		mask.hide();
	});
}

//  地区赋值
function areaChoose(){
	$(document).on('click', '.city_partition a', function(){
		var txt = $.trim($(this).text()),
			cityShow = $(this).closest('.seach_city_show'),
			finalZone = $(this).closest('.search_region');
		finalZone.find('.city_title em').text(txt);
		finalZone.find(':input[name="zone_id"]').val($(this).attr('data-value'));
		cityShow.addClass('dn');
		mask.hide();
	});
}
//  展开部门职位
function openPosition(){
	var _this = $('.position_department'),
		_arrow = _this.find('i'),
		_other = $('.position_destail');
	_this.click(function(){
		var maxLength = $(this).parent().parent().find('li').length,
			_index = $(this).parents('li').index();
		if( _index + 1 == maxLength){
			$(this).parent().find(_other).slideDown(500).parent().find(_arrow).addClass('up');
			$(this).parent().siblings().find(_other).slideUp(500).parent().find(_arrow).removeClass('up');
			$(this).addClass('bor_bottom');
		}else {
			$(this).parent().find(_other).slideDown(500).parent().find(_arrow).addClass('up');
			$(this).parent().siblings().find(_other).slideUp(500).parent().find(_arrow).removeClass('up');
			$(this).parents('.position_list').find('li').eq(maxLength-1).find(_this).removeClass('bor_bottom');
		}
	});
}

//  职位赋值
function positionChoose(){
	$(document).on('click', '.confirm', function(){
		var _this = $('.position_list'),
			_checkbox = $(':input[name="sale_inp"]:checked'),
			$close = $('.cancel'),
			_role_ids = '',
			_role_names = '';
		_checkbox.each(function(){
			if(_role_ids==''){
				_role_ids =  $(this).val();
				_role_names = $(this).attr('data-name');
			}else{
				_role_ids +=  ','+$(this).val();
				_role_names += ','+$(this).attr('data-name');
			};
		});
		$(this).next('input').val(_role_ids);
		if(_role_names.length>13){
			_role_names = _role_names.substring(0,13)+'...';
		}
		$('.position_name').text(_role_names);
		$close.closest('.search_position_show').addClass('dn');
		mask.hide();
	});

	$(document).on('click', '#all_select', function(){
		if($(this).is(':checked')){
			$(':input[name="sale_inp"]').prop('checked',true);
		}else{
			$(':input[name="sale_inp"]').prop('checked',false);
		};
	});
};


// 开始时间
$(document).ready(function(){
	var _daytime = market_daytime.split('-');
	setTimeout(function(){
		$(".startime").val(_daytime[0]).glDatePicker({});
    },500)
});
// 结束时间
$(document).ready(function(){
	var _daytime = market_daytime.split('-');
	setTimeout(function(){
		$(".endtime").val(_daytime[1]).glDatePicker({});
    },500)
});

//  切换
var curIndex=0;	//  初始化

//  地区选择城市切换
$(document).on('click', '.city_largearea li', function(){
	var index=$(this).index();
	if(index!=curIndex){
		$(".city_largearea li").siblings().removeClass("cur").eq(index).addClass("cur");
		$(".show_city_cont").removeClass("active").eq(index).addClass("active");
		curIndex=index; //  当前下标赋予变量
	}
});

//  每日数据与员工数据
$(document).on('click', '.sr_tab span', function(){
	var index=$(this).index();
	if(index!=curIndex){
		$(".sr_tab span").siblings().removeClass("current").eq(index).addClass("current");
		$(".sr_table").removeClass("active").eq(index).addClass("active");
	    curIndex=index; //  当前下标赋予变量
	}
});

//  图表部分
//  单项数据切换
$(document).on('click', '.chart_tab li', function(){
	var index = $(this).index();
	var chart_main = $('.chart_main');
	//if(index!=curIndex && curIndex!=0){
		$(".chart_tab li").siblings().removeClass("cur").eq(index).addClass("cur");
		$(".chart_main").removeClass("active").eq(index).addClass("active");
	    curIndex=index; //  当前下标赋予变量
		if($(this).attr('flag')!='true'){
			chart_main.eq(index).find('.chart_topright').children('select').trigger('change');
			$(this).attr('flag','true');
		}
	//}
});

//  选择指标select切换
$('.chart_topright select').change(function(){
	var _curVal = $(this).children('option:selected').val(),
		_chartnav=$('.chart_tab .cur').attr('data-value'),
		_chartname=$('.chart_tab .cur').text(),
		_please_select = $('#'+_chartnav).find('.please_select'),
		_daily_stats = $('#'+_chartnav).find('.daily_stats'),
		_channel = $('#'+_chartnav).find('.daily_channel2'),
		_channel_bar = $('#'+_chartnav).find('.channel_bar2'),
		_channel_pie = $('#'+_chartnav).find('.channel_pie2'),
		_channel3 = $('#'+_chartnav).find('.daily_channel3'),
		_channel_bar3 = $('#'+_chartnav).find('.channel_bar3'),
		_channel_pie3 = $('#'+_chartnav).find('.channel_pie3'),
		_channel4 = $('#'+_chartnav).find('.daily_channel4'),
		_channel_bar4 = $('#'+_chartnav).find('.channel_bar4'),
		_channel_pie4 = $('#'+_chartnav).find('.channel_pie4'),
		_quality = $('#quality'),
		_course = $('#course');
	_please_select.hide();
	//获取接口
	var data = {
		daytime:market_daytime,
		type:_chartnav
	};
	common_ajax2(data,get_info_url,'no',getHighcharts);
	function getHighcharts(redata){
		if(redata.code==0){
			if(_curVal == '1'){
				//每日统计
				var _days = [];
				var _dayVal = [];
				if(!redata.data.days){
					_please_select.show();
				}else{
					$.each(redata.data.days,function(k,v){
						_days.push(k);
						_dayVal.push(v);
					});
					dailyStats(_days,_dayVal,_chartname);
				};
				_quality.hide();
				_course.hide();
				_channel.hide();
				_channel_bar.empty();
				_channel_pie.empty();
				_channel3.hide();
				_channel_bar3.empty();
				_channel_pie3.empty();
				_channel4.hide();
				_channel_bar4.empty();
				_channel_pie4.empty();
			}else if(_curVal == '2'){
				var _navName = [];
				var _values = [];
				var _data_pie = [];
				if(redata.data.channel.length<1){
					//layer.msg('暂无该项数据',{icon:5});
					_please_select.show();
				}else{
					$.each(redata.data.channel,function(k,v){
						var _data = [];
						if(_navName.indexOf(v.pname)<0){
							_navName.push(v.pname);
						};
						for(var i=1;i<_navName.length;i++){
							_data.push(null);
						}
						_data.push(v.count);
						_data = {
							name: k,
							data: _data
						};
						_values.push(_data);
						_data_pie.push([k, v]);
					});
					channelBar(_navName,_values,_curVal);
					channelPie(_data_pie,_curVal);
				};
				_channel.show();
				_quality.hide();
				_course.hide();
				_daily_stats.empty();
				_channel3.hide();
				_channel_bar3.empty();
				_channel_pie3.empty();
				_channel4.hide();
				_channel_bar4.empty();
				_channel_pie4.empty();
			}else if(_curVal == '3'){
				var _navName = [];
				var _values = [];
				var _data_pie = [];
				if(!redata.data.infoquality){
					//layer.msg('暂无该项数据',{icon:5});
					_please_select.show();
				}else{
					$.each(redata.data.infoquality,function(k,v){
						var _data = [];
						$.each(_navName,function(k2,v2){
							_data.push(null);
						});
						_data.push(v);
						_data = {
							name: k,
							data: _data
						};
						_navName.push(k);
						_values.push(_data);
						_data_pie.push([k, v]);
					});
					channelBar(_navName,_values,_curVal);
					channelPie(_data_pie,_curVal);
				};
				_channel3.show();
				_quality.hide();
				_course.hide();
				_channel.hide();
				_daily_stats.empty();
				_channel_bar.empty();
				_channel_pie.empty();
				_channel4.hide();
				_channel_bar4.empty();
				_channel_pie4.empty();
			}else if(_curVal == '4'){
				var _navName = [];
				var _values = [];
				var _data_pie = [];
				if(!redata.data.course_id){
					//layer.msg('暂无该项数据',{icon:5});
					_please_select.show();
				}else{
					$.each(redata.data.course_id,function(k,v){
						var _data = [];
						$.each(_navName,function(k2,v2){
							_data.push(null);
						});
						_data.push(v);
						_data = {
							name: k,
							data: _data
						};
						_navName.push(k);
						_values.push(_data);
						_data_pie.push([k, v]);
					});
					channelBar(_navName,_values,_curVal);
					channelPie(_data_pie,_curVal);
				};
				_channel4.show();
				_quality.hide();
				_course.hide();
				_channel.hide();
				_daily_stats.empty();
				_channel_bar.empty();
				_channel_pie.empty();
				_channel3.hide();
				_channel_bar3.empty();
				_channel_pie3.empty();
			};
		};
	};
});

//  各图标初始化
//dailyStats();	//  新增量每日统计线型

//  新增量
function dailyStats(days,values,name){
	var _chartnav=$('.chart_tab .cur').attr('data-value');
	$('#'+_chartnav).find('.daily_stats').highcharts({
		chart: {
			type: 'line'
		},
		title: {
			text: null
		},
		subtitle: {
			text: null
		},
		credits: { enabled:false},
        exporting: { enabled:false},	//  去打印
		xAxis: {
			categories: days
		},
		yAxis: {
			title: {
				text: false
			}
		},
		plotOptions: {
			line: {
				dataLabels: {
					enabled: true
				},
				enableMouseTracking: false
			}
		},
		series: [{
			name: name,
			data: values
		}]
	});
}



//  来源渠道-柱状图
function channelBar(navName,values,num){
	var _chartnav=$('.chart_tab .cur').attr('data-value');
	$('#'+_chartnav).find('.channel_bar'+num).highcharts({
		chart:{
			className:'channel_bar',
			type:'bar'
		},
		chart: { type: 'column'},
        title: null,
        credits: { enabled:false},
        exporting: { enabled:false},
        xAxis: {
            categories: navName
        },
		yAxis: {
			min: 0,
			title: {
				text: null
			},stackLabels: {
				enabled: true,
				style: {
					fontWeight: 'bold',
					color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
				}
			}
		},
        tooltip: {
            formatter: function () {
                return '<b>' + this.x + ':'+this.point.stackTotal+' </b><br/>' +
                   '<span style="color:'+this.series.color+'">'+this.series.name+': ' + this.y + '</span>';
            }
        },
		plotOptions: {
			column: {
				stacking: 'normal',
				dataLabels: {
					enabled: true,
					color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
					style: {
						textShadow: '0 0 3px black'
					}
				}
			}
		},
        series:values
	});
}

// 来源渠道-圆饼图
function channelPie(values,num){
	var _chartnav=$('.chart_tab .cur').attr('data-value');
	$('#'+_chartnav).find('.channel_pie'+num).highcharts({
		chart: {
			className:'channel_pie',
	        plotBackgroundColor: null,
	        plotBorderWidth: 0,
	        plotShadow: false
	    },
	    title: {
	        text: '各渠道所占百分比',
	        verticalAlign: 'bottom'
	    },
	    tooltip: {
	        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
	    },
	    credits: {enabled:false},
	    exporting: { enabled:false},
	    plotOptions: {
	        pie: {
	            allowPointSelect: true,
	            cursor: 'pointer',
	            dataLabels: {
	                enabled: true,
	                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
	            }
	        }
	    },
	    series: [{
	        type: 'pie',
	        name: '所占百分比为：',
	        data: values

	    }]
	});
}