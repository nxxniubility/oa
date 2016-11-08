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
        };
    };
    for (var j = num + 1; j < count; j++) {
        var le = chk.eq(j).attr('level');
        if (chk.eq(num).attr("checked") == 'checked') {
            if (eval(le) > eval(level_bottom)) chk.eq(j).attr("checked", 'checked');
            else if (eval(le) == eval(level_bottom)) break;
        } else {
            if (eval(le) > eval(level_bottom)) chk.eq(j).attr("checked", false);
            else if (eval(le) == eval(level_bottom)) break;
        };
    };
};

$(function(){
    //获取页面参数
    var thisObj = $('#get_body_list');
    var page = $.getUrlParam('page',1);
    thisObj.html(getLoding());
    getAjax(page);
    //获取列表内容
    function getAjax(page){
        var order = $.getUrlParam('order');
        var data = {
            order:order,
            page: ((page-1)*15)+',15'
        };
        common_ajax2(data,'/SystemApi/Role/getRoleList','no',function(redata){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(demo.innerHTML).render(redata.data.data, function(result){
                    thisObj.html(result);
                });
            });
            //分页
            if(redata.data.count>0){
                layui.use(['laypage', 'layer'], function(){
                    var laypage = layui.laypage,layer = layui.layer;
                    laypage({
                        cont:  $('.paging')
                        ,pages:  Math.ceil(redata.data.count/15) //总页数
                        ,groups: 5 //连续显示分页数
                        ,first:'首页'
                        ,last:'尾页'
                        ,skin: '#4dbe88'
                        ,curr: page
                        ,jump: function(obj, first){
                            //得到了当前页，用于向服务端请求对应数据
                            var curr = obj.curr;
                            if(!first){
                                location.href=$.getUrl('page',curr);
                            }
                        }
                    });
                });
            };
        },1);
    };
    $(document).on('click','.on_sort',function(){
        location.href='?order='+$(this).attr('data-value');
    });
    //树配置
    $("#boxTabel").treeTable({
        expandable: true,
    });
    //修改排序值
    $('#sort').click(function(){
        var  ids_sort="";
        $(".sequenceInp").each(function(index, element) {
            if( $(this).val()!=$(this).attr('oldSort') ){
                ids_sort+=','+$(this).attr('data-id')+'-'+$(this).val();
            }
        });
        if(ids_sort!="")ids_sort=ids_sort.substr(1);
        var data = {
            sort_data:ids_sort,
            type:'sort'
        };
        common_ajax(data,"{:U('System/Personnel/dispostPosition')}",'reload');
    });
    //修改排序值
    $('.SequenceInp').blur(function(){
        var v=parseInt($.trim($(this).val()));
        if(!v) {
            $(this).val($(this).attr('oldSort'));
        }else{
            $(this).val(v);
        };
    });
    //修改权限-前置
    $(document).on('click','.addSubmit',function(){
        var role_id = $(':input[name="role_id"]').val();
        if(role_id!=''){
            //获取node_id
            var access='';
            if($('.radio:checked').length>0){
                $('.radio:checked').each(function(i){
                    if(i==0){
                        access+=$(this).val()+'-'+$(this).attr('pid')+'-'+$(this).attr('level');
                    }else{
                        access+=','+$(this).val()+'-'+$(this).attr('pid')+'-'+$(this).attr('level');
                    };
                });
            };
            edit_access(role_id,access);
        }
    })
})
//职位删除
function del_position(role_id){
    layer.confirm('确定要删除该职位？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        var data = {
            role_id:role_id
        };
        common_ajax2(data,"/SystemApi/Role/delRole",'reload');
    }, function(){});
}
//职位权限修改
function edit_access(role_id,access){
    var data = {
        role_id:role_id,
        access:access
    };
    common_ajax2(data,"/SystemApi/Role/editRole",'reload',function(reflag){
        layer.closeAll('loading');
        if(reflag.code && reflag.code!=0){
            layer.closeAll();
            layer.msg(reflag.msg,{icon:2});
        }else{
            layer.closeAll();
            layer.msg(reflag.msg,{icon:1});
        }
        $(':input[name="role_id"]').val('');
    });
};