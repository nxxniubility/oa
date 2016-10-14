//  键盘回车，表单提交：id为login_form
document.onkeydown=function(event){
	var	e = event ? event : (window.event ? window.event : null);
	if(e.keyCode==13){
		 Login();
	}
}
$('.loginSubmit').click(function(){
    Login();
});
var login_fref = '/Api/SystemLogin/login';
function Login() {
    //$('#loginForm').submit();
    layer.load(2);
    var data = {
        username:$(':input[name="username"]').val(),
        password:$(':input[name="password"]').val(),
        verification:$(':input[name="verification"]').val()
    };
    $.ajax({
        url:login_fref,
        dataType:'json',
        type:'post',
        data:data,
        success:function(reflag){
            if(reflag.code && reflag.code!=0){
                if(reflag.data.sign){
                    layer.tips(reflag.msg, $(':input[name="'+reflag.data.sign+'"]'),{time:1000});
                }else{
                    layer.msg(reflag.msg,{icon:2});
                }
                $('#verify_img').trigger('click');
                layer.closeAll('loading');
            }else{
                location.href='/';
            };
        },
        error:function(){
            layer.msg('网络异常,请稍后再试！',{icon:2});
            layer.closeAll('loading');
        }
    });
}

/*
 var c=5;
 var t;
 function timedMsg()
 {
 document.getElementById('showtime').innerHTML=c;
 if(c==0){
 clearTimeout(t);
 //window.location.href="url";//为跳转地址
 }else{
 t=setTimeout('timedMsg()',1000);
 }
 c--;
 }
 */

