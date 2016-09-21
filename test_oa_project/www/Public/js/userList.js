//-------------------------2016/08/05---------------------



$(function(){
    //重置筛选
    $('#clearSearch').click(function(){
        $('.frame .details').each(function(){
            if(!$(this).children('ul').children('li').eq(0).children('a').hasClass('on_hover')){
                $(this).children('ul').children('li').eq(0).children('a').trigger('click');
            }
        });
        $(':input[name="channel_sele"]').get(0).selectedIndex=0;
        if($(':input[name="zone_sele"]').length>0){
            $(':input[name="zone_sele"]').get(0).selectedIndex=0;
        }
        if($(':input[name="role_sele"]').length>0){
            $(':input[name="role_sele"]').get(0).selectedIndex=0;
        }
        if($(':input[name="system_sele"]').length>0){
            $(':input[name="system_sele"]').get(0).selectedIndex=0;
        }
        if($(':input[name="createsystem_sele"]').length>0){
            $(':input[name="createsystem_sele"]').get(0).selectedIndex=0;
        }
        $('#subForm').children('input[type="hidden"]').val('');
    });
    //创建订单
    $(document).on('click', '#reserve_submit', function(){
        if($(':input[name="reserve_subscription"]').val().length>10){
            layer.msg('定金不能大于10位数', {icon:2});
            return false;
        }else if(!chkInt($(':input[name="reserve_subscription"]').val())){
            layer.msg('定金只能输入数字', {icon:2});
            return false;
        }
        var data = {
            type : 'reserve',
            user_id:$(':input[name="temp_user_id"]').val(),
            username:$(':input[name="reserve_username"]').val(),
            realname : $(':input[name="reserve_realname"]').val(),
            subscription : $(':input[name="reserve_subscription"]').val()
        };
        common_ajax2(data, createOrder_href, 'reload');
    });

    function chkInt(strForText){
        var str = /^[0-9]+.?[0-9]*$/;
        var reg = new RegExp(str);
        if(!reg.test(strForText)) {
            return false;
        }
        return true;
    };
    //设置自定义列
    $(document).on('click', '#column_submit', function(){
        var columnname = '';
        $('.column_name:checked').each(function(i){
            if(i==0){
                columnname += $(this).val()+'-'+$(this).parents('.wOne').siblings('.wThr').children('input').val();
            }else{
                columnname += ','+$(this).val()+'-'+$(this).parents('.wOne').siblings('.wThr').children('input').val();
            };
        });
        var columntype = 1;
        if($(':input[name="columntype"]').length>0){
            columntype = $(':input[name="columntype"]').val();
        }
        var data = {
            type : 'column',
            columnname:columnname,
            columntype : columntype
        };
        common_ajax2(data, editColumn_href, 'reload');
    });
    //赎回客户
    $(document).on('click', '#recover_submit', function(){
        if($(':input[name="recover_nextvisit"]').val().length==0){
            layer.tips("下次回访时间不能为空", $(':input[name="recover_nextvisit"]'));
            return false;
        }
        var data = {
            type : 'recover',
            user_id:$(':input[name="temp_user_id"]').val(),
            waytype : $(':input[name="recover_waytype"]').val(),
            attitude_id : $(':input[name="recover_attitude_id"]').val(),
            nexttime : $(':input[name="recover_nextvisit"]').val()+' '+$(':input[name="recover_nextvisit_h"]').val()+":"+$(':input[name="recover_nextvisit_i"]').val(),
            remark : $(':input[name="recover_remark"]').val()
        };
        common_ajax2(data, recoverUser_href, 'reload');
    });
    //确认到访
    $(document).on('click', '#visit_submit', function(){
        var data = {
            type : 'visit',
            user_id:$(':input[name="temp_user_id"]').val()
        };
        common_ajax2(data, affirmVisit_href, 'reload');
    });
    //设置重点标记
    $(document).on('click', '.btn_mark', function(){
        var user_id = $(this).parent().attr('data-value');
        var _thisObj = $(this);
        var mark = _thisObj.attr('data-value');
        var key = _thisObj.attr('key');
        if(!_thisObj.hasClass('active')){
            var data = {
                type : 'edituser',
                user_id:user_id,
                mark : mark
            };
            common_ajax2(data, editUserMark_href, 'no', on_btn);
            function on_btn(reflag){
                if(reflag.code==0){
                    if($(".keyPop").length>0){
                        if(_thisObj.attr('data-value')==1){
                            $('.keyPop').find('.kpCenterTitle').text('标记普通客户');
                            $('.keyPop').find('.kpCenterCont').children('p').text('已将该客户标记为普通客户');
                            $('.keyPop').find('.key_realname').html(_thisObj.parent().attr('data-realname')+'<i><img src="/Public/images/star_ordinary.png"></i>');
                            $('.dispost_'+key).children('.btn_mark').attr('data-value',2).html('<span class="setEdit"></span><em>标为重点</em>');
                            $('.bodyr_'+key).children('.emphasis').attr('data-value',2).html('<img src="/Public/images/star-client-2.png" alt="普通" width="20" height="20">');
                        }else{
                            $('.keyPop').find('.kpCenterTitle').text('标记重点客户');
                            $('.keyPop').find('.kpCenterCont').children('p').text('已将该客户标记为重点客户');
                            $('.keyPop').find('.key_realname').html(_thisObj.parent().attr('data-realname')+'<i><img src="/Public/images/star-client.png"></i>');
                            $('.dispost_'+key).children('.btn_mark').attr('data-value',1).html('<span class="setEdit1"></span><em>标为普通</em>');
                            //标为重点和标为普通不同状态下的背景图改变
                            $('.bodyr_'+key).children('.emphasis').attr('data-value',1).html('<img src="/Public/images/star-client.png" alt="重点" width="20" height="20">');
                        }
                        _thisObj.parents('.otherOperation').hide();
                        $('.keyPop').find('.key_mobile').text(_thisObj.parent().attr('data-username'));
                        layer.open({
                            type: 1, 					//  页面层
                            title: false, 				//	不显示标题栏
                            area: ['auto', 'auto'],
                            closeBtn: 0, //不显示关闭按钮
                            shift: 1,
                            shade: .6, 					//	遮罩
                            time: 0, 					//  关闭自动关闭
                            shadeClose: true, 			//	遮罩控制关闭层
                            content: $(".keyPop"),	//  加载主体内容
                            scrollbar: false
                        });
                        setTimeout(function(){
                            layer.closeAll();
                        },2000);
                    }else{
                        setTimeout(function(){
                            location.reload();
                        },1000);
                    }
                };
            }
        };
    });
});


//------------------------------------异步获取分页--------------------------------------
function getPaging(){
    //异步获取分页
    var data = {
        'type':'getPaging'
    };
    $.ajax({
        url: window.location.href,
        dataType:'json',
        type:'post',
        data:data,
        success:function(reflag){
            $('#paging').html(reflag.data);
            return false;
        },
        error:function(){
            getPaging();
            return false;
        }
    });
};

//------------------------------------回库客户--------------------------------------
$(function(){
    //回库
    $(document).on('click', '.btn_abandon', function() {
        //批量？
        if($(this).hasClass('pl')){
            var ids = '';
            $(':input[name="librayChk"]:checked').each(function(){
                if(ids==''){
                    ids= $(this).val();
                }else{
                    ids += ','+$(this).val();
                };
            });
            if(ids!=''){
                $(':input[name="temp_user_id"]').val(ids);
            }else{
                layer.msg('请先选择客户',{icon:2});
                return false;
            };
        }else{
            $(':input[name="temp_user_id"]').val($(this).parent('ul').attr('data-value'));
            $('#panel4').find('.realname').text($(this).parent('ul').attr('data-realname'));
            $('#panel4').find('.mobile').text($(this).parent('ul').attr('data-username'));
            $("#panel4").find('.clearinfo').show();
        };
        $("#panel4").find('.clearinfo').hide();
        layer.open({
            type: 1, 					//  页面层
            title: '填写原因', 		//	不显示标题栏
            area: ['600px', 'auto'],
            closeBtn:2,
            shade: .6, 					//	遮罩
            time: 0, 					//  关闭自动关闭
            shadeClose: true, 			//	遮罩控制关闭层
            shift: 1, 					//	出现动画
            content: $('#panel4')	//  加载主体内容
        });
        return false;
    });
    //批量回库提交
    $(document).on('click', '#abandonAll_submit', function(){
        var data = {
            type : 'abandon',
            user_id:$(':input[name="temp_user_id"]').val(),
            abandon_remark:$(':input[name="abandonAll_remark"]').val()
        };
        common_ajax2(data, abandonUser_href, 'reload');
    });
    //回库提交
    $(document).on('click', '#abandon_submit', function(){
        var data = {
            type : 'abandon',
            user_id:$(':input[name="temp_user_id"]').val(),
            abandon_attitude_id:$(':input[name="abandon_attitude_id"]').val(),
            abandon_remark:$(':input[name="abandon_remark"]').val()
        };
        common_ajax2(data, abandonUser_href, 'reload');
    });
});

//------------------------------------转出客户--------------------------------------
$(function(){
    //转出
    $(document).on('click', '.btn_allocation', function() {
        //批量？
        if($(this).hasClass('pl')){
            var ids = '';
            $(':input[name="librayChk"]:checked').each(function(){
                if(ids==''){
                    ids= $(this).val();
                }else{
                    ids += ','+$(this).val();
                };
            });
            if(ids!=''){
                $(':input[name="temp_user_id"]').val(ids);
            }else{
                layer.msg('请先选择客户',{icon:2});
                return false;
            };
        }else{
            $(':input[name="temp_user_id"]').val($(this).parent('ul').attr('data-value'));
        };
        layer.open({
            type: 1, 					//  页面层
            title: '选择操作者', 			//	不显示标题栏
            area: ['1000px', '490px'],
            closeBtn:2,
            shade: .6, 					//	遮罩
            time: 0, 					//  关闭自动关闭
            shadeClose: true, 			//	遮罩控制关闭层
            shift: 1, 					//	出现动画
            content: $("#panel3")	//  加载主体内容
        });
        $('.Capacity').attr('data-type','allocation');
        getSystemUser(1, 'allocation');
    });
    //转出
    $(document).on('click', '.allocation_submit', function(){
        is_scroll = 1;
        is_page_num = 1;
        var data = {
            type : 'submit',
            user_id:$(':input[name="temp_user_id"]').val(),
            system_user_id: $(this).attr('data-value')
        };
        layer.confirm('确定要将客户转给该员工？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            common_ajax2(data, allocationUser_href, 'reload');
        }, function(){});
    });
});


//------------------------------------出库客户--------------------------------------
$(function(){
    //出库客户
    $(document).on('click', '.btn_restart', function() {
        //批量？
        if($(this).hasClass('pl')){
            var ids = '';
            $(':input[name="librayChk"]:checked').each(function(){
                if(ids==''){
                    ids= $(this).val();
                }else{
                    ids += ','+$(this).val();
                };
            });
            if(ids!=''){
                $(':input[name="temp_user_id"]').val(ids);
            }else{
                layer.msg('请先选择客户',{icon:2});
                return false;
            };
        }else{
            $(':input[name="temp_user_id"]').val($(this).parent('ul').attr('data-value'));
        };
        layer.confirm('进行出库操作后，出库人与所属人将被重置', {
            btn: ['确定','取消'] //按钮
        }, function(index){
            layer.close(index);
            layer.open({
                type: 1, 					//  页面层
                title: '选择操作者', 			//	不显示标题栏
                area: ['1000px', '490px'],
                closeBtn:2,
                shade: .6, 					//	遮罩
                time: 0, 					//  关闭自动关闭
                shadeClose: true, 			//	遮罩控制关闭层
                shift: 1, 					//	出现动画
                content: $("#panel3")	//  加载主体内容
            });
            $('.Capacity').attr('data-type','restart');
            getSystemUser(1, 'restart');
        }, function(){});
    });
    //出库提交
    $(document).on('click', '.restart_submit', function(){
        is_scroll = 1;
        is_page_num = 1;
        var data = {
            type : 'submit',
            user_id:$(':input[name="temp_user_id"]').val(),
            system_user_id: $(this).attr('data-value')
        };
        layer.confirm('确定要将客户转给该员工？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            common_ajax2(data, restartUser_href, 'reload');
        }, function(){});
    });
});




//------------------------------------异步加载转出列表--------------------------------------
var is_scroll = 1;
var is_page_num = 1;
$('.Capacity').on('scroll', function(){
    var data_type = $(this).attr('data-type');
    if(($(this).scrollTop()+320)>=$('#allocation_body').height() && is_scroll==1){
        getSystemUser(is_page_num+1, data_type);
    }
})
//搜索职位相关-检索
$('.nsSearchSubmit').on('click', function(){
    var data_type = $('#panel3').find('.Capacity').attr('data-type');
    getSystemUser(1,data_type);
});
$(':input[name="allocation_roleselect"]').change(function(){
    var data_type = $('#panel3').find('.Capacity').attr('data-type');
    getSystemUser(1,data_type);
});
//异步加载转出列表
function getSystemUser(page,type){
    if(page>=2) {
        is_scroll = 0;
        is_page_num = page;
        if($('.fee_loding').length<1){
            $('#allocation_body').append('<img class="fee_loding" src="/Public/images/loading.gif" style="margin:30px 0 0 425px; width: 31px;">');
        }
    }else{
        is_scroll = 1;
        is_page_num = 1;
        $('#allocation_body').html('<img class="fee_loding" src="/Public/images/loading.gif" style="margin:100px 0 0 425px; width: 31px;">');
    }
    var aa = ['wThr','wFou','wFiv','wSix','wSev','wEig'];
    if(!page){
        page=1;
    }
    var role_id = $(':input[name="allocation_roleselect"]').val();
    var search = $(':input[name="allocation_realname"]').val();
    if(search=='输入姓名'){
        search = '';
    }
    var data = {
        type : 'getSystemUser', role_id: role_id, search:search, page:page
    };

    if(type=='restart'){
        common_ajax2(data, restartUser_href, 'no', getsystemallocation, 1);
    }else if(type=='allocation'){
        common_ajax2(data, allocationUser_href, 'no', getsystemallocation, 1);
    }else if(type=='apply'){
    common_ajax2(data, applyUser_href, 'no', getsystemallocation, 1);
}
    function getsystemallocation(reflag){
        $('.fee_loding').remove();
        if(reflag.code==0){
            var html = '';
            $.each(reflag.data.data,function(k,v){
                html +='<dl class="clearfix fw"><dd class="wOne">'+v.realname+'</dd><dd class="wTwo">'+v.zonename+'</dd> ';
                $.each(v.count,function(k2,v2){
                    $('.channelname .'+aa[k2]).html('<p>'+v2.channelname+'</p><p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>');
                    if(k2<6){
                        html +='<dd class="'+aa[k2]+'"><p>'+v2.countA+' &nbsp;&nbsp; '+v2.countB+' &nbsp;&nbsp;'+v2.countC+'&nbsp;&nbsp; '+v2.countD+'</p> </dd>';
                    }
                });
                if(type=='restart'){
                    html +='<dd class="wNin restart_submit" data-value="'+v.system_user_id+'"> <i>确定</i> </dd> </dl>';
                }else if(type=='allocation'){
                    html +='<dd class="wNin allocation_submit" data-value="'+v.system_user_id+'"> <i>确定</i> </dd> </dl>';
                }else if(type=='apply'){
                    html +='<dd class="wNin apply_tosystemuser_submit" data-value="'+v.system_user_id+'"> <i>确定</i> </dd> </dl>';
                }
            });
            if(page>=2){
                $('#allocation_body,#search_body').append(html);
            }else{
                $('#allocation_body,#search_body').html(html);
            }
            var _num = reflag.data.count;
            var _num = Math.ceil((_num/10));
            if(_num>page){
                is_scroll=1;
            }
        }else{
            $('#allocation_body,#search_body').html(' ');
            layer.msg(reflag.msg,{icon:2});
        }
    };
};


function SetSameHeight(obj1,obj2,obj3) { 
     var h1 = $(obj1).outerHeight();	//获取对象1的高度
     var h2 = $(obj2).outerHeight();  
     var h3 = $(obj3).outerHeight();  
     var mh = Math.max( h1, h2, h3); 		//比较一下
     $(obj1).height(mh); 
     $(obj2).height(mh); 
     $(obj3).height(mh);
}
$(function(){
	var tdHeight1 = $('#table1').find('td'),
		tdHeight2 = $('#table2_div').find('td'),
		tdHeight3 = $('#table3').find('td');
	SetSameHeight(tdHeight1,tdHeight2);
	SetSameHeight(tdHeight2,tdHeight3);	
});
