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

//  跳转登录页
$(document).read(function(){
	var jump_code;
	if(jump_code == 501){
		window.location.href = 'http://www.oa.com/system/index';
	}
});
