Date.prototype.format = function(fmt) {
    var o = {
        "M+": this.getMonth() + 1, //月份         
        "d+": this.getDate(), //日         
        "h+": this.getHours() % 12 == 0 ? 12 : this.getHours() % 12, //小时         
        "H+": this.getHours(), //小时         
        "m+": this.getMinutes(), //分         
        "s+": this.getSeconds(), //秒         
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度         
        "S": this.getMilliseconds() //毫秒         
    };
    var week = {
        "0": "/u65e5",
        "1": "/u4e00",
        "2": "/u4e8c",
        "3": "/u4e09",
        "4": "/u56db",
        "5": "/u4e94",
        "6": "/u516d"
    };
    if (/(y+)/.test(fmt)) {
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }
    if (/(E+)/.test(fmt)) {
        fmt = fmt.replace(RegExp.$1, ((RegExp.$1.length > 1) ? (RegExp.$1.length > 2 ? "/u661f/u671f" : "/u5468") : "") + week[this.getDay() + ""]);
    }
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        }
    }
    return fmt;
}


//解决ie9及以下不支持placehoder属性
$(document).ready(function() {
    var doc = document,
        inputs = doc.getElementsByTagName('input'),
        supportPlaceholder = 'placeholder' in doc.createElement('input'),

        placeholder = function(input) {

            var text = input.getAttribute('placeholder'),
                defaultValue = input.defaultValue;

            if (defaultValue == '') {
                input.value = text
            }


            input.onfocus = function() {
                if (input.value === text) {
                    this.value = ''
                }
            };
            input.onblur = function() {
                if (input.value === '') {
                    this.value = text
                }
            }
        };

    if (!supportPlaceholder) {
        for (var i = 0, len = inputs.length; i < len; i++) {

            var input = inputs[i],
                text = input.getAttribute('placeholder');

            if (input.type === 'text' && text) {
                placeholder(input)
            }
        }
    }
});

//  iframe跳转登录页
if(jump_code == 501){
	//window.location.href = '/system/admin/index';
	top.location.href = '/system/admin/index';
}


function common_ajax(data,url,loca,fun){
    layer.load(2);
    if(!url || url.length<1){
        url = window.location.href ;
    };
    $.ajax({
        url:url,
        dataType:'json',
        type:'post',
        data:data,
        success:function(reflag){
            if(reflag.code && reflag.code!=0){
                if(reflag.sign){
                    layer.tips(reflag.msg, $(':input[name="'+reflag.sign+'"]'));
                    $(':input[name="'+reflag.sign+'"]').focus();
                }else{
                    if(reflag.msg){
                        layer.msg(reflag.msg,{icon:2});
                    };
                };
                layer.closeAll('loading');
                return false;
            }else{
                if(reflag.msg){
                    layer.msg(reflag.msg,{icon:1});
                };
                if(loca=='reload'){
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }else if(loca=='no'){
                    setTimeout(function(){
                        layer.closeAll();
                    },1000);
                    if(fun){
                        fun();
                    };
                }else{
                    setTimeout(function(){
                        location.href=reflag.data;
                    },1000);
                };
            };
        },
        error:function(){
            layer.msg('网络异常,请稍后再试！',{icon:2});
            layer.closeAll('loading');
        }
    });
};
function common_ajax2(data,url,loca,fun,showload){
    if(!showload){
        layer.load(2);
    }
    if(!url || url.length<1){
        url = window.location.href ;
    };
    $.ajax({
        url:url,
        dataType:'json',
        type:'post',
        data:data,
        success:function(reflag){
            layer.closeAll('loading');
            if(fun){
                fun(reflag);
                return false;
            };
            if(reflag.code && reflag.code!=0){
                if(reflag.sign){
                    layer.tips(reflag.msg, $(':input[name="'+reflag.sign+'"]'));
                    $(':input[name="'+reflag.sign+'"]').focus();
                }else{
                    if(reflag.msg){
                        layer.msg(reflag.msg,{icon:2});
                    };
                };
            }else{
                if(loca=='reload'){
                    layer.closeAll();
                    setTimeout(function(){
                        //var loca_url = window.location.href;
                        //window.location.href = loca_url+'#main';
                        location.reload();
                    },1000);
                }else if(loca=='no'){
                    if(!fun){
                        setTimeout(function(){
                            layer.closeAll();
                        },1000);
                    };
                }else{
                    setTimeout(function(){
                        window.location.href = reflag.data;
                    },1500);
                };
                if(reflag.msg){
                    layer.msg(reflag.msg,{icon:1});
                };
            };
            return false;
        }
        //,
        //error:function(){
        //    layer.msg('网络异常,请稍后再试！',{icon:2});
        //    layer.closeAll('loading');
        //    return false;
        //}
    });
}