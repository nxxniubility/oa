//  操作弹框的点击事件

$(document).on('click', '.regionSelect',
    function(event){
    $(this).next().toggle().closest('dl').siblings().find('.otherOperation').hide();
    //阻止点击document对当前事件的影响
    event.stopPropagation();
});

 $(document).click(function() {
	$('.otherOperation').hide();
});


//  显示一级区域
$(document).on('click', '.switchBtnShow', function(){
	var parClick = $(this).parents('.osOperation');
	var os = parClick.closest('dl.oneRegions');
	var	pzone_id=$(this).attr('zone-id');
	var szc= os.nextAll('.pzone_'+pzone_id);
	szc.each(function (index,element){
		if($(this).is(':hidden')){
			$(this).removeClass('dn');
			$(this).addClass('switchBtnHide');
			$(this).attr('title','收起');
		}else {
			$(this).addClass('dn');
			$(this).removeClass('switchBtnHide');
			$(this).attr('title','展开');
		}
	})
	
});


//  显示二级区域
$(document).on('click', '.switchBtnShow', function(){
	var parentclick = $(this).parents('.szOperation');
	var sz = parentclick.closest('dl.secondaryZoneRow');
	var pzone_id = $(this).attr('zone-id');
	var tr= sz.nextAll('.pzone_'+pzone_id);
	var _this = $(this);
	tr.each(function (index,element){
		if($(this).is(':hidden')){
			$(this).removeClass('dn');
			_this.addClass('switchBtnHide');
			_this.attr('title','收起');
			$('.trOperation').find('.switchBtnShow').removeClass('switchBtnHide');
		}else {
			$(this).addClass('dn');
			_this.removeClass('switchBtnHide');
			_this.attr('title','展开');
			//一级区域关闭同时关闭二级和三级
			$(".regionalCenter").addClass('dn');

		}
	})
	
});


//  显示中心区域
$(document).on('click', '.switchBtnShow', function(){
	var pclick = $(this).parents('.trOperation');
	var trs = pclick.closest('.threeRegionsRow');
	var pzone_id=$(this).attr('zone-id');
	var rc = trs.nextAll('.pzone_'+pzone_id);
	var _this = $(this);
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


$(document).on('click','.seeDetails',function(){
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
		area: ['600px','auto'],	
		shade: .6,					//	遮罩
		time: 0,					//  关闭自动关闭
		shadeClose: true,			//	遮罩控制关闭层
		closeBtn: false,			//	不显示关闭按钮
		shift: 1,					//	出现动画
		content: $(".desBox") 	//  加载主体内容
	});
	$(document).on('click','.desClose',function(){
		layer.closeAll(); 		// 关闭
	});

});

$(function(){
	getAjax();
	//获取列表内容
	function getAjax(){
		var data = {

		};
		common_ajax2(data,'/SystemApi/Zone/getZoneList','no',_setHtml,1);
		function _setHtml(redata){
			layui.use('laytpl', function(){
				var laytpl = layui.laytpl;
				laytpl(demo.innerHTML).render(redata.data, function(result){
					$('.regionContMiddle').html(result);
				});
			});
		};
	};

});

function delZone(zone_id){
    layer.confirm('确定要删除该区域？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        var data = {
            zone_id:zone_id,
        };
        common_ajax2(data,'/SystemApi/Zone/delZone','reload');
    }, function(){

    });
}