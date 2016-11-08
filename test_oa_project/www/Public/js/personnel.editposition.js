//------------------下拉框
$(document).on('click','dt',function(){
    $(this).parent().find("dd").toggle();
    $(this).parent().find(".ddoption").toggle();
    selectStatus($(this));
    if ($(this).attr("class") == "on") {
        if ($(this).parent().find("ul").height() > 200) {
            $(this).parent().find("ul").addClass("s");
        };
    } else {
        $(this).parent().find("ul").removeClass("s");
    };
    return false;
});
$(document).on('click','dd',function(){
    var url =  $(this).attr("data-url");
    if (url != undefined) window.location.href = url;
    var data_value =  $(this).attr('data-value');
    var data_name =  $(this).text();
    $(this).parent('.ddoption').toggle();
    $(this).parent("dl").find(".select_title").text(data_name);
    $(this).parent().parent().find(".select_title").text(data_name);
    $(this).parents("dl").next().val(data_value);
    $(this).parents("dl").find("dd").toggle();
    var callback = $(this).attr('callback');
    if (callback) eval(callback + '(this)');
    otherSelectStatus();
});
$(document).click(function() {
    $(".select dd").hide();
    otherSelectStatus();
});
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
    };
};
//其他下拉菜单状态
function otherSelectStatus() {
    $("[class^=select]").parent().find(".on").removeClass("on");
    $("[class^=select]").find(".arrow").removeClass("arrow_on");
    $("[class^=select]").find("dd,.select_title2").hide();
    $("[class^=select]").find("dl").removeClass("zindex4");
};

//-----------------end 下拉框

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
    $('.addPerClose, .addSubmit').on('click', function() {
        layer.closeAll(); // 关闭
    });
});

// 限制字符长度
function chkLength(el,size){
    if(el.value.length > size){
        layer.msg('不能超过'+ size +'字数限制.',{icon:2});
    }
    el.value = el.value.substring(0,size);
};
$(document).ready(function() {
    var data = {
        role_id:$.getUrlParam('role_id')
    };
    //获取职位详情
    common_ajax2(data,'/SystemApi/Role/getRoleInfo','no',function(redata){
        if(redata.data){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(demo_body.innerHTML).render(redata.data, function(result){
                    $('.addPerMiddle').html(result);
                });
            });
        };
    },1);
    //获取部门列表
    common_ajax2('','/SystemApi/Department/getDepartmentList','no',function(redata){
        if(redata.data.data){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(demo.innerHTML).render(redata.data.data, function(result){
                    $('#dp-list').html(result);
                    var department_id = $(':input[name="department_id"]').val();
                    var department_name = $('#dp-list').find('.fxDone[data-value="'+department_id+'"]').text();
                    $('#dp-list').find('.select_title').text(department_name);

                });
            });
        };
    },1);
    //获取职位列表
    common_ajax2('','/SystemApi/Role/getRoleList','no',function(redata){
        if(redata.data.data){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(demo2.innerHTML).render(redata.data.data, function(result){
                    $('#role-list').html(result);
                    var role_id = $(':input[name="pid"]').val();
                    var role_name = $('#role-list').find('.fxDone[data-value="'+role_id+'"]').text();
                    $('#role-list').find('.select_title').text(role_name);

                });
            });
        };
    },1);
    //树配置
    $("#boxTabel").treeTable({
        expandable: true,
    });
    //获取已有权限
    common_ajax2(data,'/SystemApi/Role/getRoleNode','no',function(reflag){
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
                        $('#node-'+on_nodes[i]).children().children('.radio').attr('checked',true);
                    }
                };
            };
        };
    },1);
    //提交
    $(document).on('click','.addPerSubmit',function() {
        //获取node_id
        var access='';
        if($('.radio:checked').length>0){
            for(var i=0;i<$('.radio:checked').length;i++){
                var Obj = null;
                Obj = $('.radio:checked').eq(i);
                if(i==0){
                    access+=Obj.val()+'-'+Obj.attr('pid')+'-'+Obj.attr('level');
                }else{
                    access+=','+Obj.val()+'-'+Obj.attr('pid')+'-'+Obj.attr('level');
                }
            }
        }
        var data = {
            role_id:$.getUrlParam('role_id'),
            positionname:$(':input[name="positionname"]').val(),
            remark:$(':input[name="remark"]').val(),
            department_id:$(':input[name="department_id"]').val(),
            superiorid:$(':input[name="superiorid"]').val(),
            sort:$(':input[name="sort"]').val(),
            display:$(':input[name="display"]:checked').val(),
            access:access
        };
        common_ajax2(data,'/SystemApi/Role/editRole',0,function(redata){
            if(redata.code!=0){
                layer.msg(redata.msg,{icon:2});
            }else{
                layer.msg('操作成功',{icon:1});
                window.location.href = "/System/Personnel/position";
            };
        });
    });
});