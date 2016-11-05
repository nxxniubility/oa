$(function(){
    //获取页面参数
    var zone_id = $.getUrlParam('zone_id');
    getAjax(zone_id);
    //获取列表内容
    function getAjax(zone_id){
        var data={
            zone_id:zone_id
        };
        //获取职位详情
        common_ajax2(data,'/SystemApi/Zone/getZoneInfo','no',function(redata){
            if(redata.data){
                layui.use('laytpl', function(){
                    var laytpl = layui.laytpl;
                    laytpl(demo_body.innerHTML).render(redata.data, function(result){
                        $('.newMiddle').html(result);
                    });
                });
            };
        },1);
        common_ajax2(data,'/SystemApi/Zone/getParentZoneList','no',_setHtml,1);
        function _setHtml(redata){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(demo.innerHTML).render(redata.data, function(result){
                    $('#choose').html(result);
                var zone_id = $(':input[name="pid"]').val();
                var name = $('#choose').find('.fxDone[data-value="'+zone_id+'"]').text();
                $('#choose').find('.select_title').text(name);
                });
            });
        };
    };

    //提交
    $(document).on('click', '.newAreaSubmit', function() {
        var data = {
            zone_id:zone_id,
            name:$(':input[name="name"]').val(),
            pid:$(':input[name="pid"]').val(),
            address:$(':input[name="address"]').val(),
            tel:$(':input[name="tel"]').val(),
            email:$(':input[name="email"]').val(),
            abstract:$(':input[name="abstract"]').val(),
        };
        common_ajax2(data,'/SystemApi/Zone/editZone',0,function(redata){
            if(redata.code!=0){
                layer.msg(redata.msg,{icon:2});
            }else{
                layer.msg('修改成功',{icon:1});
                window.location.href = "{:U('/System/Zone/zoneList')}";
            };
        });
    });
});