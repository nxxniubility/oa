//  添加号码
$(document).on('click','.add_number, .call_edit', function(){
    if($(this).hasClass('add_number')){
        var _title = '添加号码';
        $(':input[name="number"],:input[name="number_type"],:input[name="temp_id"]').val('');
    }else{
        var _title = '修改号码';
        var _data_id = $(this).parents('tr').attr('data-id');
        var _data_value = $(this).parents('tr').attr('data-value');
        _data_value = _data_value.split('-#-');
        $(':input[name="temp_id"]').val(_data_id);
        $(':input[name="number"]').val(_data_value[0]);
        $(':input[name="number_type"]').find('option[value="'+_data_value[1]+'"]').prop('selected',true);
    }
    layer.open({
        type: 1, 					//  页面层
        title: _title, 		    //	不显示标题栏
        area: ['450px', 'auto'],
        closeBtn: 1,
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: false, 			//	遮罩控制关闭层
        shift: 5, 					//	出现动画-5 闪现
        content: $(".call_addbox"),	    //  加载主体内容
        scrollbar: false
    });
    $('.btn_cancel').on('click',function(){
        layer.closeAll();
    });
});
//提交
$(document).on('click', '.btn_confirm',function(){
    if($(':input[name="temp_id"]').val().length==0){
        var data = {
            number:$(':input[name="number"]').val(),
            number_type:$(':input[name="number_type"]').val()
        };
        common_ajax2(data,'/SystemApi/SystemUser/addCallNumber','reload');
    }else{
        var data = {
            type:'edit',
            number:$(':input[name="number"]').val(),
            number_type:$(':input[name="number_type"]').val(),
            call_number_id:$(':input[name="temp_id"]').val()
        };
        common_ajax2(data,'/SystemApi/SystemUser/editCallNumber','reload');
    };
});
//启动
$(document).on('click', '.call_start',function(){
    var _data_id = $(this).parents('tr').attr('data-id');
    layer.confirm('是否要启动该号码作为网络电话默认接听号码？', {
        btn: ['确定','取消'], //按钮
        title: '提示信息'
    }, function(){
        var data = {
            call_number_id:_data_id,
            number_start:1
        };
        common_ajax2(data,'/SystemApi/SystemUser/startCallNumber','reload');
    }, function(){});
});
$(document).on('click', '.call_stop',function(){
    var _data_id = $(this).parents('tr').attr('data-id');
    layer.confirm('是否要停止启动该号码？', {
        btn: ['确定','取消'], //按钮
        title: '提示信息'
    }, function(){
        var data = {
            call_number_id:_data_id,
            number_start:0
        };
        common_ajax2(data,'/SystemApi/SystemUser/startCallNumber','reload');
    }, function(){});
});
//删除
$(document).on('click', '.call_del', function(){
    var _data_id = $(this).parents('tr').attr('data-id');
    layer.confirm('是否确定要删除该号码？', {
        btn: ['确定','取消'], //按钮
        title: '提示信息'
    }, function(){
        var data = {
            type:'del',
            call_number_id:_data_id
        };
        common_ajax2(data,'/SystemApi/SystemUser/delCallNumber','reload');
    }, function(){});
});