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