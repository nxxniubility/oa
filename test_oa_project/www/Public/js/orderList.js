$(function(){
    //双击
    $(document).on('dblclick',".content_li",function(){
        var el = $(this).find('.hrefDetail');
        $('#hrefForm').attr('action', el.attr('href')).submit();
    });
    //点击区域或者职位
    $('#zone_sele,#role_sele').find('dd').click(function(){
        $(this).parent().siblings('input').val($(this).attr('data-value'));
        getsystem();
    });

    if(navigator.userAgent.indexOf("MSIE")>0)
    {
        if(navigator.userAgent.indexOf("MSIE 6.0")>0)
        {
            $('#subForm').attr('onsubmit',' ');
        }
        if(navigator.userAgent.indexOf("MSIE 7.0")>0)
        {
            $('#subForm').attr('onsubmit',' ');
        }
        if(navigator.userAgent.indexOf("MSIE 8.0")>0)
        {
            //	alert("ie8");
            var url_from = $('#subForm').attr('action');
            var newstr=url_from.replace("pjax_body","main");
            $('#subForm').attr('onsubmit',' ');
        }
    }
    //pjax
    $(document).pjax('a', '#pjax_body' ,{fragment:'#pjax_body', timeout:8000});
    $('#subForm').click(function (event) {
        $.pjax.submit(event, '#pjax_body', {fragment:'#pjax_body', timeout:6000});
    });
    //pjax链接点击后显示加载动画
    $(document).on('pjax:send', function() {
        //加载层-默认风格 loading
        layer.load(2);
    });
    //pjax链接加载完成后隐藏加载动画
    $(document).on('pjax:complete', function() {
        //此处 关闭loading
        layer.closeAll('loading');
    });

    //导出订单
    $('#subOutput').on('click', function(){
        var order_status = '';
        $('#order_status .on_hover').each(function(){
            if(order_status==''){
                order_status+= $(this).attr('data-value');
            }else{
                order_status+= ','+$(this).attr('data-value');
            }
        })
        var order_loan_institutions_id = $('#order_type .on_hover').attr('data-value');
        var order_createtimeS = $(':input[name="order_createtimeS"]').val();
        var order_createtimeE = $(':input[name="order_createtimeE"]').val();
        var order_finishtimeS = $(':input[name="order_finishtimeS"]').val();
        var order_finishtimeE = $(':input[name="order_finishtimeE"]').val();
        var order_zone_id = $(':input[name="order_zone_id"]').val();
        var order_role_id = $(':input[name="order_role_id"]').val();
        var order_system_user_id = $(':input[name="order_system_user_id"]').val();
        //赋值
        $(':input[name="status"]').val(order_status);
        $(':input[name="loan_institutions_id"]').val(order_loan_institutions_id);
        $(':input[name="createtime"]').val(order_createtimeS+'--'+order_createtimeE);
        $(':input[name="finishtime"]').val(order_finishtimeS+'--'+order_finishtimeE);
        $(':input[name="zone_id"]').val(order_zone_id);
        $(':input[name="role_id"]').val(order_role_id);
        $(':input[name="system_user_id"]').val(order_system_user_id);
        //$('#subForm').submit();
    });
    //订单列表
    $('#subSearch').on('click', function(){
        var order_status = '';
        $('#order_status .on_hover').each(function(){
            if(order_status==''){
                order_status+= $(this).attr('data-value');
            }else{
                order_status+= ','+$(this).attr('data-value');
            }
        })
        var order_loan_institutions_id = $('#order_type .on_hover').attr('data-value');
        var order_createtimeS = $(':input[name="order_createtimeS"]').val();
        var order_createtimeE = $(':input[name="order_createtimeE"]').val();
        var order_finishtimeS = $(':input[name="order_finishtimeS"]').val();
        var order_finishtimeE = $(':input[name="order_finishtimeE"]').val();
        var order_zone_id = $(':input[name="order_zone_id"]').val();
        var order_role_id = $(':input[name="order_role_id"]').val();
        var order_system_user_id = $(':input[name="order_system_user_id"]').val();
        //赋值
        $(':input[name="status"]').val(order_status);
        $(':input[name="loan_institutions_id"]').val(order_loan_institutions_id);
        $(':input[name="createtime"]').val(order_createtimeS+'--'+order_createtimeE);
        $(':input[name="finishtime"]').val(order_finishtimeS+'--'+order_finishtimeE);
        $(':input[name="zone_id"]').val(order_zone_id);
        $(':input[name="role_id"]').val(order_role_id);
        $(':input[name="system_user_id"]').val(order_system_user_id);
        $('#subForm').trigger('click');
    });

});


/*
|-----------------------------------------------------------------------------------
| 异步提交处理
|-----------------------------------------------------------------------------------
*/
function chkInt(strForText){
    var str = /^(([1-9]\d{0,9})|0)(\.\d{1,2})?$/;
    var reg = new RegExp(str);
    if(!reg.test(strForText)) {
        return false;
    }
    return true;
};
$(function(){
    //审核提交
    $(document).on('click','.auditPass,.auditNotPassed', function(){
        if($(this).hasClass('auditPass')){
            var status = 'success';
        }else{
            var status = 'faile';
        };
        var data = {
            status : status,
            order_id : $('input[name="order_id"]').val(),
            payway : $('input[name="audit_payway"]').val(),
            practicaltime : $('input[name="audit_practicaltime"]').val()
        }
        common_ajax2(data, auditOrder_href, 'reload');
    });

    //退订金
    $(document).on('click','#depositSub', function(){
        if($('input[name="deposit_zone_id"]').length>0){
            if($('input[name="deposit_zone_id"]').val().length==0){
                layer.msg('请选择选择中心', {icon:2});
                return false;
            };
        };
        var data = {
            type:'deposit',
            order_id : $('input[name="order_id"]').val(),
            practicaltime : $('input[name="deposit_practicaltime"]').val(),
            payway: $('input[name="deposit_payway"]').val(),
            cost: $('input[name="deposit_cost"]').val(),
            zone_id: $('input[name="deposit_zone_id"]').val()
        }
        common_ajax2(data, refundOrder_href, 'reload');
    });

    //收款
    $(document).on('click','#receivablesSub', function(){
        var charge = $(':input[name="receivables_cost"]').val();
        var charge = charge.split('.');
        if(charge[0].length>8){
            layer.msg('收款整数位不能大于8位数', {icon:2});
            return false;
        }else if(!chkInt($(':input[name="receivables_cost"]').val())){
            layer.msg('请输入正确的金额', {icon:2});
            return false;
        }else if($('input[name="receivables_zone_id"]').length>0){
            if($('input[name="receivables_zone_id"]').val().length==0){
                layer.msg('请选择选择中心', {icon:2});
                return false;
            };
        };
        var data = {
            order_id : $('input[name="order_id"]').val(),
            practicaltime : $('input[name="receivables_practicaltime"]').val(),
            payway: $('input[name="receivables_payway"]').val(),
            cost: $('input[name="receivables_cost"]').val(),
            zone_id: $('input[name="receivables_zone_id"]').val()
        }
        common_ajax2(data, payfundOrder_href, 'reload');
    });

    //退款
    $(document).on('click','#returnSub', function(){
        var returnCost = $(':input[name="return_cost"]').val();
        var returnCost = returnCost.split('.');
        if(!chkInt($(':input[name="return_cost"]').val())){
            layer.msg('请输入正确的金额', {icon:2});
            return false;
        }else if($('input[name="return_zone_id"]').length>0){
            if($('input[name="return_zone_id"]').val().length==0){
                layer.msg('请选择选择中心', {icon:2});
                return false;
            };
        };
        var data = {
            order_id : $('input[name="order_id"]').val(),
            practicaltime : $('input[name="return_practicaltime"]').val(),
            payway: $('input[name="return_payway"]').val(),
            cost: $('input[name="return_cost"]').val(),
            zone_id: $('input[name="return_zone_id"]').val()
        }
        common_ajax2(data, refundOrder_href, 'reload');
    });
});

//异步获取分页
function getpaging(page){
    var data = {
        type:'getPaging',
        page:page
    };
    common_ajax2(data, window.location.href, 'no', paging, 1);
    function paging(data){
        $('#paging').html(data.data);
    };
}
//获取员工列表
function getsystem(){
    var html = '<dt> <div class="select_title l">全部所属人</div> <div class="arrow r"></div> </dt> <dd class="fxDone" data-value="0">全部所属人</dd>';
    var zone_id = $(':input[name="order_zone_id"]').val();
    var role_id = $(':input[name="order_role_id"]').val();
    $('#system_sele').empty().append(html);
    $('#system_sele_loading').show();
    var data = {
        type:'getSysUser',zone_id:zone_id,role_id:role_id
    };
    common_ajax2(data, local_href, 'no', setHtml, 1);
    function setHtml(reflag){
        var html2 = '';
        if(reflag.code==0){
            $.each(reflag.data,function(k,v){
                html2+='<dd data-value="'+v.system_user_id+'" style="display: none;">'+v.realname+'</dd>';
            });
        }
        if(html2!=''){
            html += html2;
            $('#system_sele').empty().html(html);
        }
        $('#system_sele_loading').hide();
    }
}