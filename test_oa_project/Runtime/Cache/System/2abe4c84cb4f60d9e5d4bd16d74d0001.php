<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
		<title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
		<link rel="stylesheet" href="/Public/css/common.css">
		<link rel="stylesheet" href="/Public/css/discountList.css">	
	</head>
	<body>
		<div class="wrapBox">
			<div class="regionCont">
				<div class="regionContTop clearfix">
					<div class="topTit l"><span class="masterList">优惠列表</span></div>
				</div>
				<div class="regionContMiddle">
					<dl class="regionTit  clearfix">
						<dt class="wOne regionSequence clearfix">
							<span>ID</span>
						</dt>
						<dt class="wTwo">优惠名称</dt>
						<dt class="wThr">优惠金额</dt>
						<dt class="wFou">优惠详情</dt>
						<dt class="wFiv">是否叠加</dt>
						<dt class="wSix">是否启用</dt>
						<dt class="wSev">操作</dt>
					</dl>
					
					<?php if(is_array($discountList)): foreach($discountList as $key=>$v1): ?><dl class="secondaryZoneRow clearfix">
						<dd class="wOne regionSequence clearfix">
							<?php echo ($v1['discount_id']); ?>
						</dd>
						<dd class="wTwo"><?php echo ($v1['dname']); ?></dd>
						<dd class="wThr"><?php echo ($v1['dmoney']); ?></dd>
						<dd class="wFou"><?php echo ($v1['remark']); ?></dd>
						<dd class="wFiv"><?php echo ($v1['type']); ?></dd>
						<dd class="wSix"><?php echo ($v1['type']); ?></dd>
						<dd class="wSev szOperation clearfix"  >
							<a href="javascript:;" class="regionSelect"><i></i></a>
							<div class="otherOperation">
								<div class="triangle"></div>
		                        <div class="otherIcon">
		                            <ul>
		                            	<li>
		                                	<!--<a href="<?php echo U('System/Zone/addZone',array('zone_id'=>$v1['zone_id']));?>" class="addPreferential">-->
		                                	<a href="javascript:;" class="addPreferential">
			                                    <span class="addZone add"></span>
			                                    <em>添加优惠</em>
			                                </a>    
		                                </li>
		                                <li>
			                            <a href="<?php echo U('System/Zone/editZone',array('zone_id'=>$v1['zone_id']));?>">
				                                    <span class="editZone modify"></span>
				                                    <em>修改</em>
				                                </a>    
			                                </li>
		                                <li onclick="delZone('<?php echo ($v1["zone_id"]); ?>')">
		                                	<a href="javascript:;">
			                                    <span class="delete"></span>
			                                    <em>删除</em>
			                                </a>    
		                                </li>
		                            </ul>
		                        </div>
		                    </div>
						</dd>
					</dl>
					<?php if($v1['sons']): if(is_array($v1['sons'])): foreach($v1['sons'] as $key=>$v2): ?><dl class="threeRegionsRow clearfix  pzone_<?php echo ($v1['discount_id']); ?>">
							<dd class="wOne regionSequence clearfix">
								<?php echo ($v2['discount_id']); ?>
							</dd>
							<dd class="wTwo trName"><span><?php echo ($v2['dname']); ?></span></dd>
							<dd class="wThr"><?php echo ($v2['dmoney']); ?></dd>
							<dd class="wFou"><?php echo ($v2['remark']); ?></dd>
							<dd class="wFiv"><?php echo ($v2['type']); ?></dd>
							<dd class="wFiv"><?php echo ($v2['type']); ?></dd> 
							<dd class="wSev szOperation clearfix">
								<a href="javascript:;" class="regionSelect"><i></i></a>
								<div class="otherOperation">
									<div class="triangle"></div>
			                        <div class="otherIcon">
			                            <ul>
		                                <li>
		                                	<a href="<?php echo U('System/Zone/editZone',array('zone_id'=>$v2['zone_id']));?>">
			                                    <span class="editZone modify"></span>
			                                    <em>修改</em>
			                                </a>    
		                                </li>
		                                <li onclick="delZone('<?php echo ($v2["zone_id"]); ?>')">
	                                		<a href="javascript:;">
			                                    <span class="delete"></span>
			                                    <em>删除</em>
			                                </a>    
		                                </li>
			                            </ul>
			                        </div>
			                    </div>
							</dd>
						</dl><?php endforeach; endif; endif; endforeach; endif; ?>
				</div>
			</div>
		</div>
		
		<!--  添加弹窗 S  -->
		<div class="preBox dn">
			<div class="preCont">
				<div class="preRows clearfix">
					<span>优惠名称：</span>
					<input type="text" class="preInt">
				</div>
				<div class="preRows clearfix">
					<span>优惠名称：</span>
					<input type="text" class="preInt">
				</div>
				<div class="preRows clearfix">
					<span>优惠名称：</span>
					<input type="text" class="preInt">
				</div>
				<div class="preRows clearfix">
					<span>优惠名称：</span>
					<input type="text" class="preInt">
				</div>
				<div class="preRows clearfix">
					<span>优惠名称：</span>
					<input type="text" class="preInt">
				</div>
				<div class="preRows clearfix">
					<span>优惠名称：</span>
					<select class="preSelect">
						<option value="0" selected="selected">--请选择--</option>
						<option value="1">优惠一</option>
						<option value="2">优惠二</option>
						<option value="3">优惠三</option>
						<option value="4">优惠四</option>
					</select>
				</div>
				<div class="preRows clearfix">
					<div class="preBtns">
						<input type="button" class="preConfirm" value="确定">
						<input type="button" class="preCancel" value="取消">
					</div>
				</div>
			</div>
		</div>
		<!--  添加弹窗 E  -->
		
		
		<script src="/Public/js/jquery-1.9.1.min.js"></script>
		<script src="/Public/js/layer/layer.js"></script>
		<script src="/Public/js/regionlManagement.js"></script>
		<script src="/Public/js/discountList.js"></script>
		<script>
			
		    function delZone(zone_id){
		        layer.confirm('确定要删除该区域？', {
		            btn: ['确定','取消'] //按钮
		        }, function(){
		            var data = {
		                zone_id:zone_id,
		                type:'del'
		            };
		            common_ajax(data,'<?php echo ($urlDelZone); ?>','reload');
		        }, function(){});
		    }
    
		</script>
		<script src="/Public/js/common_ajax.js"></script>
	</body>
</html>