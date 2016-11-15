$(function(){
    common_ajax2('', '/SystemApi/Task/getTask', 'no', function(redata){
        if(redata.data){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(tp_task.innerHTML).render(redata.data, function(result){
                    $('#body_task').html(result);
                });
            });
        }else{
            layer.msg(redata.msg, {icon:2});
        };
    });
});