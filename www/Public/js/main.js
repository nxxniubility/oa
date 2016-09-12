// JavaScript Document
$(function() {
    return_btn();
    selectbox();
    tipsbox();
    setdiv();
    setDivReturn();
    showInput();
    check();
    single_check();
    linkProvince();
    get_attr_all();
    sortrank();
    $(".table_content td.hover").hover(function() {
        $(this).find(".hoverdiv").fadeIn(200);
    },
    function() {
        $(this).find(".hoverdiv").fadeOut(100);
    });

    batch_all();
    
});

//省市联动
function linkProvince() {
    linkCity();
    $(".selectbox.province dd").bind({
        click: function() {
            $(".selectbox.city dl dd").remove();
            $(".selectbox.city .select_title").text("城市");
            $(".selectbox.area dl dd").remove();
            $(".selectbox dt").parent().find("ul").removeClass("s");
            $.getJSON("/System/Information/getAreaList/reid/" + $(this).attr("data-value"),
            function(json) {
                $.each(json.data,
                function(index, value) {
                    $(".selectbox.city ul").append('<dd style="display: none;" data-value=' + json.data[index].id + '>' + json.data[index].name + '</dd>');
                });
                linkCity();
            });
        }
    });
}
//市联动
function linkCity() {
    $(".city dd").on("click",function(){
        $(".selectbox .area dl dd").remove();
        $(".selectbox.area .select_title").text("区/县");
        $(".selectbox dt").parent().find("ul").removeClass("s");
        $.getJSON("/System/Information/getAreaList/reid/" + $(this).attr("data-value"),
        function(json) {
            $.each(json.data,
            function(index, value) {
                $(".selectbox.area ul").append('<dd style="display: none;" data-value=' + json.data[index].id + '>' + json.data[index].name + '</dd>');
            })
        });
    });
}
/**
 * 获取城市
 */
function get_city(o) {
    var province_id = $(o).attr('data-value');
    var container = $('.citylist');
    container.parent().find('.select_title').text('城市');
    $('.arealist').text('').parent().find('.select_title').text('区/县');
    $.getJSON("/System/Information/getAreaList/reid/" + province_id,
    function(json) {
        $.each(json.data,
        function(index, value) {
            container.append('<dd style="display: none;" data-value=' + json.data[index].id + ' callback="get_area">' + json.data[index].name + '</dd>');
        })
    });
}
/**
 * 获取区/县
 */
function  get_area(o) {
    var city_id = $(o).attr('data-value');
    var container = $('.arealist');
    container.parent().find('.select_title').text('区/县');
    $.getJSON("/System/Information/getAreaList/reid/" + city_id,
    function(json) {
        $.each(json.data,
        function(index, value) {
            container.append('<dd style="display: none;" data-value=' + json.data[index].id + '>' + json.data[index].name + '</dd>');
        })
    });
}

//显示隐藏双日期框
function showDateTwo() {
    $(".select dd").click(function() {
        if ($(this).text().indexOf('日期') != -1) {
            $(".inputtext.two,span.two").show();
            $(".inputtext.one").hide();
        } else {
            $(".inputtext.two,span.two").hide();
            $(".inputtext.one").show();
        };
    });
}

/**
 * 咨询缴费申请的时候，选择所属班级
 */
function showClassOption(obj) {
    $("#change_class").parent().parent().show();
   var course_id = $(obj).attr('data-value');
    $("#change_class").attr('data-url','/System/Information/getClassOption/course_id/'+course_id+'.html');
}
//返回键功能
function return_btn() {
    $('.return').click(function(){
        history.go(-1);
    });
}

//下拉框
function selectbox() {
    showDateTwo();
    $(document).bind({
        click: function() {
            $(".selectbox dt").parent().find("ul").removeClass("s");
        }
    });
    $(".select").delegate("dt", "click",
    function() {
        $(this).parent().find("dd").toggle();
        $(this).parent().find(".ddoption").toggle();
        selectStatus($(this));
        if ($(this).attr("class") == "on") {
            if ($(this).parent().find("ul").height() > 200) {
                $(this).parent().find("ul").addClass("s");
            };
        } else {
            $(this).parent().find("ul").removeClass("s");
        }
        return false;
    });

    $(".select").delegate("dd", "click",
    function() {
        var url = $(this).attr("data-url");
        if (url != undefined) window.location.href = url;
        var data_value = $(this).attr('data-value');
        var data_name = $(this).text();
        $(this).parent('.ddoption').toggle();
        $(this).parent("dl").find(".select_title").text(data_name);
        $(this).parent().parent().find(".select_title").text(data_name);
        $(this).parents("dl").next().val(data_value);
        $(this).parents("dl").find("dd").toggle();
        selectStatus($(".select dd").parent("dl").find("dt"));
        //return false;
        var callback = $(this).attr('callback');
        if(callback) eval(callback+'(this)');
    });
    $(document).click(function() {
        $(".select dd").hide();
        selectStatus($(".select dt"));
    });

}
//下拉菜单状态
function selectStatus(obj) {
    if (obj.parent().find("dd").is(":hidden")) {
        obj.removeClass("on");
        obj.find(".arrow").removeClass("arrow_on");
    } else {
        obj.addClass("on");
        obj.find(".arrow").addClass("arrow_on");
    }
}

//提示框
function tipsbox() {
    $(".tips").click(function() {
        var msg = $(this).attr("data-msg");
        var url = $(this).attr("data-url");
        if (!confirm(msg)) {
            return false;
        } else {
            window.location.href = url;
        }
    });
}

//弹出层
function setdiv() {
    $(".setdiv").click(function() {
        window.top.art.dialog.open($(this).attr("data-url"), {
            title: $(this).attr("data-name"),
            width: parseInt($(this).attr("data-width")),
            height: parseInt($(this).attr("data-height")),
            left: $(this).attr("date-left")
        });

    });
}

function setDivReturn() {
    $(".setdivreturn").click(function() {
        var origin = artDialog.open.origin;
        var data_value = $(this).attr("data-value");
        var data_text = $(this).attr("data-text");
        var data_id = $(this).attr("data-id");
        var data_btnid = $(this).attr("data-btnid");
        //alert(data_btnid);
        //alert(origin.document.title);
        var btn = origin.document.getElementById("main").contentDocument.getElementById(data_btnid);
        var noreturn = btn.getAttribute("data-noreturn");
        if (noreturn == 1) {
            var url = window.location.host;
            url = 'http://' + url + '/System/Information/allot.html';
            data_btn_value = btn.getAttribute("data-btn-value");
            if(data_btn_value == null) data_btn_value = '';
            $(origin.document.getElementById("main").contentDocument).find('.check_input').each(function(){
                var obj = $(this);
                if(obj.prop('checked')) data_btn_value += ',' + obj.attr('value');
            });
            if(data_btn_value == '') {
                alert('请勾选待分配数据');
                return;
            }
            $.post(url, {
                user_id: data_btn_value,
                allot_id: data_value
            },
            function(rs) {
                if (rs.code != 0) {
                    alert(rs.msg);
                    return false;
                } else if (rs.code == 0) {
                    alert(rs.msg);
                    setTimeout("art.dialog.close()", 100);
                    var href = origin.document.getElementById("main").contentDocument.location.href;
                    origin.document.getElementById("main").contentDocument.location.href = href;
                }
            });
        } else {
            btn.setAttribute("value", data_text);
            var returndata = origin.document.getElementById("main").contentDocument.getElementById(data_id);
            returndata.setAttribute("value", data_value);
            setTimeout("art.dialog.close()", 100);
            return false;
        }

    });
}
/**
 * 选择优惠规则
 */
function setDiscount() {
    var cbx;
    var html = '';
    var total_money = 0;
    $('.small_table').find("input[type='checkbox']").each(function(){
        cbx = $(this);
        if(cbx.prop('checked') == false) return;
        var discount_id = cbx.attr('value');
        var dname = cbx.parent().next().text();
        var dmoney = cbx.parent().next().next().text();
        total_money += parseInt(cbx.attr('dmoney'));
        html +='<p>';
        html +='    <input type="hidden" value="'+discount_id+'" name="discount_id[]"/>';
        html +='    '+dname+'：'+dmoney;
        html +='</p>';
    });
    
    $('.small_table').find("input[type='radio']").each(function(){
        cbx = $(this);
        if(cbx.prop('checked') == false) return;
        var discount_id = cbx.attr('value');
        var dname = cbx.parent().next().text();
        var dmoney = cbx.parent().next().next().text();
        total_money += parseInt(cbx.attr('dmoney'));
        html +='<p>';
        html +='    <input type="hidden" value="'+discount_id+'" name="discount_id[]"/>';
        html +='    '+dname+'：'+dmoney;
        html +='</p>';
    });
    
    // alert(window.parent.document.title);
    // alert(document.title);
    if(html) {
        html += '<p>合计优惠总额：￥'+total_money+'</p>';
        $(window.parent.document.getElementById('discount_why')).show();
    }else{
        $(window.parent.document.getElementById('discount_why')).hide();
    }
    $(window.parent.document.getElementById("main").contentDocument).find("#discount_con").html(html);
    setTimeout("art.dialog.close()", 100);
}

//日期
function datebox() {
    $(".datepicker").datepicker({
        dateFormat: 'yy-mm-dd'
    });
}
//日期
function laydatebox(params) {
    if(typeof params == 'undefined') params = {istime: true, format: 'YYYY-MM-DD hh:mm:ss'};
    params.istime = true;
    $(".datepicker").each(function(o){
        $(this).on('click',function(){
            laydate(params);
        });
    });
}
//显示隐藏相应选项
function showInput() {
    $(".openshow dd").click(function() {
        var data_click = $(this).attr("data-click");
        if (data_click != undefined) {
            var data_obj = $(this).attr("data-obj");
            if (data_obj != undefined) {
                var data_objname = '.' + data_obj;
                $(data_objname).show();
				$(".showinput").hide();
            } else {
                $(".showinput").show();
				var data_obj = $("[data-obj]");
				$.each(data_obj,function(){
				var obj = $(this).attr("data-obj");
				var data_objname = '.' + obj;
				 $(data_objname).hide();
				});
            }

        } else {
            var data_obj = $("[data-obj]");
			$.each(data_obj,function(){
				var obj = $(this).attr("data-obj");
				var data_objname = '.' + obj;
				 $(data_objname).hide();
			});
			$(".showinput").hide();

        }
    });
}
/**
 * 缴费填写银行卡号
 */
function showPaySureDiv(fee_id) {
    $("#PaySureDiv").find('form').append('<input type="hidden" value="'+fee_id+'" name="fee_id"/>');
    art.dialog({
        title:'请填写还款金额',
        width: 800,
        height: 200,
        content: document.getElementById('PaySureDiv'),
        ok: function () {
            //this.title('3秒后自动关闭').time(3);
            $("#PaySureDiv").find('form').submit();
            return false;
        },
        cancelVal: '关闭',
        cancel: true //为true等价于function(){}
    });
}
//咨询添加来量时用到该函数
function showChannelMarker(obj){
    var marker_id = $(obj).attr('data-value');
    $('.marker_msg').hide();
    $('.marker_id_'+marker_id).show();
}
//模板管理时候用到该函数
function pageNavList(json) {
    //console.log(json);
    var html = '';
    for(var i in json) {
        html +='<p>';
        html +='    '+json[i].pagename+'';
        html +='    <input type="text" placeholder="请填写导航名称" class="text" style="width:150px;margin:15px;" value="'+json[i].nav_name+'" name="nav_name[]">';
        html +='    <input type="text" placeholder="排序" class="text" style="width:25px;margin:15px;" value="'+json[i].sort+'" name="sort[]">';
        html += '<input type="hidden" name="pages_nav_id[]" value="'+json[i].pages_nav_id+'"/>';
        html +='    <a href="javascript:void(0);" onclick="$(this).parent().remove();">删除</a>';
        html +='</p>';
    }
    return html;
}
//单选
function single_check() {
    $('.single_check').click(function(event){
        var obj = $(this).find("input[type='checkbox']");
        obj.prop('checked',!obj.prop('checked'));
        event.preventDefault(); //阻止捕获
        
    });
    $('.single_check').find("input[type='checkbox']").click(function(event){
        event.stopPropagation();  //阻止冒泡
    });
}
//全选
function check() {
    $('.check_btn').click(function(){
        $('.check_input').prop('checked',$(this).prop('checked'));
    });
}
//获取选中的 checkbox
function get_check_input(is_all) {
    if(typeof is_all == 'undefined') is_all = 0;
    var value = '';
    var array = new Array();
    $(".check_input").each(function(){
        var obj = $(this);
        if(obj.prop('checked')) {
            if(is_all) {
                var tmp_array = new Array();
                for(var i=0;i<this.attributes.length;i++) {
                    tmp_array[this.attributes[i].nodeName] = this.attributes[i].nodeValue;
                }
                array.push(tmp_array);
                //console.log(tmp_array);
            }else{
                if(value == '') {
                    value += obj.attr('value');
                }else{
                    value += ','+obj.attr('value');
                }
            }
        }
    });
    
    if(is_all) return array;
    return value;
}

function confirmurl(url,tips) {
    if(confirm(tips)){
        window.location.href=url; 
    }
}
/**
 * 判断是否滚动到底部
 * @author Echo 
 */
function reachBottom(){
    var d_scrollTop = document.documentElement.scrollTop; //文档滚动后隐藏起来的高度
    var d_clientHeight = document.documentElement.clientHeight; //文档可视区域高度
    var d_scrollHeight = document.documentElement.scrollHeight; //文档总高度
    var b_scrollTop = document.body.scrollTop;//body滚动后隐藏起来的高度
    var b_clientHeight = document.body.clientHeight; //body可视区域高度
    var b_scrollHeight = document.body.scrollHeight; //body总高度 
    if(d_clientHeight == d_scrollHeight) {
        return b_scrollTop + b_clientHeight >= d_scrollHeight;
    }
    if(d_scrollTop == 0 && d_scrollHeight == b_scrollHeight) {
        return d_clientHeight + b_scrollTop >= b_scrollHeight;
    }
    return d_scrollTop + d_clientHeight >= d_scrollHeight;
}
/**
 * 批量提交user_id
 */
function batch_all() {
    $('.batch_all').on('click',function(){
        var url  = $(this).attr('data-url');
        var msg = $(this).attr('data-msg');
        if(!msg) msg = '您确定要继续吗';
        var value = get_check_input();
        if(!value) {
            alert('请勾选按钮');
            return;
        }
        if(confirm(msg)){
            window.location.href = url+value+'/0.html';
        }
    });
}
/**
 * 获取表单数据
 */
function get_form(id) {
    var result = {};
    var i = 0;
    var k,val;
    $(id).find('input').each(function (index) {
        var type = this.type;
        if(type == 'text' || type == 'hidden' || type == 'password') {
            if(this.id == '') {
                k = this.name;
            }else{
                k = this.id;
            }
            if(k == '') return;
            result[k] = this.value; 
        }else if(type == 'radio') {
            if ($(this).prop('checked')) {
               result[this.name] = $("input[name='"+this.name+"']:checked").val();
            }
        }else if(type == 'checkbox') {
            if ($(this).prop('checked')) {
                k = this.id == '' ? i : this.id;
                i++;
                var k2 = this.name;
                if(k2.indexOf('[') !== -1) k2 = k2.substr(0,k2.indexOf('['));
                if(typeof(result[k2]) == 'undefined') result[k2] = {};
                result[k2][k] = $(this).val();
            }
        }
    });
    $(id).find('select').each(function () {
        if(this.id == '') {
            k = this.name;
        }else{
            k = this.id;
        }
        if(k) {
            result[k] = $(this).find("option:selected").val();
        }
    });
    $(id).find('textarea').each(function () {
         if(this.id == '') {
            k = this.name;
        }else{
            k = this.id;
        }
        if(k) {
            if(k.indexOf('umeditor_textarea_') != -1) { //兼容百度编辑器
                k = k.replace('umeditor_textarea_','');
            }
            result[k] = $(this).val(); //中文需要 encodeURIComponent
        }
    });
    return result;
    //console.log(result);
}
/**
 * 初始化表单数据
 */
function init_form(id,json) {
    //console.log(json);
    var obj = $(id);
    if(arguments[1]) {
        for(var i in json) {
            obj.find('#'+i).val(json[i]);
        }
        return;
    }
    obj.find('input').val('');
    obj.find('textarea').val('');
}
//返回弹出层中被选中的元素的所有属性
function get_attr_all() {
    $(".get_attr_all").on('click',function(){
        var is_all = window.art.dialog.data('is_all');
        if(typeof is_all == 'undefined') is_all = 1;
        var data = get_check_input(is_all);
        var callback = window.art.dialog.data('callback');
        callback(data);
        window.art.dialog.close();
    });
}
//排序
function sortrank() {
    $(".sortrank").blur(function(){
        var url = $(this).attr('data-url');
        var data = {};
        data['sortrank'] = $(this).attr('value');
        data['id'] = $(this).attr('data-id');
        $.post(url,data,function(json) {
            if(json.code == 0) {
                window.location.reload(window.location.href);
            }else{
                alert(json.msg);
            }
        });
    });
}
/**
 * 控制重复提交
 */
function submit_control(o,is_unlock) {
    var is_unlock = is_unlock || 0;
    var o = $(o);
    var classname = 'lock-btn';
    if(o.hasClass(classname)) {
        if(is_unlock) o.removeClass(classname);
        return true;
    }
    o.addClass(classname);
    return false;
}
