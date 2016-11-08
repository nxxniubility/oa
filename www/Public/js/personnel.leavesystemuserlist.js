
//下拉框
function selectbox() {
    $(document).bind({
        click: function() {
            $(".selectbox dt").parent().find("ul").removeClass("s");
        }
    });
    $(document).on('click', '.select dt',
        function(){
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

    $(document).on('click', '.select dd',
        function(){
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_name = $(this).text();
            $(this).parent('.ddoption').toggle();
            $(this).parent("dl").find(".select_title").text(data_name);
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
};
function selectbox2() {
    $(document).bind({
        click: function() {
            $(".selectbox2 dt").parent().find("ul").removeClass("s");

            $(".selectbox2 dt").find(".select_title2").hide();

        }
    });

    $(".dt_btn").on( "click",
        function() {
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_value_name = $(this).attr('data-name');
            var data_name = $(this).text();
            $(this).parents("dl").find(".select_title").text(data_name);
            $(':input[name="'+data_value_name+'"]').val(data_value);
            $(this).parents('.ddoption').toggle();
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select dd").parent("dl").find("dt"));
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
        });
    $(document).on('click', '.select2 dt',
        function(){
            if ($(this).is(".caption")) return false;
            if ($(this).is(".caption2")) return false;
            $(this).parent().find("dd").toggle();
            $(this).parent().find(".ddoption").toggle();
            $(this).parent().find(".select_title2").toggle();
            $(this).parent().find(".select_title3").toggle();
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
    $(document).on('click', '.select2 dd',
        function(){
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_name = $(this).text();
            var data_value_name = $(this).attr('data-name');
            $(this).parent("dl").find(".select_title").text(data_name);
            $(this).parent("dl").find(".select_title2").toggle();
            $(this).parent("dl").find(".select_title3").toggle();
            $(this).parent().parent().find(".select_title").text(data_name);
            $(':input[name="'+data_value_name+'"]').val(data_value);
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select2 dd").parent("dl").find("dt"));
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
        });
    $(document).click(function() {
        $(".select2 dd").hide();
        selectStatus($(".select2 dt"));
    });

};
//当前下拉菜单状态
function selectStatus(obj) {
    if (obj.parent().find("dd").is(":hidden")) {
        otherSelectStatus(); //
        obj.parent().removeClass("zindex4").parent().find(".on").removeClass("on");
        obj.find(".arrow").removeClass("arrow_on");
    } else {
        otherSelectStatus(); //
        obj.parent().find("dd,.select_title2").show();
        obj.parent().find("dd,.select_title3").show();
        obj.addClass("on");
        obj.parent().addClass("on zindex4");
        obj.find(".arrow").addClass("arrow_on");

    };
}
//其他下拉菜单状态
function otherSelectStatus() {
    $("[class^=select]").parent().find(".on").removeClass("on");
    $("[class^=select]").find(".arrow").removeClass("arrow_on");
    $("[class^=select]").find("dd,.select_title2").hide();
    $("[class^=select]").find("dd,.select_title3").hide();
    $("[class^=select]").find("dl").removeClass("zindex4");
};

//  操作
$(document).on('click', '.proSelect', function(event){
    $(this).next().toggle().closest('tr').siblings().find('.otherOperation').hide();
    //阻止点击document对当前事件的影响
    event.stopPropagation();
});

$(document).click(function() {
    $('.otherOperation').hide();
});
//获取中心
function getArea(zoneid){
    if(zoneid){
        $.ajax({
            url:"{:U('System/Personnel/getZoneList')}",
            dataType:'json',
            type:'post',
            data:{zone_id:zoneid},
            success:function(redata){
                if(redata.code==0) {
                    if(redata.data.children.length!=''){
                        $('#areaBody').show();
                        var str = '<dl class="select2"><dt><div class="select_title l">所有中心</div> <div class="arrow r"></div></dt>';
                        $.each(redata.data.children, function (n, value) {
                            str += '<dd class="fxDone" data-name="zone_id" data-value="'+value.zone_id+'">'+value.name+'</dd>'
                        });
                        str += '</dl>';
                        $('#areaBody').html(str);
                    }else{
                        $('#areaBody').hide();
                    }
                }
            }
        });
    }else{
        $('#areaBody').hide();
    };
};
//网络通话
function dayu_call(userKey){
    layer.confirm('您要与该员工进行网络通话吗？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        $.ajax({
            url:"{:U('System/Personnel/dispostSystemUser')}",
            dataType:'json',
            type:'post',
            data:{userKey:userKey,type:'call'},
            success:function(redata){
                if(redata.code==0) {
                    layer.msg(redata.msg, {icon:1});
                }else{
                    layer.msg(redata.msg, {icon:2});
                };
            }
        });
    }, function(){});
}

$(function(){
    selectbox();
    selectbox2();
    //pjax
    //$(document).pjax('a', '#pjax_body' ,{fragment:'#pjax_body', timeout:8000});
    $('#foem1').submit(function (event) {
        $.pjax.submit(event, '#pjax_body', {fragment:'#pjax_body', timeout:6000});
        getAjax();
    });
    $("a").click(function (event) {
        NProgress.start();
        var url = $(this).attr("href");
        setTimeout(function () {
            $.pjax({ url: url, container: '#pjax_body', fragment: '#pjax_body', timeout: 8000 });
        }, 300);
        return false;
    });
    //pjax链接点击后显示加载动画
    $(document).on('pjax:send', function() {
        //$("#pjax_body").fadeOut();
        NProgress.start();
    });
    //pjax链接加载完成后隐藏加载动画
    $(document).on('pjax:complete', function() {
        //加载进度条完成。
        NProgress.done();
        //$("#pjax_body").fadeIn();
    });
    //全选
    $('#feCheckBox1').change(function(e){
        if($(this).is(':checked')){
            $(":input[name='feCheck']").each(function(){
                $(this)[0].checked = true
            });
        }else{
            $(":input[name='feCheck']").each(function(){
                $(this).removeAttr("checked");
            });
        };
    });
    //账号删除
    $(document).on('click','.delSystemUser',function(){
        var system_user_id = $(this).parents('tr').children('td').eq(1).text();
        var data = {
            system_user_id:system_user_id
        };
        layer.confirm('确定要删除该账号？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            common_ajax(data,"/SystemApi/SystemUser/delSystemUser",'reload');
        }, function(){});
    });
    //账号批量删除
    $(document).on('click','.delInp',function(){
        var users = '';
        $(':input[name="feCheck"]:checked').each(function(k,v){
            if(k==0){
                users = $(this).val();
            }else{
                users += ','+$(this).val();
            }
        });
        var data = {
            users:users,
            type:'dels'
        };
        layer.confirm('确定要批量删除选中的账号？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            common_ajax(data,"/SystemApi/SystemUser/delSystemUser",'reload');
        }, function(){});
    });
});
//获取列表内容
function getAjax(page){
    var page = $.getUrlParam('page',1);
    var thisObj = $('#pjax_body');
    thisObj.html(getLoding());
    var data = {
        page: ((page-1)*20)+',20',
        order: 'system_user_id desc',
        zone_id: $.getUrlParam('zone_id'),
        role_ids: $.getUrlParam('role_id'),
        usertype: 10,
        key_name: $.getUrlParam('key_name'),
        key_value: $.getUrlParam('key_value'),
    };
    common_ajax2(data,'/SystemApi/SystemUser/getSystemUserList','no',function(redata){
        if(redata.data.data){
            layui.use('laytpl', function(){
                var laytpl = layui.laytpl;
                laytpl(demo.innerHTML).render(redata.data.data, function(result){
                    thisObj.html(result);
                });
            });
        }else{
            thisObj.html(getNullHint());
        };
        //分页
        if(redata.data.count>0){
            layui.use(['laypage', 'layer'], function(){
                var laypage = layui.laypage,layer = layui.layer;
                laypage({
                    cont:  $('.paging')
                    ,pages:  Math.ceil(redata.data.count/20) //总页数
                    ,groups: 5 //连续显示分页数
                    ,first:'首页'
                    ,last:'尾页'
                    ,skin: '#4dbe88'
                    ,curr: page
                    ,jump: function(obj, first){
                        //得到了当前页，用于向服务端请求对应数据
                        var curr = obj.curr;
                        if(!first){
                            var url = $.getUrl('page',curr);
                            $.pjax({ url: url, container: '#pjax_body', fragment: '#pjax_body', timeout: 8000 });
                            return false;
                            //location.href=$.getUrl('page',curr);
                        }
                    }
                });
            });
        };
    },1);
};
//获取部门职位列表
common_ajax2('','/SystemApi/Department/getDepartmentRoleList','no',function(redata){
    if(redata.data.data){
        layui.use('laytpl', function(){
            var laytpl = layui.laytpl;
            laytpl(demo1.innerHTML).render(redata.data.data, function(result){
                $('#role_li').html(result);
                if($.getUrlParam('role_id')){
                    var _role_title = $('#role_li').find('.fxDone[data-value="'+$.getUrlParam('role_id')+'"]').text();
                    $('#role_li').find('.select_title').text(_role_title);
                    $(':input[name="role_id"]').val($.getUrlParam('role_id'));
                };
            });
        });
    };
},1);
//获取区域列表
common_ajax2('','/SystemApi/Zone/getZoneList','no',function(redata){
    if(redata.data){
        layui.use('laytpl', function(){
            var laytpl = layui.laytpl;
            laytpl(demo2.innerHTML).render(redata.data, function(result){
                $('#zone_li').html(result);
                if($.getUrlParam('zone_id')){
                    var _zone_title = $('#zone_li').find('.fxDone[data-value="'+$.getUrlParam('zone_id')+'"]').text();
                    $('#zone_li').find('.select_title').text(_zone_title);
                    $(':input[name="zone_id"]').val($.getUrlParam('zone_id'));
                };
            });
        });
    };
},1);