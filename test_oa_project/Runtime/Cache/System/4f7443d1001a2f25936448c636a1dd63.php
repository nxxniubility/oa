<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" >
    <title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
    <link rel="stylesheet" href="/Public/css/common.css">
    <link rel="stylesheet" href="/Public/css/external.min.css">
    <link rel="stylesheet" href="/Public/js/glDatePicker/glDatePicker.default.css">
    <link rel="stylesheet" href="/Public/css/paymentInformation.css">
    <script>
        var local_href = window.location.href;
        var payfundOrder_href = "<?php echo U('System/Order/payfund');?>";
        var orderList_href = "<?php echo U('System/Order/orderList');?>";
    </script>
</head>
<body>
<div class="wrapBox">
    <div class="imCont">
        <div class="imContTop clearfix">
            <div class="topTit l">
                <span class="masterList">订单管理</span>
                <span><em>&gt;</em>设置缴费信息</span>
            </div>
            <div class="topRight r">
                <a href="javascript:history.go(-1);" class="addAccount">返回</a>
            </div>
        </div>
    </div>
    <div class="payInfoMain">
        <div class="payRow clearfix">
            <div class="payRowLeft">真实姓名：</div>
            <div class="payRowRight"><?php echo ($data["info"]["orderInfo"]["user_realname"]); ?></div>
        </div>
        <div class="payRow clearfix">
            <div class="payRowLeft">手机号：</div>
            <div class="payRowRight"><?php echo !empty($data['info']['orderInfo']['username'])?decryptPhone($data['info']['orderInfo']['username'], C('PHONE_CODE_KEY')):'--';?></div>
        </div>
        <div class="payRow clearfix">
            <div class="payRowLeft">所属人：</div>
            <div class="payRowRight"><?php echo ($data["info"]["orderInfo"]["system_user_realname"]); ?></div>
        </div>
        <div class="payRow clearfix" id="paymentCourse">
            <div class="payRowLeft"><i>&#42</i>进班课程：</div>
            <div class="payRowRight clearfix">
                <div class="selectbox l">
                    <dl class="select">
                        <dt>
                        <div class="select_title l">选择课程</div>
                        <div class="arrow r"></div>
                        </dt>
                        <?php if(is_array($data['courseList'])): foreach($data['courseList'] as $key=>$v): ?><dd class="fxDone" data-value="<?php echo ($v["course_id"]); ?>" data-price="<?php echo ($v["price"]); ?>"><?php echo ($v["coursename"]); ?></dd><?php endforeach; endif; ?>
                    </dl>
                    <input type="hidden" name="course_id">
                </div>
            </div>
        </div>
        <div class="payRow dn clearfix">
            <div class="payRowLeft">学费总额：</div>
            <div class="payRowRight price">￥19800.00</div>
        </div>
        <div class="payRow clearfix">
            <div class="payRowLeft"><i>&#42</i>学习方式：</div>
            <div class="payRowRight clearfix">
                <div class="selectbox l">
                    <dl class="select">
                        <dt>
                        <div class="select_title l">选择方式</div>
                        <div class="arrow r"></div>
                        </dt>
                        <?php if(is_array($data['order_studytype'])): foreach($data['order_studytype'] as $key=>$v): ?><dd class="fxDone" data-value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></dd><?php endforeach; endif; ?>
                    </dl>
                    <input type="hidden" name="studytype">
                </div>
            </div>
        </div>
        <div class="payRow clearfix" id="paymentType">
            <div class="payRowLeft"><i>&#42</i>付款类型：</div>
            <div class="payRowRight clearfix">
                <div class="selectbox l">
                    <dl class="select">
                        <dt>
                        <div class="select_title l">选择类型</div>
                        <div class="arrow r"></div>
                        </dt>
                        <?php if(is_array($data['order_loan_institutions'])): foreach($data['order_loan_institutions'] as $key=>$v): ?><dd class="fxDone" data-value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></dd><?php endforeach; endif; ?>
                    </dl>
                    <input type="hidden" name="loan_institutions_id">
                </div>
            </div>
        </div>
        <div class="payRow dn clearfix">
            <div class="payRowLeft"><i>&#42</i>货款金额：</div>
            <div class="payRowRight">
                <input type="text" class="payInfoInp" maxlength="6" name="loan_institutions_cost" />
            </div>
        </div>
        <div class="payRow clearfix">
            <div class="payRowLeft">优惠方式：</div>
            <div class="payRowRight">
                <input type="button" class="payPreferentialWay" maxlength="6" value="选择优惠" />
                <input type="hidden" name="discount_id" value="" autocomplete="off"/>
            </div>
        </div>
        <div class="payRow clearfix">
            <div class="payRowLeft">&nbsp;</div>
            <div class="payRowRight">
                <table id="discount_body" class="dn" cellpadding="0" cellspacing="0">
                    <tr>
                        <th>优惠项目</th>
                        <th>优惠金额</th>
                    </tr>
                </table>
                <input type="submit" class="paySubmit" value="提交" />
            </div>
        </div>
    </div>
</div>

<div class="dn">
    <input type="hidden" name="order_id" value="<?php echo ($data['order_id']); ?>" autocomplete="off">

    <!-- 设置优惠方式 S -->
    <div class="preferentialWayBox popup">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th>优惠分类</th>
                <th>优惠项目</th>
                <th>优惠金额</th>
                <th>说明</th>
                <th class="lastTh">操作</th>
            </tr>
            <?php if(is_array($data['discount'])): foreach($data['discount'] as $k=>$v): $current = current($v['children']); ?>
                <tr>
                    <td rowspan="<?php echo count($v['children']);?>"><?php echo ($v["dname"]); echo ($v['repeat']==1)?'（可叠加）':'（单选）';?></td>
                    <td class="name"><?php echo $current['dname'];?></td>
                    <td class="money" data-value="<?php echo $current['dmoney'];?>">¥<?php echo $current['dmoney'];?></td>
                    <td><?php echo $current['remark'];?></td>
                    <td>
                        <?php if($v['repeat'] == 1): ?><input type="checkbox" name="checkboxName<?php echo ($k); ?>" class="pwChk" value="<?php echo $current['discount_id'];?>" />
                            <?php else: ?>
                            <input type="radio" name="preferentialChk<?php echo ($k); ?>" class="pwChk" value="<?php echo $current['discount_id'];?>" /><?php endif; ?>
                    </td>
                </tr>
                <?php if(is_array($v['children'])): foreach($v['children'] as $k2=>$v2): if($k2!=(key($v['children'])-1)){ ?>
                        <tr>
                            <td class="name"><?php echo ($v2["dname"]); ?></td>
                            <td class="money" data-value="<?php echo ($v2["dmoney"]); ?>">¥<?php echo ($v2["dmoney"]); ?></td>
                            <td><?php echo ($v2["remark"]); ?></td>
                            <td>
                                <?php if($v['repeat'] == 1): ?><input type="checkbox" name="checkboxName<?php echo ($k); ?>" class="pwChk" value="<?php echo ($v2["discount_id"]); ?>" />
                                    <?php else: ?>
                                    <input type="radio" name="preferentialChk<?php echo ($k); ?>" class="pwChk" value="<?php echo ($v2["discount_id"]); ?>" /><?php endif; ?>
                            </td>
                        </tr>
                    <?php } endforeach; endif; endforeach; endif; ?>
        </table>
        <div class="pwBtnBox">
            <input type="button" class="pwConfirm discountSub true" value="确定" />
            <input type="button" class="notpreferential discountSub" value="无优惠" />
        </div>
    </div>
    <!-- 设置优惠方式 E -->

    <!-- 收款 s -->
    <div class="receivablesBox popup">
        <div class="reRow clearfix add">
            <span>真实姓名：</span>
            <em><?php echo ($data["info"]["orderInfo"]["user_realname"]); ?></em>
        </div>
        <div class="reRow clearfix add">
            <span>手机号码：</span>
            <em><?php echo !empty($data['info']['orderInfo']['username'])?decryptPhone($data['info']['orderInfo']['username'], C('PHONE_CODE_KEY')):'--';?></em>
        </div>
        <div class="reRow clearfix add">
            <span>所属人：</span>
            <em><?php echo ($data["info"]["orderInfo"]["system_user_realname"]); ?></em>
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

</div>


<script src="/Public/js/jquery-1.9.1.min.js"></script>
<script src="/Public/js/jquery.lib.min.js"></script>
<script src="/Public/js/glDatePicker/glDatePicker.js"></script>
<script src="/Public/js/placeholder.js"></script>
<script src="/Public/js/layer/layer.js"></script>
<script src="/Public/js/common_ajax.js"></script>
<script src="/Public/js/paymentInformation.js?v=201608051153"></script>
<script>
    var max_cost = "<?php echo C('ADMIN_USER_MAX_DISCOUNT');?>";
</script>
</body>
</html>