//关闭colorbox弹窗
$('.naEtermine').on('click', function() {
    layer.closeAll(); 			// 关闭
});

/*转介绍显示*/
$('.singleBox').each(function(){
	var $self = $(this);
	$self.find('label').on('click',function(){
		var status=parseInt($(this).find("input").val());
        	$(this).find("input").prop("checked", true);
            $(this).siblings("input").prop("checked", false);
            if(status){
            	$(this).closest('.alRow').next().fadeIn();
            }else{
            	$(this).closest('.alRow').next().fadeOut();
            }
	})
});

//添加预转出人
$(document).on('click', '.btn_apply_tosystem', function() {
    var index = layer.open({
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
    getSystemUser(1, 'apply');
    //添加预转出人
    $(document).on('click', '.apply_tosystemuser_submit', function() {
        $(':input[name="apply_to_system_user_id"]').val($(this).attr('data-value'));
        $(':input[name="apply_to_system_user_name"]').val($(this).siblings('.wOne').text());
        layer.close(index);
    });
});

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
        var _get_url = restartUser_href;
    }else if(type=='allocation'){
        var _get_url = allocationUser_href;
    }else if(type=='apply'){
        var _get_url = applyUser_href;
    };
    common_ajax2(data, _get_url, 'no', getsystemallocation, 1);
    function getsystemallocation(reflag){
        $('.fee_loding').remove();
        if(reflag.code==0){
            var html = '';
            var _get_ids = '';
            $.each(reflag.data.data,function(k,v){
                if(_get_ids.length==0){
                    _get_ids = v.system_user_id;
                }else{
                    _get_ids += ','+v.system_user_id;
                };
                html +='<dl class="clearfix fw list_system_user_'+v.system_user_id+'"><dd class="wOne">'+v.sign+'-'+v.realname+'</dd><dd class="wTwo">'+v.zonename+'</dd> ';
                //$.each(v.count,function(k2,v2){
                //$('.channelname .'+aa[k2]).html('<p>'+v2.channelname+'</p><p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>');
                //    if(k2<6){
                //        html +='<dd class="'+aa[k2]+'"><p>'+v2.countA+' &nbsp;&nbsp; '+v2.countB+' &nbsp;&nbsp;'+v2.countC+'&nbsp;&nbsp; '+v2.countD+'</p> </dd>';
                //    }
                //});
                for(var i=0;i<6;i++){
                    html +='<dd class="'+aa[i]+'"><p>-- &nbsp;&nbsp; -- &nbsp;&nbsp;--&nbsp;&nbsp; --</p> </dd>';
                }
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
            if(_get_ids.length>0){
                var data_infoquality = {
                    type : 'getInfoquality', systemUserId: _get_ids
                };
                common_ajax2(data_infoquality, _get_url, 'no', _getInfoquality, 1);
                function _getInfoquality(redata){
                    if(redata.code==0){
                        $.each(redata.data,function(k,v){
                            $.each(v.count,function(k2,v2){
                                if( !$('.channelname').hasClass('no') ){
                                    $('.channelname .'+aa[k2]).html('<p>'+v2.channelname+'</p><p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>');
                                    $('.channelname').addClass('no');
                                };
                                $('.list_system_user_'+v.system_user_id).find('.'+aa[k2]).html('<p>'+v2.countA+' &nbsp;&nbsp; '+v2.countB+' &nbsp;&nbsp;'+v2.countC+'&nbsp;&nbsp; '+v2.countD+'</p> </dd>');
                            });
                        });
                    };
                };
            };
        }else{
            $('#allocation_body,#search_body').html(' ');
            layer.msg(reflag.msg,{icon:2});
        };
    };
};