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
			role_ids = '';
		_checkbox.each(function(){
			if(role_ids==''){
				role_ids =  $(this).val();
			}else{
				role_ids +=  ','+$(this).val();
			};
		});
		$(this).next('input').val(role_ids);
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
	market_daytime = market_daytime.split('-');
	setTimeout(function(){
        //var myDate = new Date();
        //ymd = myDate.getFullYear()+'/'+(myDate.getMonth()+1)+'/'+myDate.getDate();
		$(".startime").val(market_daytime[0]).glDatePicker({});
    },500)
});
// 结束时间
$(document).ready(function(){
	setTimeout(function(){
        //var myDate = new Date();
        //ymd = myDate.getFullYear()+'/'+(myDate.getMonth()+1)+'/'+myDate.getDate();
		$(".endtime").val(market_daytime[1]).glDatePicker({});
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
	var index=$(this).index();
	if(index!=curIndex){
		$(".chart_tab li").siblings().removeClass("cur").eq(index).addClass("cur");
		$(".chart_main").removeClass("active").eq(index).addClass("active");
	    curIndex=index; //  当前下标赋予变量
	}
});

//  选择指标select切换
$('.chart_topright select').change(function(){
	var _curVal = $(this).children('option:selected').val(),
		_p = $('.please_select'),
		_chartnav=$('.chart_tab .cur').attr('data-value'),
		_chartname=$('.chart_tab .cur').text(),
		_daily_stats = $('#'+_chartnav).find('.daily_stats'),
		_channel = $('#'+_chartnav).find('.daily_channel'),
		_channel_bar = $('#'+_chartnav).find('.channel_bar'),
		_channel_pie = $('#'+_chartnav).find('.channel_pie'),
		_quality = $('#quality'),
		_course = $('#course');
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
				$.each(redata.data.days,function(k,v){
					_days.push(k);
					_dayVal.push(v);
				});
				dailyStats(_days,_dayVal,_chartname);
				_daily_stats.show();
				_p.hide();
				//_channel.hide();
				_channel_bar.empty();
				_channel_pie.empty();
				_quality.hide();
				_course.hide();
			}else if(_curVal == '2'){
				var _navName = [];
				var _values = [];
				var _data = '';
				var _data_pie = [];
				$.each(redata.data.channel,function(k,v){
					$.each(v,function(k2,v2){
						var _val_num = _values.length;
						var _val_data = [];
						if(_val_num>0){
							for(var i=0;i<_val_num;i++){
								_val_data.push(0);
							}
						}
						_val_data.push(v2.count);
						_data = {
							name: v2.name,
							data: _val_data,
							stack: k
						};
						_navName.push(k);
						_values.push(_data);
						_data_pie.push([v2.name, v2.count]);
					});
				});
				channelBar(_navName,_values)
				channelPie(_data_pie);
				_channel.show();
				_p.hide();
				_daily_stats.empty();
				_quality.hide();
				_course.hide();
			}else if(_curVal == '2'){
				var _navName = [];
				var _values = [];
				var _data = '';
				var _data_pie = [];
				$.each(redata.data.channel,function(k,v){
					$.each(v,function(k2,v2){
						var _val_num = _values.length;
						var _val_data = [];
						if(_val_num>0){
							for(var i=0;i<_val_num;i++){
								_val_data.push(0);
							}
						}
						_val_data.push(v2.count);
						_data = {
							name: v2.name,
							data: _val_data,
							stack: k
						};
						_navName.push(k);
						_values.push(_data);
						_data_pie.push([v2.name, v2.count]);
					});
				});
				channelBar(_navName,_values)
				channelPie(_data_pie);
				_channel.show();
				_p.hide();
				_daily_stats.empty();
				_quality.hide();
				_course.hide();
			}
		}
		//if(_curVal == '0'){
		//	_p.show();
		//	//_daily_stats.hide();
		//	//_channel.hide();
		//	_daily_stats.empty();
		//	_channel_bar.empty();
		//	_channel_pie.empty();
		//	_quality.hide();
		//	_course.hide();
		//}else if(_curVal == '1'){
		//	dailyStats();
		//	_daily_stats.show();
		//	_p.hide();
		//	//_channel.hide();
		//	_channel_bar.empty();
		//	_channel_pie.empty();
		//	_quality.hide();
		//	_course.hide();
		//}else if(_curVal == '2'){
		//	channelPie();
		//	channelBar();
		//	_channel.show();
		//	_p.hide();
		//	_daily_stats.empty();
		//	_quality.hide();
		//	_course.hide();
        //
		//}
		/*else if(curVal == '3'){
		 $('#daily_stats2').show();
		 $('#daily_stats2').siblings().hide();
		 }else if(curVal == '4'){
		 $('#daily_stats3').show();
		 $('#daily_stats3').siblings().hide();
		 }*/
	}
});

//  各图标初始化
dailyStats();	//  新增量每日统计线型

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
function channelBar(navName,values){
	var _chartnav=$('.chart_tab .cur').attr('data-value');
	$('#'+_chartnav).find('.channel_bar').highcharts({
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
            allowDecimals: false,
            title: {
                text: null
            }
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y + '<br/>' +
                    '总量: ' + this.point.stackTotal;
            }
        },
         plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series:values
			//[{
			//	name: '360',
			//	data: [5],
			//	stack: '线下院校'			//  分组
			//}, {
			//	name: '智联',
			//	data: [3],
			//	stack: '线下院校'
			//}, {
			//	name: '58同城',
			//	data: [2],
			//	stack: '线下院校'
			//}, {
			//	name: '赶集',
			//	data: [3],
			//	stack: '线下院校'
			//},{
			//	name:'百度',
			//	data: [9],
			//	stack: '线下院校'
			//},{
			//	name:'千度',
			//	data: [0,2],
			//	stack: '在线简历'
			//},{
			//	name:'千度',
			//	data: [0,2],
			//	stack: '在线简历'
			//},{
			//	name:'千度',
			//	data: [0,2],
			//	stack: '在线简历'
			//},{
			//	name:'千度',
			//	data: [0,2],
			//	stack: '在线简历'
			//},{
			//	name:'千度',
			//	data: [0,2],
			//	stack: '在线简历'
			//},{
			//	name:'千度',
			//	data: [0,2],
			//	stack: '在线简历'
			//},{
			//	name:'千度',
			//	data: [2],
			//	stack: '在线简历2'
			////}]
	});
}

// 来源渠道-圆饼图
function channelPie(values){
	var _chartnav=$('.chart_tab .cur').attr('data-value');
	$('#'+_chartnav).find('.channel_pie').highcharts({
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
