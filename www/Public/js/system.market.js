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
	openPosition();
	close_layer();
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
/*function positionChoose(){
	
}*/


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
		_daily_stats = $('#'+_chartnav).find('.daily_stats'),
		_channel = $('#'+_chartnav).find('.daily_channel'),
		_channel_bar = $('#'+_chartnav).find('.channel_bar'),
		_channel_pie = $('#'+_chartnav).find('.channel_pie'),
		_quality = $('#quality'),
		_course = $('#course');
	if(_curVal == '0'){
		_p.show();
		//_daily_stats.hide();
		//_channel.hide();
		_daily_stats.empty();
		_channel_bar.empty();
		_channel_pie.empty();
		_quality.hide();
		_course.hide();
	}else if(_curVal == '1'){
		dailyStats();
		_daily_stats.show();
		_p.hide();
		//_channel.hide();
		_channel_bar.empty();
		_channel_pie.empty();
		_quality.hide();
		_course.hide();
	}else if(_curVal == '2'){
		channelPie();
		channelBar();
		_channel.show();
		_p.hide();
		_daily_stats.empty();
		_quality.hide();
		_course.hide();
		
	}/*else if(curVal == '3'){
		$('#daily_stats2').show();
		$('#daily_stats2').siblings().hide();
	}else if(curVal == '4'){
		$('#daily_stats3').show();
		$('#daily_stats3').siblings().hide();
	}*/
});

//  各图标初始化
dailyStats();	//  新增量每日统计线型
//channelBar();	//  新增量来源渠道柱状
//channelPie();	//  新增量来源渠道饼状

//  新增量
function dailyStats(days,values){
	_chartnav=$('.chart_tab .cur').attr('data-value');
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
		xAxis: {
			categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
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
			name: '新增量',
			data: [7.0, 6.9, 9.5, 14.5, 18.4, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
		}]
	});
}



//  来源渠道-柱状图
function channelBar(){
	_chartnav=$('.chart_tab .cur').attr('data-value');
	$('#'+_chartnav).find('.channel_bar').highcharts({
		chart:{
			className:'channel_bar',
			type:'bar'
		},
		chart: {type: 'column'},
        title: null,
        credits: {enabled:false},
        xAxis: {
            categories: ['线下院校', '在线简历', '招聘网站', '线上推广', '自然网络','朋友/亲戚']
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
                    'Total: ' + this.point.stackTotal;
            }
        },
         plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series: [{
            name: '360',
            data: [5, 3, 4, 7, 2, 12],
            stack: 'male'			//  分组
        }, {
            name: '智联',
            data: [3, 4, 4, 2, 5, 9],
            stack: 'male'
        }, {
            name: '58同城',
            data: [2, 5, 6, 2, 1, 2],
            stack: 'male'
        }, {
            name: '赶集',
            data: [3, 1, 4, 4, 3, 6],
            stack: 'male'
        },{
        	name:'百度',
        	data: [9, 5, 3, 6, 7, 2],
        	stack: 'male'
        },{
        	name:'千度',
        	data: [2, 6, 2, 7, 3, 8],
        	stack: 'male'
        }]
	});
}

// 来源渠道-圆饼图
function channelPie(){
	_chartnav=$('.chart_tab .cur').attr('data-value');
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
	        data: [
	            ['360', 45.0],
	            ['智联', 26.8],
	            ['58同城', 12.8],
	            ['赶集', 8.5],
	            ['百度', 6.2],
	            ['千度', 0.7]
	        ]
	    }]
	});
}
