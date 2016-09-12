<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
    <link rel="stylesheet" href="/Public/css/common.css">
    <link rel="stylesheet" href="/Public/css/external.min.css">
    <link rel="stylesheet" href="/Public/js/glDatePicker/glDatePicker.default.css">
    <link rel="stylesheet" href="/Public/css/orderManagement.css">
    <script src="/Public/js/jquery-1.9.1.min.js"></script>
    <script>
        var local_href = window.location.href;
        var auditOrder_href = "<?php echo U('System/Order/auditingOrder');?>";
        var payfundOrder_href = "<?php echo U('System/Order/payfund');?>";
        var refundOrder_href = "<?php echo U('System/Order/refund');?>";
        $(function(){
            getsystem();
        });
    </script>
</head>
<body>
<div class="wrapBox">
    <div class="imCont">
        <div class="imContTop clearfix">
            <div class="topTit l">订单管理 </div>
            <div class="topRight r">
                <a href="javascript:;" class="addAccount">导出订单</a>
            </div>
        </div>

        <div class="p clearfix">筛选条件:</div>
        
        <div class="Filter" id="Filter">
            <div class="frame">
                <div class="details2 clearfix">
                    <span>订单状态<i>（可多选）</i>：</span>
                    <ul class="clearfix" id="order_status">
                        <?php if(is_array($data['order_status'])): foreach($data['order_status'] as $k=>$v): ?><li><a href="javascript:;" class="on_hover" data-value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></a></li><?php endforeach; endif; ?>
                    </ul>
                </div>
                <div class="details clearfix">
                    <span>付款类型：</span>
                    <ul id="order_type">
                        <li><a href="javascript:;" class="on_hover">全部</a></li>
                        <?php if(is_array($data['order_loan_institutions'])): foreach($data['order_loan_institutions'] as $k=>$v): ?><li><a href="javascript:;" data-value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></a></li><?php endforeach; endif; ?>
                    </ul>
                </div>
                <div class="details  clearfix">
                    <span>创建时间：</span>
                    <div class="createdTimeBox clearfix">
                        <input type="text" class="createdTime" name="order_createtimeS" value="" placeholder="选择日期">
                        <em>至</em>
                        <input type="text" class="createdTime" name="order_createtimeE" value="" placeholder="选择日期">
                    </div>
                </div>
                <div class="details clearfix">
                    <span>完成时间：</span>
                    <div class="completeTimeBox clearfix">
                        <input type="text" class="completeTime" name="order_finishtimeS" value="" placeholder="选择日期">
                        <em>至</em>
                        <input type="text" class="completeTime" name="order_finishtimeE" value="" placeholder="选择日期">
                    </div>
                </div>
                <div class="details clearfix">
                    <span>区域：</span>
                    <div class="selectbox l">
                        <dl class="select" id="zone_sele">
                            <dt>
                            <div class="select_title l">全部区域</div>
                            <div class="arrow r"></div>
                            </dt>
                            <?php if(is_array($data['zoneAll'])): foreach($data['zoneAll'] as $k1=>$v1): ?><dd class="fxDone" data-value="<?php echo ($v1['zone_id']); ?>"><?php echo ($v1['name']); ?></dd>
                                <?php if(($v1['centersign'] != 10) && (!empty($v1['children']))): if(is_array($v1['children'])): foreach($v1['children'] as $key=>$v2): ?><dd class="fxDone" data-value="<?php echo ($v2['zone_id']); ?>">&nbsp;&nbsp;├─ <?php echo ($v2['name']); ?></dd>
                                        <?php if(($v2['centersign'] != 10) && (!empty($v2['children']))): if(is_array($v2['children'])): foreach($v2['children'] as $key=>$v3): ?><dd class="fxDone" data-value="<?php echo ($v3['zone_id']); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─ <?php echo ($v3['name']); ?></dd>
                                                <?php foreach($v3['children'] as $v4){ ?>
                                                <dd class="fxDone" data-value="<?php echo ($v4['zone_id']); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─ <?php echo ($v4['name']); ?></dd>
                                                <?php } endforeach; endif; endif; endforeach; endif; endif; endforeach; endif; ?>
                        </dl>
                        <input type="hidden" name="order_zone_id" aotucomplete="off">
                    </div>
                </div>
                <div class="details clearfix">
                    <span>职位：</span>
                    <div class="selectbox l">
                        <dl class="select" id="role_sele">
                            <dt>
                            <div class="select_title l">全部职位</div>
                            <div class="arrow r"></div>
                            </dt>
                            <dd class="fxDone" data-value="0">全部职位</dd>
                            <?php if(is_array($data['roleAll']['data'])): foreach($data['roleAll']['data'] as $key=>$v): ?><dd class="fxDone" data-value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?></dd><?php endforeach; endif; ?>
                        </dl>
                        <input type="hidden" name="order_role_id" aotucomplete="off">
                    </div>
                </div>
                <div class="details clearfix">
                    <span>所属人：</span>
                    <div class="selectbox l">
                        <dl class="select" id="system_sele">
                            <dt>
                            <div class="select_title l">请选择所属人</div>
                            <div class="arrow r"></div>
                            </dt>
                        </dl>
                        <input type="hidden" name="order_system_user_id" aotucomplete="off">
                    </div>
                    <a href="javascript:void(0);" id="system_sele_loading" style="display: none"><img src="/Public/images/loading.gif"></a>
                </div>
                <div class="details clearfix">
                	<span>&nbsp;</span>
                	<form action="<?php echo U('System/Order/orderList');?>#pjax_body" id="subForm" onsubmit="return false;" method="get">
			            <input type="hidden" name="status" value="" autocomplete="off">
			            <input type="hidden" name="loan_institutions_id" autocomplete="off">
			            <input type="hidden" name="createtime" value="" autocomplete="off">
			            <input type="hidden" name="finishtime" value="" autocomplete="off">
			            <input type="hidden" name="zone_id" value="" autocomplete="off">
			            <input type="hidden" name="role_id" value="" autocomplete="off">
			            <input type="hidden" name="system_user_id" value="" autocomplete="off">
			            <input type="hidden" name="role_id" value="" autocomplete="off">
			            <input type="submit" class="confirmBtn" id="subSearch" value="确定">
			        </form>
                </div>

            </div>

            <div class="frame1">
                <ul>
                    <li><a href="javascript:;">订单状态<i>（可多选）</i>：待审核 审核失败 审核通过 交易中 完成交易 部分退费 全额退款 </a></li>
                    <!--<li><a href="javascript:;">付款类型：全部</a></li>-->
                    <!--<li><a href="javascript:;">创建时间： 全部</a></li>-->
                    <!--<li><a href="javascript:;">完成时间： 全部</a></li>-->
                    <!--<li><a href="javascript:;">区域：全部区域</a></li>-->
                    <!--<li><a href="javascript:;">职位：全部职位</a></li>-->
                    <!--<li><a href="javascript:;">所属人：全部所属人</a></li>-->
                </ul>
            </div>
        </div>

        <div class="arrowFather1">
            <div class="arrow1">
                <span>展开筛选</span>
                <i class="showIcon"></i>
            </div>
        </div>

        <div class="arrowFather">
            <div class="arrow">
                <span>收起筛选</span>
                <i class="hideIcon"></i>
            </div>
        </div>

        <div class="forCondition clearfix">
            <form action="<?php echo U('System/User/orderList');?>" method="get">
                <div class="selectbox l">
                    <dl class="select">
                        <dt>
                        <div class="select_title l"><?php echo $data['request']['key_name']=='realname'?'真实姓名':($data['request']['key_name']=='username'?'手机号':($data['request']['key_name']=='tel'?'固定电话':($data['request']['key_name']=='qq'?'QQ':'请选择搜索词')));?></div>
                        <div class="arrow r"></div>
                        </dt>
                        <dd class="fxDone" >请选择搜索词</dd>
                        <dd class="fxDone" data-value="realname">真实姓名</dd>
                        <dd class="fxDone" data-value="username">手机号</dd>
                        <dd class="fxDone" data-value="tel">固定电话</dd>
                        <dd class="fxDone" data-value="qq">QQ</dd>
                    </dl>
                    <input type="text" name="key_name" value="<?php echo ($data['request']['key_name']); ?>" autocomplete='off'/>
                </div>
                <input type="text" class="viInp l" name="key_value" placeholder="<?php echo ($data['request']['key_value']); ?>" placeholder="请输入关键词">
                <input type="submit" class="viSearchBtn l" value="搜索">
            </form>
        </div>


        <div id="pjax_body">
            <div class="forMiddle">
                <table cellpadding="0" cellspacing="0" id="forTable">
                    <tr class="forHeader">
                        <th class="optionsTh">真实姓名</th>
                        <th>手机号码</th>
                        <th>所属人</th>
                        <th>订金</th>
                        <th>学费总额</th>
                        <th>优惠金额</th>
                        <th>实际学费</th>
                        <th>实际缴费</th>
                        <th>欠费总额</th>
                        <th>付款类型</th>
                        <th>货款金额</th>
                        <th>创建时间</th>
                        <th>完成时间</th>
                        <th>订单状态</th>
                        <th class="operatingTh">操作</th>
                    </tr>
                    <?php if(is_array($data['order_list'])): foreach($data['order_list'] as $k=>$v): ?><tr class="content_li">
                            <td class="optionsThTd"><?php echo ($v["user_realname"]); ?></td>
                            <td><?php echo !empty($v['mobile'])?$v['mobile']:'--';?></td>
                            <td><?php echo ($v["system_user_realname"]); ?></td>
                            <td><?php echo ($v['subscription']!='0.00')?$v['subscription']:'--';?></td>
                            <td><?php echo ($v['coursecost']!='0.00')?$v['coursecost']:'--';?></td>
                            <td><?php echo ($v['discountcost']!='0.00')?$v['discountcost']:'--';?></td>
                            <td><?php echo ($v['paycount']!='0.00')?$v['paycount']:'--';?></td>
                            <td><?php echo ($v['cost']!='0.00')?$v['cost']:'--';?></td>
                            <td><?php echo ($v['sparecost']!='0.00')?$v['sparecost']:'--';?></td>
                            <td><?php echo ($v['status']>=40)?$v['loan_institutions_name']:'--';?></td>
                            <td><?php echo ($v['loan_institutions_cost']!='0.00')?$v['loan_institutions_cost']:'--';?></td>
                            <td><?php echo date('Y-m-d', $v['createtime']);?></td>
                            <td><?php echo !empty($v['finishtime'])?date('Y-m-d', $v['finishtime']):'--';?></td>
                            <td><?php echo ($v['status_name']); ?></td>
                            <td data-id="<?php echo ($v['order_id']); ?>" data-value="<?php echo ($v["user_realname"]); ?>-<?php echo !empty($v['username'])?decryptPhone($v['username'], C('PHONE_CODE_KEY')):'';?>-<?php echo ($v["system_user_realname"]); ?>-<?php echo ($data['order_receivetype'][$v['payway']]['text']); ?>-<?php echo ($v["subscription"]); ?>-<?php echo ($v["cost"]); ?>">
                                <span><a class="dn" href="<?php echo U('System/User/detailUser',array('id'=>$v['user_id']));?>" >详情</a></span>
                                <a class="hrefDetail dn" href="<?php echo U('System/User/detailUser',array('id'=>$v['user_id']));?>" target="_blank" style="color: #009dda;line-height:40px;">详情</a>
                            </td>
                        </tr><?php endforeach; endif; ?>

                </table>
            </div>

            <div class="clearfix">
                <div class="collegaPage" id="paging">
                    <!--<?php echo ($data['paging']); ?>-->
                </div>
            </div>
            <input type="hidden" name="order_id" value="" autocomplete="off">
            <script>
                $(function(){
                    getpaging("<?php echo ($data['request']['page'])?$data['request']['page']:1;?>");
                });
            </script>
        </div>
    </div>
</div>

<div class="dn">
    <!-- 收款 s -->
    <div class="receivablesBox popup">
        <div class="reRow clearfix add">
            <span>真实姓名：</span>
            <em>--</em>
        </div>
        <div class="reRow clearfix add">
            <span>手机号码：</span>
            <em>--</em>
        </div>
        <div class="reRow clearfix add">
            <span>所属人：</span>
            <em>--</em>
        </div>
        <div class="reRow clearfix">
            <span><i>&#42</i>收款日期：</span>
            <input type="text" class="receivablesTime" name="receivables_practicaltime" value="" placeholder="选择日期">
        </div>
        <div class="reRow clearfix">
            <span><i>&#42</i>收款方式：</span>
            <div class="selectbox l">
                <dl class="select">
                    <dt>
                    <div class="select_title l">选择方式</div>
                    <div class="arrow r"></div>
                    </dt>
                    <dd class="fxDone" data-value="0">选择方式</dd>
                    <?php if(is_array($data['order_receivetype'])): foreach($data['order_receivetype'] as $k=>$v): ?><dd class="fxDone" data-value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></dd><?php endforeach; endif; ?>
                </dl>
                <input type="hidden" name="receivables_payway">
            </div>
        </div>
        <div class="reRow clearfix">
            <span><i>&#42</i>收款金额：</span>
            <input type="text" class="receivablesInp" name="receivables_cost">
        </div>
        <div class="reRow clearfix">
            <span>&nbsp;</span>
            <input type="button" class="receivablesConfirm" id="receivablesSub" value="提交">
        </div>
    </div>
    <!-- 收款 e -->

    <!-- 退款 s -->
    <div class="returnBox popup">
        <div class="raRow clearfix add">
            <span>真实姓名：</span>
            <em>--</em>
        </div>
        <div class="raRow clearfix add">
            <span>手机号码：</span>
            <em>--</em>
        </div>
        <div class="raRow clearfix add">
            <span>所属人：</span>
            <em>--</em>
        </div>
        <div class="raRow clearfix add dn">
            <span>：</span>
            <em>--</em>
        </div>
        <div class="raRow clearfix add dn">
            <span>：</span>
            <em>--</em>
        </div>
        <div class="raRow clearfix add">
            <span>实际缴费：</span>
            <em>--</em>
        </div>
        <div class="raRow clearfix">
            <span><i>&#42</i>退款日期：</span>
            <input type="text" class="refundTime" name="return_practicaltime" value="" placeholder="选择日期">
        </div>
        <div class="raRow clearfix">
            <span><i>&#42</i>退款方式：</span>
            <div class="selectbox l">
                <dl class="select">
                    <dt>
                    <div class="select_title l">选择方式</div>
                    <div class="arrow r"></div>
                    </dt>
                    <dd class="fxDone" data-value="0">选择方式</dd>
                    <?php if(is_array($data['order_receivetype'])): foreach($data['order_receivetype'] as $k=>$v): if($v['num'] != 10): ?><dd class="fxDone" data-value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></dd><?php endif; endforeach; endif; ?>
                </dl>
                <input type="hidden" name="return_payway">
            </div>
        </div>
        <div class="raRow clearfix">
            <span><i>&#42</i>退款金额：</span>
            <input type="text" class="raRefundInp" name="return_cost">
        </div>
        <div class="raRow clearfix">
            <span>&nbsp;</span>
            <input type="button" class="raConfirm" id="returnSub" value="提交">
        </div>
    </div>
    <!-- 退款 e -->

    <!-- 审核 s -->
    <div class="auditOrderBox popup">
        <div class="aoRow clearfix add">
            <span>真实姓名：</span>
            <em>--</em>
        </div>
        <div class="aoRow clearfix add">
            <span>手机号码：</span>
            <em>--</em>
        </div>
        <div class="aoRow clearfix add">
            <span>所属人：</span>
            <em>--</em>
        </div>
        <div class="aoRow clearfix">
            <span><i>&#42</i>收款日期：</span>
            <input type="text" class="auditOrderTime" name="audit_practicaltime" value="" placeholder="选择日期">
        </div>
        <div class="aoRow clearfix add">
            <span><i>&#42</i>收款方式：</span>
            <em>--</em>
        </div>
        <div class="aoRow clearfix add">
            <span><i>&#42</i>收款金额：</span>
            <em>--</em>
        </div>
        <div class="aoRow toExamineBox clearfix">
            <input type="button" class="auditPass" value="审核通过">
            <input type="button" class="auditNotPassed" value="审核不通过">
        </div>
    </div>
    <!-- 审核 e -->

    <!-- 退订金 s -->
    <div class="depositBox popup">
        <div class="raRow clearfix add">
            <span>真实姓名：</span>
            <em>--</em>
        </div>
        <div class="raRow clearfix add">
            <span>手机号码：</span>
            <em>--</em>
        </div>
        <div class="raRow clearfix add">
            <span>所属人：</span>
            <em>--</em>
        </div>
        <div class="aoRow clearfix add" style="display: none">
            <span><i>&#42</i>收款方式：</span>
            <em>--</em>
        </div>
        <div class="raRow clearfix add">
            <span>订金：</span>
            <em id="deposit_cost">--</em>
        </div>
        <div class="raRow clearfix">
            <span><i>&#42</i>退款日期：</span>
            <input type="text" class="depositBoxTime" name="deposit_practicaltime" value="" placeholder="选择日期">
        </div>
        <div class="raRow clearfix">
            <span><i>&#42</i>退款方式：</span>
            <div class="selectbox l">
                <dl class="select">
                    <dt>
                    <div class="select_title l">选择方式</div>
                    <div class="arrow r"></div>
                    </dt>
                    <dd class="fxDone" data-value="0">选择方式</dd>
                    <?php if(is_array($data['order_receivetype'])): foreach($data['order_receivetype'] as $k=>$v): if($v['num'] != 10): ?><dd class="fxDone" data-value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></dd><?php endif; endforeach; endif; ?>
                </dl>
                <input type="hidden" name="deposit_payway">
            </div>
        </div>
        <div class="raRow clearfix">
            <span>&nbsp;</span>
            <input type="button" class="raConfirm" id="depositSub" value="提交">
        </div>
    </div>
    <!-- 退订金 e -->
</div>

<!--双击-->
<form id="hrefForm" action='' method="get"  target="_blank" >
</form>

<script src="/Public/js/jquery.lib.min.js"></script>
<script src="/Public/js/placeholder.js"></script>
<script src="/Public/js/glDatePicker/glDatePicker.js"></script>
<script src="/Public/js/layer/layer.js"></script>
<script src="/Public/js/orderManagement.js"></script>
<script src="/Public/js/common_ajax.js"></script>
<script src="/Public/js/orderList.js"></script>

<link rel="stylesheet" href="/Public/js/pjax/css/nprogress.css">
<script type="text/javascript" src="/Public/js/pjax/js/jquery.pjax.js"></script>
<script type="text/javascript" src="/Public/js/pjax/js/nprogress.js"></script>

</body>
</html>