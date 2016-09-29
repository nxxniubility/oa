$(function(){
    $(document).on('click','.top_close',function(){
        var _id = $(this).parent().parent().attr('class');
        $(this).parent().parent().remove();
        $('.individual li[data-value="'+_id+'"]').removeClass('cur');
    });
    $('.individual li').click(function(){
        var _id = $(this).attr('data-value');
        var _name = $(this).text();
        if($(this).hasClass('cur')){
            $(this).removeClass('cur');
            $('.standard_list').children('.'+_id).remove();
        }else{
            $(this).addClass('cur');
            getLiBody(_id, _name);
        };
    });
    //提交 添加数据
    $('.list_confirm').click(function(){
        var _standard_name = $(':input[name="standard_name"]').val();
        var _department_id = $(':input[name="department_id"]').val();
        var _standard_remark = $(':input[name="standard_remark"]').val();
        var _option_objs = [],
            _option_name = '',
            _option_num = '',
            _option_warn = '';
        if($('.standard_list').children('li').length==0){
            layer.msg('请添加规则内容',{icon:2});return false;
        }else if(_standard_name.length==0){
            layer.msg('请添加标准名称',{icon:2});return false;
        }else if(_department_id==0){
            layer.msg('请选择部门',{icon:2});return false;
        };
        $('.standard_list li').each(function(){
            _option_name = $(this).attr('class');
            _option_num = $(this).find(':input[name="option_num"]').val();
            _option_warn = $(this).find(':input[name="than_'+_option_name+'"]:checked').val();
            if(_option_num.length>6 || _option_num.length==0){
                layer.msg('标准数值不能为空且不能大于6为数字',{icon:2});
                _option_objs = '';return false;
            };
            _option_objs.push({
                'option_name':_option_name,
                'option_num':_option_num,
                'option_warn':_option_warn,
            });
        });
        if(_option_objs==''){return false;}
        //对象转换json
        _option_objs = JSON.stringify( _option_objs );
        var data = {
            standard_name:_standard_name,
            department_id:_department_id,
            standard_remark:_standard_remark,
            option_objs:_option_objs,
            standard_id:data_standard_id
        };
        common_ajax2(data, add_standard_url, 'no', _reData);
        function _reData(redata){
            if(redata.code==0){
                layer.msg(redata.msg, {icon:1});
                location.href=href_standard;
            }else{
                layer.msg(redata.msg, {icon:2});
            };
        };
    });
});

//生成规则模版
function getLiBody(id, name){
    $('#body_li').children('li').attr('class',id).children('.list_top').children('p').text(name);
    if($('.'+id).length>=1){
        var _body_li = $('#body_li').html();
        $('.standard_list').append(_body_li);
        $('.standard_list .'+id).find('label').eq(0).attr('for', 'less_than_'+id).children('input').attr('id', 'less_than_'+id).attr('name', 'than_'+id);
        $('.standard_list .'+id).find('label').eq(1).attr('for', 'higher_than_'+id).children('input').attr('id', 'higher_than_'+id).attr('name', 'than_'+id);
    };
};