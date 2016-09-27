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

});

//生成规则模版
function getLiBody(id, name){
    $('#body_li').children('li').attr('class',id).children('.list_top').children('p').text(name);
    if($('.'+id).length>=1){
        var _body_li = $('#body_li').html();
        $('.standard_list').append(_body_li);
    }
}