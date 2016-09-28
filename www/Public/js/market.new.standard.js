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
        if($('.standard_list').children('li').length==0){
            layer.msg('请添加规则内容',{icon:2});return false;
        }else if(_standard_name.length==0){
            layer.msg('请添加标准名称',{icon:2});return false;
        }else if(_department_id==0){
            layer.msg('请选择部门',{icon:2});return false;
        }
        var data = {
            standard_name:_standard_name,
            department_id:_department_id,
            standard_remark:_standard_remark,
        };
        common_ajax2(data, add_standard_url, 'no');
    });
});

//生成规则模版
function getLiBody(id, name){
    $('#body_li').children('li').attr('class',id).children('.list_top').children('p').text(name);
    if($('.'+id).length>=1){
        var _body_li = $('#body_li').html();
        $('.standard_list').append(_body_li);
    }
}