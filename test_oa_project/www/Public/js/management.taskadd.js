$(function(){
    //获取任务列表
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
    //获取部门职位列表
    common_ajax2('','/SystemApi/Department/getDepartmentList','no',function(redata){
        if(redata.data.data){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(tp_department.innerHTML).render(redata.data.data, function(result){
                    $('#body_department').html(result);
                });
            });
        };
    },1);
    //提交按钮
    $(document).on('click','#subtn',function(){

    })
});