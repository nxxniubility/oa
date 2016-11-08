//------------------������
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
//��ǰ�����˵�״̬
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
//���������˵�״̬
function otherSelectStatus() {
    $("[class^=select]").parent().find(".on").removeClass("on");
    $("[class^=select]").find(".arrow").removeClass("arrow_on");
    $("[class^=select]").find("dd,.select_title2").hide();
    $("[class^=select]").find("dl").removeClass("zindex4");
};

//-----------------end ������

/*�����/�༭ģ��*/
$(document).on('click','.selectPermissions', function() {
    layer.open({
        type: 1, //  ҳ���
        title: false, //    ����ʾ������
        area: ['500px', '500px'],
        shade: .6, //   ����
        time: 0, //  �ر��Զ��ر�
        shadeClose: true, //    ���ֿ��ƹرղ�
        closeBtn: false, // ����ʾ�رհ�ť
        shift: 1, //    ���ֶ���
        content: $(".competenceBox") //  ������������
    });
    $('.addPerClose, .addSubmit').on('click', function() {
        layer.closeAll(); // �ر�
    });
});

// �����ַ�����
function chkLength(el,size){
    if(el.value.length > size){
        layer.msg('���ܳ���'+ size +'��������.',{icon:2});
    }
    el.value = el.value.substring(0,size);
};
$(document).ready(function() {
    var data = {
        role_id:$.getUrlParam('role_id')
    };
    //��ȡְλ����
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
    //��ȡ�����б�
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
    //��ȡְλ�б�
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
    //������
    $("#boxTabel").treeTable({
        expandable: true,
    });
    //��ȡ����Ȩ��
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
    //�ύ
    $(document).on('click','.addPerSubmit',function() {
        //��ȡnode_id
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
                layer.msg('�����ɹ�',{icon:1});
                window.location.href = "/System/Personnel/position";
            };
        });
    });
});