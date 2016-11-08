//  几个时间初始化
$('#atStar, #atEnd, #afBirthday, #afBirthday2').glDatePicker();

//------------------下拉框
$(document).on('click','dt',function(){
    if(!$(this).hasClass('clearfix')){
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
    };
});
$(document).on('click','dd',function(){
    if(!$(this).parent().hasClass('clearfix')){
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
    };
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

/*添加新/编辑模版*/
$('.nsSelectPost').on('click', function() {
    layer.open({
        type: 1, 					//  页面层
        title: false, 				//	不显示标题栏
        area: ['600px', '580px'],
        shade: .6, 					//	遮罩
        time: 0, 					//  关闭自动关闭
        shadeClose: true, 			//	遮罩控制关闭层
        closeBtn: false, 			//	不显示关闭按钮
        shift: 1, 					//	出现动画
        content: $(".department")	//  加载主体内容
    });
    $('.nsClose').on('click', function() {
        layer.closeAll(); 			// 关闭
    });
});

// 限制字符长度
function chkLength(el,size){
    if(el.value.length > size){
        layer.msg('已超出字数规定上限.',{icon:2});
    };
    el.value = el.value.substring(0,size);
};

$("#afBirthday").glDatePicker({onClick:function(el, cell, date, data) {
    el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
}});
$("#afBirthday2").glDatePicker({onClick:function(el, cell, date, data) {
    el.val(date.toLocaleDateString().replace("年","-").replace("月","-").replace("日",""));
}});



$(function(){
    $(document).on('click','.nsDetermine',function(){
        var role_id = '';
        var role_name = ''
        if($(':input[name="nsChk"]:checked').length>3){
            layer.msg('职位添加不能超过三个', {icon:2});
            return false;
        }
        for(var i=0;i<$(':input[name="nsChk"]:checked').length;i++){
            if(i==0){
                role_id += $(':input[name="nsChk"]:checked').eq(i).val();
                role_name += $(':input[name="nsChk"]:checked').eq(i).parent().siblings('.wNsTwo').text()+'/'+$(':input[name="nsChk"]:checked').eq(i).parent().siblings('.wNsThr').text();
            }else{
                role_id += ','+$(':input[name="nsChk"]:checked').eq(i).val();
                role_name += '，'+$(':input[name="nsChk"]:checked').eq(i).parent().siblings('.wNsTwo').text()+'/'+$(':input[name="nsChk"]:checked').eq(i).parent().siblings('.wNsThr').text();
            }
        }
        $(':input[name="role_id"]').val(role_id);
        $(':input[name="role_name"]').val(role_name);
        layer.closeAll();
    });
    //搜索职位相关-检索
    $(document).on('click','.nsSearchSubmit',function(){
        $(':input[name="nsChk"]').attr('checked',false);
        var val = $(':input[name="nsSelectSearch"]').val();
        var d_val = $('.nsSelectSearch_d').text();
        $('.nsDeparMiddle .department_content').remove();
        if(val.length>0 || d_val!='全部'){
            $('#search_body .department_content').each(function(i){
                var zmnumReg=new RegExp( val ,'gim');
                var zmnumReg2=new RegExp( d_val ,'gim');
                var name=$(this);
                if(d_val!='全部'){
                    if( zmnumReg.test(name.children('.wNsThr').text()) && zmnumReg2.test(name.children('.wNsTwo').text()) ){
                        $('.department_title').after(name.clone());
                    }
                }else{
                    if( zmnumReg.test(name.children('.wNsThr').text()) ){
                        $('.department_title').after(name.clone());
                    }
                }
            });
        }else{
            $('.department_title').after( $('#search_body .department_content').clone() );
        };
    });
    $(document).on('click','.nsSubmit',function(){
        var data = {
            realname:$(':input[name="realname"]').val(),
            username:$(':input[name="username"]').val(),
            email:$(':input[name="email"]').val(),
            sex:$(':input[name="sex"]:checked').val(),
            zone_id:$(':input[name="zone_id"]').val(),
            role_id:$(':input[name="role_id"]').val(),
            usertype:$(':input[name="usertype"]').val(),
            check_id:$(':input[name="check_id"]').val(),
            entrytime:$(':input[name="entrytime"]').val(),
            straightime:$(':input[name="straightime"]').val(),
        };
        common_ajax2(data,'/SystemApi/SystemUser/addSystemUser',0,function(redata){
            if(redata.code!=0){
                layer.msg(redata.msg,{icon:2});
            }else{
                layer.msg('操作成功',{icon:1});
                window.location.href = "/System/Personnel/systemUserList";
            };
        });
    });
});

//获取职位列表
common_ajax2('','/SystemApi/Role/getRoleList','no',function(redata){
    if(redata.data.data){
        layui.use('laytpl', function(){
            var laytpl = layui.laytpl;
            laytpl(demo1.innerHTML).render(redata.data.data, function(result){
                $('#role_li').append(result);
                $('#search_body').html(result);
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
//获取员工状态列表
common_ajax2({name:'SYSTEMUSERSTATUS'},'/SystemApi/Config/getStatusList','no',function(redata){
    if(redata.data){
        layui.use('laytpl', function(){
            var laytpl = layui.laytpl;
            laytpl(demo3.innerHTML).render(redata.data, function(result){
                $('#usertype_li').html(result);
                if($.getUrlParam('usertype')){
                    var _usertype_title = $('#usertype_li').find('.fxDone[data-value="'+$.getUrlParam('usertype')+'"]').text();
                    $('#usertype_li').find('.select_title').text(_usertype_title);
                    $(':input[name="usertype"]').val($.getUrlParam('usertype'));
                };
            });
        });
    };
},1);
