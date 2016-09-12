<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
		<title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
		<link rel="stylesheet" href="/Public/css/common.css">
		<link rel="stylesheet" href="/Public/css/external.min.css">
		<link rel="stylesheet" href="/Public/css/forecastManagement.css">
	</head>
	<body>
		<div class="wrapBox">
			<div class="imCont">
				<div class="imContTop clearfix">
					<div class="topTit l">预报管理 </div>
				</div>

				<div class="forCondition clearfix">
					<form action="<?php echo U('System/Fee/signing');?>"  method="get">
						<select class="forWay" name="receivetype">
							<option value="0" <?php echo $request['receivetype']=='0'?'selected="true"':'';?>>请选择收款方式 </option>
							<!--<option value="1" <?php echo $request['receivetype']=='1'?'selected="true"':'';?>>现金</option>
							<option value="2" <?php echo $request['receivetype']=='2'?'selected="true"':'';?>>刷卡</option>
							<option value="3" <?php echo $request['receivetype']=='3'?'selected="true"':'';?>>转账</option>
							<option value="4" <?php echo $request['receivetype']=='4'?'selected="true"':'';?>>微信</option>
							<option value="5" <?php echo $request['receivetype']=='5'?'selected="true"':'';?>>支付宝</option>-->
							<?php if(is_array($receiveType)): foreach($receiveType as $k=>$receiveType_item): ?><option value="<?php echo ($k); ?>" <?php echo $request['receivetype']==$k?'selected="true"':'';?>><?php echo ($receiveType_item['text']); ?></option><?php endforeach; endif; ?>
						</select>
						<select class="forUser" name="key_name">
							<option value="qq" <?php echo $request['key_name']=='qq'?'selected="true"':'';?>>QQ</option>
							<option value="username" <?php echo $request['key_name']=='username'?'selected="true"':'';?>>手机号码</option>
							<option value="tel" <?php echo $request['key_name']=='tel'?'selected="true"':'';?>>固定电话</option>
							<option value="realname" <?php echo $request['key_name']=='realname'?'selected="true"':'';?>>真实姓名</option>
						</select>

						<input type="text" class="viInp l" name="key_value" placeholder="请输入相关关键字">
						<input type="submit" class="viSearchBtn l" value="搜索">
					</form>
				</div>
				
				<div class="forMiddle">
					<table cellpadding="0" cellspacing="0" id="forTable">
						<tr class="forHeader">
							<th class="optionsTh">真实姓名</th>
							<th>手机号码</th>
							<th>QQ</th>
							<th>固定电话</th>
							<th>所属人</th>
							<th>预报金额</th>
							<th>预报时间</th>
							<th>审核状态</th>
							<th class="operatingTh">操作</th>
						</tr>

						<?php if(is_array($signingUsers)): foreach($signingUsers as $k=>$signing_item): ?><tr>
								<td class="optionsThTd"><?php echo ($signing_item["realname"]); ?></td>
								<td><?php echo ($signing_item["username"]); ?></td>
								<td><?php echo ($signing_item["qq"]); ?></td>
								<td><?php echo ($signing_item["tel"]); ?></td>
								<td><?php echo ($signing_item["apply_realname"]); ?></td>
								<td><?php echo ($signing_item["pay"]); ?></td>
								<td><?php echo ($signing_item["receivetime"]); ?></td>
								<td><?php echo ($signing_item["describe_status"]); ?></td>
								<td>
									<?php if(($signing_item[describe_status] != 已退款)): ?><a href="javascript:;" class="forSelect"><i></i></a>
										<div class="otherOperation">
											<div class="triangle"></div>
											<div class="otherIcon">
												<ul>
													<?php if(($signing_item[describe_status] == 等待审核)): ?><li>
															<a href="javascript:;" class="receivablesBtn" data-value="<?php echo ($signing_item["fee_logs_id"]); ?>::<?php echo ($signing_item["realname"]); ?>::<?php echo ($signing_item["receivetypeTx"]); ?>::<?php echo ($signing_item["pay"]); ?>">
																<span class="confirmPayment"></span>
																<em>确认收款</em>
															</a>
														</li>
														<li>
															<a href="javascript:;" class="returnBtn" data-value="<?php echo ($signing_item["fee_logs_id"]); ?>::<?php echo ($signing_item["realname"]); ?>::<?php echo ($signing_item["receivetypeTx"]); ?>::<?php echo ($signing_item["pay"]); ?>">
																<span class="return"></span>
																<em>退回申请</em>
															</a>
														</li><?php endif; ?>
													<?php if(($signing_item[describe_status] == 审核通过)): ?><li>
															<a href="javascript:;" class="refundBtn" data-value="<?php echo ($signing_item["fee_logs_id"]); ?>::<?php echo ($signing_item["realname"]); ?>::<?php echo ($signing_item["receivetypeTx"]); ?>::<?php echo ($signing_item["pay"]); ?>">
																<span class="refund"></span>
																<em>确认退款</em>
															</a>
														</li><?php endif; ?>
												</ul>
											</div>
										</div>
										<a  href="<?php echo U('System/User/detailUser', array('id' => $signing_item['user_id']));?>"  class="detailLink" target="_blank">详情</a><?php endif; ?>
								</td>
							</tr><?php endforeach; endif; ?>
					</table>
				</div>
				
				<div class="clearfix">
		            <div class="collegaPage">
		                <?php echo ($paging); ?>
		            </div>
		        </div>
			</div>
		</div>
		
		<div class="dn">
			<!-- 确定收款 s -->
			<div class="receivablesBox popup" id="receivables">
				<div class="reRow clearfix">
					<span>真实姓名：</span>
					<em></em>
				</div>
				<div class="reRow clearfix">
					<span>收款方式：</span>
					<em></em>
				</div>
				<div class="reRow clearfix">
					<span>预报金额：</span>
					<em></em>
				</div>
				<div class="reBtnBox clearfix">
					<input type="button" class="reConfirm"  value="确认收款"  >
					<input type="button" class="reCencel" value="取消">
				</div>
			</div>
			<!-- 确定收款 e -->
			
			<!-- 退回申请 s -->
			<div class="returnBox popup" id="returnApplication">
				<div class="raRow clearfix">
					<span>真实姓名：</span>
					<em></em>
				</div>
				<div class="raRow clearfix">
					<span>收款方式：</span>
					<em></em>
				</div>
				<div class="raRow clearfix">
					<span>预报金额：</span>
					<em></em>
				</div>
				<div class="raBtnBox clearfix">
					<input type="button" class="raConfirm" value="确认退回" >
					<input type="button" class="raCencel" value="取消">
				</div>
			</div>
			<!-- 退回申请 e -->

			<!-- 退回预报款项 s -->
			<div class="refundBox popup" id="refundPay">
				<div class="refundRow clearfix">
					<span>真实姓名：</span>
					<em></em>
				</div>
				<div class="refundRow clearfix">
					<span>收款方式：</span>
					<em></em>
				</div>
				<div class="refundRow clearfix">
					<span>预报金额：</span>
					<em></em>
				</div>
				<div class="refundRow clearfix">
					<span><i>&#42</i>退款方式：</span>
					<select class="refundSelect" id="refundSelect">
						<option value="0">退款方式</option>
						<!--<option value="1">现金</option>
						<option value="2">刷卡</option>
						<option value="3">转账</option>
						<option value="4">微信</option>
						<option value="5">支付宝</option>-->
						<?php if(is_array($receiveType)): foreach($receiveType as $k=>$receiveType_item): ?><option value="<?php echo ($k); ?>"><?php echo ($receiveType_item['text']); ?></option><?php endforeach; endif; ?>
					</select>
				</div>
				<div class="refundRow clearfix">
					<span><i>&#42</i>退款金额：</span>
					<input type="text" class="refundAmountInp" name="refundAmount">
				</div>
				<div class="refundBtnBox clearfix">
					<input type="button" class="refundConfirm" value="提交">
				</div>
			</div>
			<!-- 退回预报款项 e -->
		</div>


		<script src="/Public/js/jquery-1.9.1.min.js"></script>
		<script src="/Public/js/jquery.lib.min.js"></script>
		<script src="/Public/js/placeholder.js"></script>
		<script src="/Public/js/forecastManagement.js"></script>
		<script src="/Public/js/layer/layer.js"></script>
		<script src="/Public/js/common_ajax.js"></script>

		<script>
			//确认预报申请
			$('.reConfirm').on('click',function(){
				var data = {
					feeLogId: fee_logs_id,
				};
				common_ajax(data,"<?php echo U('System/Fee/signingPay');?>",'loca');
				$("#receivables").colorbox.close();
			});
			//退回预报申请
			$('.raConfirm').on('click',function(){
				var data = {
					feeLogId: fee_logs_id,
				};
				common_ajax(data,"<?php echo U('System/Fee/signingFailure');?>",'loca');

				$("#receivables").colorbox.close();
			});

			//退回预报款项
			$('.refundConfirm').on('click',function(){
				var refundAmount = parseInt($.trim($(':input[name="refundAmount"]').val()));
				var patrn=/^[1-9]{1}[0-9]{2,3}$/;  //只能输入以1-9开头的正整数,最多是4位数,预报金额是大于100小于3000的数额
				var  forecasePay = parseInt($.trim($("#refundPay .refundRow").eq(2).children('em').text()));
				if(!patrn.exec(refundAmount)) {
					layer.msg('退款金额格式不正确,请输入4位以内非0开头的整数',{icon:2});
					return;
				}
				if(refundAmount>forecasePay){
					layer.msg('退款金额不能大于预报金额,请重新输入',{icon:2});
					return;
				}
				var data = {
					feeLogId: fee_logs_id,
					pay: refundAmount,
					receivetype: $("#refundSelect  option:selected").attr('value')
				};
				common_ajax(data,"<?php echo U('System/Fee/signingRefund');?>",'loca');

				$("#receivables").colorbox.close();
			});
		</script>

	</body>
</html>