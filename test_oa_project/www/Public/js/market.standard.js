function delStandard(id){
    layer.confirm('确定要删除该合格标准？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        var data = {
            standard_id:id
        };
        common_ajax2(data, standard_url, 'reload');
    }, function(){});
}