$(function(){
	selectbox();
	checknode();
});
//  操作
$(document).on('click', '.rightsSelect',function(event){
	$(this).next().toggle().closest('tr').siblings().find('.otherOperation').hide();
    event.stopPropagation();
});
//  点击页面任意地方隐藏otherOperation
 $(document).click(function() {
	$('.otherOperation').hide();
});

//下拉框
function selectbox() {
    $(document).bind({
        click: function() {
            $(".selectbox dt").parent().find("ul").removeClass("s");
        }
    });
    $(".select").delegate("dt", "click",
        function() {
            $(this).parent().find("dd").toggle();
            $(this).parent().find(".ddoption").toggle();
            selectStatus($(this));
            if ($(this).attr("class") == "on") {
                if ($(this).parent().find("ul").height() > 200) {
                    $(this).parent().find("ul").addClass("s");
                };
            } else {
                $(this).parent().find("ul").removeClass("s");
            }
            return false;
        });

    $(".select").delegate("dd", "click",
        function() {
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_name = $(this).text();
            var data_id = $(this).attr('data_id');
            $(this).parent('.ddoption').toggle();
            $(this).parent("dl").find(".select_title").text(data_name).attr('data_id',data_id);
            $(this).parent().parent().find(".select_title").text(data_name);
            $(this).parents("dl").next().val(data_value);
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select dd").parent("dl").find("dt"));
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
        });
    $(document).click(function() {
        $(".select dd").hide();
        selectStatus($(".select dt"));
    });

}

//当前下拉菜单状态
function selectStatus(obj) {
    if (obj.parent().find("dd").is(":hidden")) {
        otherSelectStatus(); //
        obj.parent().removeClass("zindex4").parent().find(".on").removeClass("on");
        obj.find(".arrow").removeClass("arrow_on");
    } else {
        otherSelectStatus(); //
        obj.parent().find("dd,.select_title2").show();
        obj.addClass("on");
        obj.parent().addClass("on zindex4");
        obj.find(".arrow").addClass("arrow_on");

    }
}
//其他下拉菜单状态
function otherSelectStatus() {
    $("[class^=select]").parent().find(".on").removeClass("on");
    $("[class^=select]").find(".arrow").removeClass("arrow_on");
    $("[class^=select]").find("dd,.select_title2").hide();
    $("[class^=select]").find("dl").removeClass("zindex4");
}

/*添加新/编辑模版*/
$(document).on('click','.selectPermissions', function() {
    layer.open({
        type: 1, //  页面层
        title: false, //    不显示标题栏
        area: ['500px', '500px'],
        shade: .6, //   遮罩
        time: 0, //  关闭自动关闭
        shadeClose: true, //    遮罩控制关闭层
        closeBtn: false, // 不显示关闭按钮
        shift: 1, //    出现动画
        content: $(".competenceBox") //  加载主体内容
    });
    //职位节点
    $('.radio').attr('checked',false);
    var data = {
        role_id:$(this).attr('sid')
    };
    $(':input[name="role_id"]').val($(this).attr('sid'));
    common_ajax2(data,'/SystemApi/Role/getRoleNode','reload',function(reflag){
        if(reflag.code && reflag.code!=0){
            layer.closeAll();
            layer.msg(reflag.msg,{icon:2});
        }else{
            layer.closeAll('loading');
            var on_nodes=reflag.data;
            if(on_nodes!=null){
                on_nodes = on_nodes.split(',');
                if(on_nodes){
                    for(var i=0;i<on_nodes.length;i++){
                        $('.radio-node-'+on_nodes[i]).prop('checked',true);
                    };
                };
            };
        };
    });
    // 关闭
    $('.addPerClose').on('click', function() {
        layer.closeAll();
    });
});

function checknode(obj) {
    var chk = $("input[type='checkbox']");
    var count = chk.length;
    var num = chk.index(obj);
    var level_top = level_bottom = chk.eq(num).attr('level')
    for (var i = num; i >= 0; i--) {
        var le = chk.eq(i).attr('level');
        if (eval(le) < eval(level_top)) {
            chk.eq(i).attr("checked", 'checked');
            var level_top = level_top - 1;
        }
    }
    for (var j = num + 1; j < count; j++) {
        var le = chk.eq(j).attr('level');
        if (chk.eq(num).attr("checked") == 'checked') {
            if (eval(le) > eval(level_bottom)) chk.eq(j).attr("checked", 'checked');
            else if (eval(le) == eval(level_bottom)) break;
        } else {
            if (eval(le) > eval(level_bottom)) chk.eq(j).attr("checked", false);
            else if (eval(le) == eval(level_bottom)) break;
        }
    }
}