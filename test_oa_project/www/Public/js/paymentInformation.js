//  下拉初始化
selectbox();
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
    $("[class^=select]").find("dd,.select_title3").hide();
    $("[class^=select]").find("dl").removeClass("zindex4");
}

/*$('#paymentType').find('dd').each(function(){
	
});*/

//  设置优惠方式
$('.payPreferentialWay').on('click', function(){
    $.colorbox({
        inline: true,
        href: $(".preferentialWayBox"),
        overlayClose: false,
        title: "设置优惠方式",
        onLoad: function(){
        	$('#cboxClose').remove();
        }
    });
});$(':input[name="discount_id"]')
//  关闭colorbox弹窗
$('.pwConfirm, .notpreferential').on('click',function(){
	//  关闭colorbox弹窗
	$('.preferentialWayBox').colorbox.close();
	var father = $('.notpreferential').closest('.preferentialWayBox'),
		radioInp = father.find('input[type="radio"]'),
		chkInp = father.find('input[type="checkbox"]');
	
});


//  课程费用
$('#paymentCourse dd').on('click',function(){
    if($(this).attr('data-price')){
        if($(this).closest('.payRow').next().hasClass('dn')){
            $(this).closest('.payRow').next().removeClass('dn');
        }
        $(this).closest('.payRow').next().children('.price').text('￥'+$(this).attr('data-price'));
    }
});
// 付款类型
$('#paymentType dd').on('click',function(){
    if($(this).attr('data-value')!=1){
        if($(this).closest('.payRow').next().hasClass('dn')){
            $(this).closest('.payRow').next().removeClass('dn');
        }
    }else{
        $(this).closest('.payRow').next().addClass('dn');
    }
});

//选中优惠金额时，锁定其所禁止的选项，再次点击时，取消释放锁定
$('.pwChk').on('click',function(){
    var repeatIds = '';
    $(':input[name="checkboxName"]:checked').each(function(){
        if(repeatIds==''){
            repeatIds += $(this).attr('data-value');
        }else{
            repeatIds += ','+$(this).attr('data-value');
        }  
    })
    $(':input[name="checkboxName"]').removeAttr('disabled'); 
    repeatIds = repeatIds.split(',');
    $.each(repeatIds,function(k,v){
        if (v != 0) {
            $('#discountInfo_'+v).attr('checked',false).attr('disabled','disabled');
        }
    })
    
})


// 优惠金额
$('.discountSub').on('click',function(){
    if($(this).hasClass('true')){
        var ids = '';
        var moneyCount = 0;
        var show_html = '<tr> <th>优惠项目</th> <th>优惠金额</th> </tr>';
        $('.pwChk').each(function(){
            if($(this).is(':checked')){
                if(ids==''){
                    ids = $(this).val();
                }else{
                    ids += ','+$(this).val();
                }
                moneyCount = moneyCount+parseInt($(this).parent().siblings('.money').attr('data-value'));
                show_html += '<tr> <td>'+$(this).parent().siblings('.name').text()+'</td> <td>'+$(this).parent().siblings('.money').text()+'</td> </tr>';
            }
        })
        if(moneyCount>0){
            if(moneyCount>max_cost){
                show_html += '<tr> <td>合计</td> <td style="color:red">¥'+max_cost+'(最高优惠不能大于'+max_cost+')</td> </tr>';
            }else{
                show_html += '<tr> <td>合计</td> <td>¥'+moneyCount+'</td> </tr>';
            }
        }
        $('#discount_body').removeClass('dn').empty().html(show_html);
        $(':input[name="discount_id"]').val(ids);
        if (!ids) {
            $('#discount_body').addClass('dn').empty();
        }
    }else{
        $('#discount_body').addClass('dn').empty();
        $('.pwChk').attr('checked',false);
        $(':input[name="discount_id"]').val('');
    }
});
//receivables();
// 收款
function receivables(){
    $.colorbox({
        inline: true,
        href: $(".receivablesBox"),
        overlayClose: false,
        title: "确认收款"
    });
    // 收款日期选择
    setTimeout(function(){
        $(".receivablesTime").glDatePicker({});
    },1000);
    // 关闭返回列表
    $('#cboxClose').on('click', function(){
        window.location.href=orderList_href;
    });
};
/*
 |-----------------------------------------------------------------------------------
 | 异步提交处理
 |-----------------------------------------------------------------------------------
 */
 //提交缴费信息
$('.paySubmit').on('click',function(){
    if($('input[name="course_id"]').val().length==0){
        layer.msg('请选择进班课程', {icon:2});
        return false;
    }else if($('input[name="studytype"]').val().length==0){
        layer.msg('请选择学习方式', {icon:2});
        return false;
    }else if($('input[name="loan_institutions_id"]').val().length==0){
        layer.msg('请选择付款类型', {icon:2});
        return false;
    }else if($('input[name="loan_institutions_id"]').val()!=1){
        if($('input[name="loan_institutions_cost"]').val()==0 || $('input[name="loan_institutions_cost"]').val()==''){
            layer.msg('请输入贷款金额', {icon:2});
            return false;
        };
    };
    var data = {
        course_id : $('input[name="course_id"]').val(),
        studytype : $('input[name="studytype"]').val(),
        loan_institutions_id: $('input[name="loan_institutions_id"]').val(),
        loan_institutions_cost: $('input[name="loan_institutions_cost"]').val(),
        discount_id: $('input[name="discount_id"]').val()
    };
    common_ajax2(data, local_href, 'no', hintDispost);
});
function hintDispost(reflag){
    if(reflag.code==0){
        var index = layer.confirm('客户的缴费信息已经添加成功，请问是否继续收款？', {
            btn: ['添加收款','返回订单列表'] //按钮
        }, function(){
            layer.close(index);
            receivables();
        }, function(){
            window.location.href=orderList_href;
        });
    }else{
        layer.msg(reflag.msg, {icon:2});
    }
};
//收款
$('#receivablesSub').on('click', function(){
    var data = {
        order_id : $('input[name="order_id"]').val(),
        practicaltime : $('input[name="receivables_practicaltime"]').val(),
        payway: $('input[name="receivables_payway"]').val(),
        cost: $('input[name="receivables_cost"]').val()
    }
    common_ajax2(data, payfundOrder_href, 'no', hrefUrl);
});
function hrefUrl(reflag){
    if(reflag.code==0){
        window.location.href=orderList_href;
    }else{
        layer.msg(reflag.msg, {icon:2});
    }
}