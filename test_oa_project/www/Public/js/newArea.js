selectbox2();


function selectbox2() {
    $(document).bind({
        click: function() {
            $(".selectbox2 dt").parent().find("ul").removeClass("s");

            $(".selectbox2 dt").find(".select_title2").hide();

        }
    });
    $(document).delegate("dt", "click", ".select2",
        function() {
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

    $(document).delegate("dd", "click", ".select2",
        function() {
            var url = $(this).attr("data-url");
            if (url != undefined) window.location.href = url;
            var data_value = $(this).attr('data-value');
            var data_name = $(this).text();
            var data_id = $(this).attr('zoneid');
            $(this).parent("dl").find(".select_title").text(data_name);
            $(this).parent("dl").find(".select_title").attr('zoneid',data_id);
            $(this).parent("dl").find(".select_title2").toggle();
            $(this).parent("dl").find(".select_title3").toggle();
            $(this).parent().parent().find(".select_title").text(data_name);
            $(this).parents("dl").next().val(data_value);
            $(this).parents("dl").find("dd").toggle();
            selectStatus($(".select2 dd").parent("dl").find("dt"));
            //return false;
            var callback = $(this).attr('callback');
            if (callback) eval(callback + '(this)');
        });
    $(document).click(function() {
        $(".select2 dd").hide();
        $('.select2 .select_title3').hide();
        selectStatus($(".select2 dt"));
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
        obj.parent().find("dd,.select_title3").show();
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

$(function(){
    //获取页面参数
    var zone_id = $.getUrlParam('zone_id');
    getAjax(zone_id);
    //获取列表内容
    function getAjax(zone_id){
        var data={
            zone_id:zone_id
        };
        //获取详情
        common_ajax2(data,'/SystemApi/Zone/getZoneInfo','no',function(redata){
            if(redata.data){
                layui.use('laytpl', function(){
                    var laytpl = layui.laytpl;
                    laytpl(demo_body.innerHTML).render(redata.data, function(result){
                        $('.newMiddle').html(result);
                    });
                });
            };
        },1);
    };
});

//提交
$(document).on('click', '.newAreaSubmit', function() {
    var data = {
        name:$(':input[name="name"]').val(),
        zone_id:$(':input[name="zone_id"]').val(),
        address:$(':input[name="address"]').val(),
        tel:$(':input[name="tel"]').val(),
        email:$(':input[name="email"]').val(),
        abstract:$(':input[name="abstract"]').val(),
    };
    common_ajax2(data,'/SystemApi/Zone/addZone',0,function(redata){
        if(redata.code!=0){
            layer.msg(redata.msg,{icon:2});
        }else{
            layer.msg('操作成功',{icon:1});
            window.location.href = "{:U('/System/Zone/zoneList')}";
        };
    });
});

