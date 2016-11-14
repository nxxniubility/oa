

$(function(){
    $(':input[name="zone_id"]').val($.getUrlParam('zone_id'));
    $(':input[name="role_id"]').val($.getUrlParam('role_id'));
    $(':input[name="logtime"]').val($.getUrlParam('logtime'));
    $(':input[name="system_user_id"]').val($.getUrlParam('system_user_id'));
    var str = '<div class=\"wait\"><i></i>正在获取统计数据....</div>';
    var loading_start = layer.open({
        type:1
        ,area:['260px','auto']
        ,title:false
        ,shade:.6
        ,time: 0 //不自动关闭
        ,shadeClose:false
        ,shift: 1
        ,closeBtn:false
        ,content:str
    });
    var data = {
        role_id : $.getUrlParam('role_id'),
        system_user_id : $.getUrlParam('system_user_id'),
        logtime : $.getUrlParam('logtime')+time_his
    };
    $('#count_body,#btn_body,#demo_body').hide();
    common_ajax2(data, '/SystemApi/data/getDataMarket', 'no', function(redata){
        layer.close(loading_start);    		//  关闭加载提示
        if(redata.data){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(templets_count.innerHTML).render(redata.data, function(result){
                    $('#count_body').append(result).show();
                });
                laytpl(templets_btn.innerHTML).render(redata.data, function(result){
                    $('#btn_body').append(result).show();
                });
                laytpl(templets_content.innerHTML).render(redata.data, function(result){
                    $('#demo_body').append(result).show();
                });
                $('#stTab1 .sr_time').text('统计时间：'+$.getUrlParam('logtime','@').split('@')[0]+' 至 '+$.getUrlParam('logtime','@').split('@')[1]);
                $('#stTab2 .sr_time').text('统计员工：'+$('#sr_staff .sr_li').length+' 人');
            });
            setTimeout(function(){
                $('.chart_tab').children('li').eq(0).trigger('click');
            },300);
        }else{
            layer.msg(redata.msg, {icon:2});
            $('.main').append(getNullHint());
        };
    },1);
});

$(document).on('change','.select_role',function(){
    $(':input[name="role_id"]').val($(this).val());
});

//获取当前时分秒
var oDate = new Date();
var time_his = ' '+oDate.getHours()+':'+oDate.getMinutes()+':'+oDate.getSeconds();
//  单项指标左侧列表都添加点击事件
$(function(){
    //$('.chart_tab').children('li').eq(0).trigger('click');
});
//  遮罩层-全局
var mask = $('#mask');

//  显示地区选择弹层
$(document).on('click', '.city_title', function(){
    mask.show();
    $(this).parent().find('.seach_city_show').removeClass('dn');
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
    $(document).on('click', '.city_partition a', function(){
        var txt = $.trim($(this).text()),
            cityShow = $(this).closest('.seach_city_show'),
            finalZone = $(this).closest('.search_region');
        finalZone.find('.city_title em').text(txt);
        finalZone.find(':input[name="zone_id"]').val($(this).attr('data-value'));
        cityShow.addClass('dn');
        mask.hide();
    });
};
//  城市赋值
function cityAssignment(){
    $(document).on('click', '.show_city_cont span', function(){
        if($(this).attr('data-value')!=''){
            var coreTxt = $.trim($(this).text()),
                cityShow = $(this).closest('.seach_city_show'),
                finalZone = $(this).closest('.search_region');

            finalZone.find('.city_title em').text(coreTxt);
            cityShow.addClass('dn');
            mask.hide();
            $(':input[name="zone_id"]').val($(this).attr('data-value'));
        };
    });
};

//  地区重置
function areaReset(){
    $(document).on('click', '.reset_btn', function(){
        $(this).closest('.search_region').find('.city_title em').text('请选择区域');
        $(this).closest('.seach_city_show').addClass('dn');
        $(this).next('input[name="zone_id"]').val('');
        mask.hide();
    });
};


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
    };
    //  职位赋值
    positionChoose();
    //  点遮罩关闭
    layerClose();
    //  点取消关闭
    cancelClose();
});

//  展开部门职位
function openPosition(){
    var _this = $('.position_department'),
        _arrow = _this.find('i'),
        _other = _this.parent().find($('.position_destail'));
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
            };
            $(this).toggleClass('bor_bottom');
        }else {
            if($(this).parent().find(_other).css('display') == 'none'){
                $(this).parent().find(_other).slideDown(500).parent().find(_arrow).addClass('up');
                $(this).parent().siblings().find(_other).slideUp(500).parent().find(_arrow).removeClass('up');
            }else {
                $(this).parent().find(_other).slideUp(500).parent().find(_arrow).removeClass('up');
            };
            //$(this).parent().siblings().find(_other).slideUp(500).parent().find(_arrow).removeClass('up');
            $(this).parents('.position_list').find('li').eq(maxLength-1).find(_this).removeClass('bor_bottom');
        };
    });
};
//  职位赋值
function positionChoose(){
    $(document).on('click', '.confirm', function(){
        var _this = $('.position_list'),
            _checkbox = $(':input[name="sale_inp"]:checked'),
            all_checkfalse = $('#all_select').is(':checked'),
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
            };
        });
        $(this).next('input').val(_role_ids);

        //  假如全选，则限制显示前十三个字
        if(all_checkfalse){
            if(_role_names.length>13){
                _role_names = _role_names.substring(0,13)+'...';
            };
            $('.position_name em').text(_role_names);
        }else {		//  假如没全选，有两种情况

            //  全选按钮没全选，但职位则有选择
            if (!all_checkfalse && _checkbox[0] ){
                if(_role_names.length>13){
                    _role_names = _role_names.substring(0,13)+'...';
                };
                $('.position_name em').text(_role_names);
            }else {		//  全选按钮和职位都没有选
                $('.position_name em').text('请选择职位');
            };
        };


        //$('.position_name em').text(_role_names);
        position_close.closest('.search_position_show').addClass('dn');
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

//  子项控制全选按钮：全选与不全选
$(document).ready(function(){
    $('input[name="sale_inp"]').on('click', function(){
        allChk();
    });
});
//  各职位子项与职位总全选按钮关联
function allChk(){
    var chknum = $('input[name="sale_inp"]').length;//选项总个数 
    var chk = 0;
    $('input[name="sale_inp"]').each(function () {
        if($(this).is(':checked')){
            chk++;
        };
    });
    if(chknum==chk){//全选 
        $("#all_select").prop("checked",true);
    }else{//不全选 
        $("#all_select").prop("checked",false);
    };
};

//  关闭职位弹层
function cancelClose(){
    var _close = $('.cancel');
    _close.click(function(){
        _close.closest('.search_position_show').addClass('dn');
        mask.hide();
    });
};
//  点遮罩关闭地区\职位layer
function layerClose(){
    $(document).on('click', '#mask', function(){
        $('.seach_city_show, .search_position_show').addClass('dn');
        mask.hide();
    });
};

//  切换
var curIndex=0;	//  初始化

//  地区选择城市切换
$(document).on('click', '.city_largearea li', function(){
    var index=$(this).index();
    if(index!=curIndex){
        $(".city_largearea li").siblings().removeClass("cur").eq(index).addClass("cur");
        $(".show_city_cont").removeClass("active").eq(index).addClass("active");
        curIndex=index; //  当前下标赋予变量
    };
});

//  每日数据与员工数据
function doTabClick(o,parm) {
    o.className = "current";		// 当前被中的对象设置css
    var j;
    var id;
    var sbId;
    for (var i = 1; i <= 2; i++) {	// i等于几，就表示有几个切换
        id = "tab" + i;
        sbId='#'+'stTab'+i;
        j = document.getElementById(id);

        if (id != o.id && j != null) {
            j.className = "";
            $(sbId).css('display', 'none');
        } else {
            $(sbId).css('display', 'block');
        };
    };
};

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
    };
    //}
});

//  选择指标select切换
$(document).on('change','.chart_topright select',function(){
    var _curVal = $(this).children('option:selected').val(),
        _chartnav=$('.chart_tab .cur').attr('data-value'),
        _chartname=$('.chart_tab .cur').text(),
        _please_select = $('#'+_chartnav).find('.please_select'),
        _daily_stats = $('#'+_chartnav).find('.daily_stats'),
        _box2 = $('#'+_chartnav).find('.box2'),
        _bar2 = $('#'+_chartnav).find('.bar2'),
        _pie2 = $('#'+_chartnav).find('.pie2'),
        _box3 = $('#'+_chartnav).find('.box3'),
        _bar3 = $('#'+_chartnav).find('.bar3'),
        _pie3 = $('#'+_chartnav).find('.pie3'),
        _box4 = $('#'+_chartnav).find('.box4'),
        _bar4 = $('#'+_chartnav).find('.bar4'),
        _pie4 = $('#'+_chartnav).find('.pie4'),
        _quality = $('#quality'),
        _course = $('#course');
    _please_select.hide();
    //获取接口
    var data = {
        type : _chartnav.replace('btn_',''),
        role_id : $.getUrlParam('role_id'),
        system_user_id : $.getUrlParam('system_user_id'),
        logtime : $.getUrlParam('logtime')+time_his,
    };
    if(_chartnav == 'totalratio' || _chartnav == 'chargebackratio' || _chartnav == 'conversionratio' || _chartnav == 'visitratio'){
        var navnum = {
            'totalratio':16,
            'chargebackratio':15,
            'conversionratio':14,
            'visitratio':13,
        };
        if($('#sr_everyday').find('tr').length>2){
            //每日统计
            var _days = [];
            var _dayVal = [];
            $('#sr_everyday').find('tr').each(function(i){
                if(i>1){
                    _days.push($(this).children('td').eq(0).text());
                    _dayVal.push(parseInt($(this).children('td').eq(navnum[_chartnav]).text()));
                }
            });
            dailyStats(_days,_dayVal,_chartname);
            _quality.hide();
            _course.hide();
            _box2.hide();
            _bar2.empty();
            _pie2.empty();
            _box3.hide();
            _bar3.empty();
            _pie3.empty();
            _box4.hide();
            _bar4.empty();
            _pie4.empty();
        }else{
            _please_select.show();
        };
        return false;
    };
    common_ajax2(data,'/SystemApi/Data/getDataMarketInfo','no',function(redata){
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
                _box2.hide();
                _bar2.empty();
                _pie2.empty();
                _box3.hide();
                _bar3.empty();
                _pie3.empty();
                _box4.hide();
                _bar4.empty();
                _pie4.empty();
            }else if(_curVal == '2'){
                var _navName = [];
                var _values = [];
                var _data_pie = [];
                if(redata.data.channel.list.length<1){
                    //layer.msg('暂无该项数据',{icon:5});
                    _please_select.show();
                }else{
                    $.each(redata.data.channel.list,function(k,v){
                        var _data = [];
                        //  为ie低版本增加.indexOf()放大
                        if (!Array.prototype.indexOf){
                            Array.prototype.indexOf = function(elt /*, from*/){
                                var len = this.length >>> 0;

                                var from = Number(arguments[1]) || 0;
                                from = (from < 0)
                                    ? Math.ceil(from)
                                    : Math.floor(from);
                                if (from < 0)
                                    from += len;

                                for (; from < len; from++){
                                    if (from in this && this[from] === elt)
                                        return from;
                                }
                                return -1;
                            };
                        }	// end
                        if(_navName.indexOf(v.pname)<0){		//  判断数组内是否含有pname
                            _navName.push(v.pname);
                        };
                        for(var i=1;i<_navName.length;i++){
                            _data.push(null);
                        }
                        _data.push(parseInt(v.count));
                        _data = {
                            name: v.name,
                            data: _data
                        };
                        _values.push(_data);
                    });
                    $.each(redata.data.channel.broad,function(k,v){
                        _data_pie.push([k, parseInt(v)]);
                    });
                    console.log(_values);
                    channelBar(_navName,_values,_curVal);
                    channelPie(_data_pie,_curVal);
                };
                _box2.show();
                _quality.hide();
                _course.hide();
                _daily_stats.empty();
                _box3.hide();
                _bar3.empty();
                _pie3.empty();
                _box4.hide();
                _bar4.empty();
                _pie4.empty();
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
                        _data.push(parseInt(v));
                        _data = {
                            name: k,
                            data: _data
                        };
                        _navName.push(k);
                        _values.push(_data);
                        _data_pie.push([k, parseInt(v)]);
                    });
                    channelBar(_navName,_values,_curVal);
                    channelPie(_data_pie,_curVal);
                };
                _box3.show();
                _quality.hide();
                _course.hide();
                _daily_stats.empty();
                _box2.hide();
                _bar2.empty();
                _pie2.empty();
                _box4.hide();
                _bar4.empty();
                _pie4.empty();
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
                        _data.push(parseInt(v));
                        _data = {
                            name: k,
                            data: _data
                        };
                        _navName.push(k);
                        _values.push(_data);
                        _data_pie.push([k, parseInt(v)]);
                    });
                    channelBar(_navName,_values,_curVal);
                    channelPie(_data_pie,_curVal);
                };
                _box4.show();
                _quality.hide();
                _course.hide();
                _daily_stats.empty();
                _box2.hide();
                _bar2.empty();
                _pie2.empty();
                _box3.hide();
                _bar3.empty();
                _pie3.empty();
            };
        };
    },1);
});

//  各图标初始化
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
    var _chartnav=$('.chart_tab .cur').attr('data-value'),
        faWidth = $('.chart_main').width(),
        columnWidth = faWidth*0.55;

    $('#'+_chartnav).find('.bar'+num).highcharts({
        chart: {
            width:columnWidth,
            style:{
                zIndex:1000
            },
            className:'channel_bar',
            type: 'column'
        },
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
        legend: {
            enabled: false
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.x + ':'+this.point.stackTotal+' </b><br/>' +
                    '<span style="color:'+this.series.color+'">'+this.series.name+': ' + this.y + '</span>';
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal'
            }
        },
        series:values
    });
}

// 来源渠道-圆饼图
function channelPie(values,num){
    var _chartnav=$('.chart_tab .cur').attr('data-value'),
        faWidth = $('.chart_main').width(),
        pieWidth = faWidth*0.42;
    $('#'+_chartnav).find('.pie'+num).highcharts({
        chart: {
            width:pieWidth,
            className:'channel_pie',
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: '所占百分比',
            verticalAlign: 'bottom'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
        },
        credits: {enabled:false},
        exporting: { enabled:false},
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.2f} %'
                }
            }
        },
        series: [{
            type: 'pie',
            name: '所占百分比为：',
            data: values

        }]
    });
};

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
};

//  统计报表超出当前分辨率宽度则出现滚动条
$(document).ready(function(){
    var tab = $('.tab-container');
    wrap_width = $('.wrapper_box').width();

    tab.width(wrap_width);
});
//排序
$(document).on('click','.on_btn',function(){
    var _num = $(this).parents('th').index();
    $(this).parents('th').siblings('th').find('.on_btn').removeClass('sr_down').removeClass('sr_up');
    if($(this).hasClass('sr_down')){
        $(this).removeClass('sr_down').addClass('sr_up');
        //排序
        var sortTitle = $(this).parents('tr').siblings('.sr_li').sort(function(a, b) {
            return parseInt($(b).children('td').eq(_num).text().replace(/\-/g,'')) - parseInt($(a).children('td').eq(_num).text().replace(/\-/g,''));
        });
    }else{
        $(this).addClass('sr_down').removeClass('sr_up');
        //排序
        var sortTitle = $(this).parents('tr').siblings('.sr_li').sort(function(a, b) {
            return parseInt($(a).children('td').eq(_num).text().replace(/\-/g,'')) - parseInt($(b).children('td').eq(_num).text().replace(/\-/g,''));
        });
    }
    $(this).parents('tr').siblings('.sr_li').remove();
    $(this).parents('table').append(sortTitle);
});

//  统计报表
$(document).ready(function(){
    //  表头
    $('#sr_everyday th').each(function(k,v){
        var _index = $(this).index();

        if(_index == 0){
            $(this).addClass('one-item');
        }
        if(_index == 6){
            $(this).addClass('one-item');
        }
        if(_index == 8){
            $(this).addClass('one-item');
        }
        if(_index == 9){
            $(this).addClass('one-item');
        }
    });
});



// 开始时间
$(document).ready(function(){
    var _daytime = $.getUrlParam('logtime','@').split('@'),
        my_date = new Date();
    setTimeout(function(){
        $(".startime").val(_daytime[0]).glDatePicker({
            selectableDateRange: [
                {
                    from: new Date(1990, 1, 1) ,
                    to: new Date(my_date.getFullYear(), my_date.getMonth(), my_date.getDate())
                }
            ],
            onClick:function(el, cell, date, data) {
                el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
                var start_time = el.val();
                var end_time = $('.endtime').val();
                $(':input[name="logtime"]').val(start_time+'@'+end_time);
            }
        });
    },500);
    // 结束时间
    var _daytime = $.getUrlParam('logtime','@').split('@'),
        my_date = new Date();
    setTimeout(function(){
        $(".endtime").val(_daytime[1]).glDatePicker({
            selectableDateRange: [
                {
                    from: new Date(1990, 1, 1) ,
                    to: new Date(my_date.getFullYear(), my_date.getMonth(), my_date.getDate())
                }
            ],
            onClick:function(el, cell, date, data) {
                el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
                var start_time = $('.startime').val();
                var end_time = el.val();
                $(':input[name="logtime"]').val(start_time+'@'+end_time);
            }
        });
    },500);
});

