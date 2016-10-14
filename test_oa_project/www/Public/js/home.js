$(function () {
    /*添加新/编辑模版*/
    $('.sbTop').find('i').on('click', function () {
        layer.open({
            type: 1, 						//  页面层
            title: false, 					//	不显示标题栏
            area: ['780px', '604px'],
            shade: .6, 						//  遮罩
            time: 0, 						//  关闭自动关闭
            shadeClose: true, 				//	遮罩控制关闭层
            closeBtn: false, 				//	不显示关闭按钮
            shift: 1, 						//	出现动画
            content: $(".sbBox") 			//  加载主体内容
        });
        $('.sbClose, .submitBtn').on('click', function () {
            layer.closeAll(); 				// 关闭
        });
    });

    $(function () {
        //  各种最后一个
        $('.btnBox>div:last-child').css('borderBottom', 'none');
        $('.itemBox li:nth-child(5n)').css('margin', '0');
    });

    //自定义导航选择
    $('.node_btn').click(function () {
        var node_id = $(this).attr('node_id');
        if ($(this).hasClass('on_btn')) {
            $(this).removeClass('on_btn');
            $('.show_node_' + node_id).remove();
        } else {
            if ($('#show_node').children('li').length < 8) {
                $(this).addClass('on_btn');
                var str = "<li class='show_node_" + node_id + "' node_id='" + node_id + "><a href='javascript:;' >" + $(this).text() + "</a></li>";
                $('#show_node').append(str);
            } else {
                layer.msg('最多添加8个自定义导航项', {icon: 2});
            }
        }
    });


    //自定义导航提交
    $('.submitBtn').click(function () {
        var nodes = '';
        for (var i = 0; i < $('#show_node li').length; i++) {
            if (i == 0) {
                nodes += $('#show_node li').eq(i).attr('node_id');
            } else {
                nodes += ',' + $('#show_node li').eq(i).attr('node_id');
            }
        }
        var data = {
            nodes: nodes
        };

        common_ajax(data, '', 'reload');
    });

    //自定义导航重置
    $('.resetBtn').click(function () {
        //删除已经定义的节点
        $('#show_node li').remove();
        $('#child_nodes .node_btn').removeClass('on_btn');
        var str = '';
        $.each(json_arr,function(k,v){
            $('.li_'+v.node_id).addClass('on_btn');
            str += '<li class="show_node_'+v.node_id+'" node_id="'+v.node_id+'"><a href="javascript:;" >'+v.title+'</a></li>';
        });
        $('#show_node').html(str);
    });




});




