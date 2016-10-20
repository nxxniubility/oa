//  操作弹框的点击事件-渠道管理
$(document).on('click', '.regionSelect',function(event){
    $(this).next().toggle().closest('dl').siblings().find('.otherOperation').hide();
    //阻止点击document对当前事件的影响
    event.stopPropagation();
});
//  操作弹框的点击事件-产品线管理
$(document).on('click', '.proSelect',function(event){
    $(this).next().toggle().closest('tr').siblings().find('.otherOperation').hide();
    //阻止点击document对当前事件的影响
    event.stopPropagation();
});

$(document).click(function() {
    $('.otherOperation').hide();
});

// 限制字符长度
function chkLength(el,size){
	if(el.value.length > size){
		layer.msg('不能超过'+ size +'字数限制.',{icon:2});
	}
	el.value = el.value.substring(0,size);
}

//  显示二级区域
$('.osOperation').find('.switchBtnShow').on('click',function(){
    var os = $(this).closest('dl.oneRegions');
    var	parent_id=$(this).attr('node-id');

    var szc= os.nextAll('.parent_id'+parent_id),
        _this=$(this);
    szc.each(function (index,element){
        if($(this).is(':hidden')){
            $(this).removeClass('dn');
            _this.addClass('switchBtnHide');
            _this.attr('title','收起');
        }else {
            $(this).addClass('dn');
            _this.removeClass('switchBtnHide');
            _this.attr('title','展开');
        }
    })

});


//  显示三级区域
$('.szOperation').find('.switchBtnShow').on('click',function(){
    var sz = $(this).closest('dl.secondaryZoneRow');
    var parent_id=$(this).attr('node-id');
    var tr= sz.nextAll('.parent_'+parent_id);
    var _this=$(this);

    tr.each(function (index,element){
        if($(this).is(':hidden')){
            $(this).removeClass('dn');
            _this.addClass('switchBtnHide');
            _this.attr('title','收起');
        }else {
            $(this).addClass('dn');
            _this.removeClass('switchBtnHide');
            _this.attr('title','展开');
            //一级区域关闭同时关闭二级和三级
            $(".regionalCenter").addClass('dn');
        };
    });
});


//  显示中心区域
$('.trOperation').find('.switchBtnShow').on('click',function(){
    var trs = $(this).closest('.threeRegionsRow');
    var parent_id=$(this).attr('node-id');
    var rc = trs.nextAll('.parent_'+parent_id);
    var _this=$(this);
    rc.each(function (index,element){
        if($(this).is(':hidden')){
            $(this).removeClass('dn');
            _this.addClass('switchBtnHide');
            _this.attr('title','收起');
        }else {
            $(this).addClass('dn');
            _this.removeClass('switchBtnHide');
            _this.attr('title','展开');
        }

    })

});



/*选择PC/moblie模版*/
$('.seeDetails').on('click',function(){
    var detailInfo = $(this).attr('detailInfo');
    detailInfo = detailInfo.split('_');
    $('#zone_tel').html(detailInfo[0]);
    $('#zone_email').html(detailInfo[1]);
    $('#zone_name').html(detailInfo[2]);
    $('#zone_address').html(detailInfo[3]);
    $('#zone_abstract').html(detailInfo[4]);
    layer.open({
        type: 1,					//  页面层
        title: false,				//	不显示标题栏
        area: ['600px','560px'],
        shade: .6,					//	遮罩
        time: 0,					//  关闭自动关闭
        shadeClose: true,			//	遮罩控制关闭层
        closeBtn: false,			//	不显示关闭按钮
        shift: 1,					//	出现动画
        content: $(".desBox") 	//  加载主体内容
    });
    $('.desClose').on('click',function(){
        layer.closeAll(); 		// 关闭
    });
});