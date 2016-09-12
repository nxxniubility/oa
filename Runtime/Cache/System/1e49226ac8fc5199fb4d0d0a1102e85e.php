<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
    <title>客户详情-<?php echo $data['userInfo']['realname']?$data['userInfo']['realname']:'';?></title>
    <link rel="stylesheet" href="/Public/css/common.css">
    <link rel="stylesheet" href="/Public/js/glDatePicker/glDatePicker.default.css">
    <link rel="stylesheet" href="/Public/css/clientDetail.css?v=201609031">
    <link rel="stylesheet" href="/Public/css/orderListClientDetails.css?v=201609031">
    <script>
        //创建订单
        var createOrder_href = "<?php echo U('System/User/createOrder');?>";
        //放弃
        var abandonUser_href = "<?php echo U('System/User/abandonUser');?>";
        //转出
        var allocationUser_href = "<?php echo U('System/User/allocationUser');?>";
        //赎回
        var recoverUser_href = "<?php echo U('System/User/redeemUser');?>";
        //申请转入
        var applyUser_href = "<?php echo U('System/User/applyUser');?>";
        //设置重点
        var editUserMark_href = "<?php echo U('System/User/editUserMark');?>";
    </script>
</head>
<body>
<div class="wrapBox">
    <div class="proCont">
        <div class="proContTop clearfix">
            <div class="topTit l">
                <span class="masterList">客户列表</span>
                <span><em>&gt;</em>客户详情</span>
            </div>
            <div class="topRight r" data-value="<?php echo ($data['userInfo']['user_id']); ?>">
                <!-- 待联系 待跟进-->
                <?php if(($data['userInfo']['status']==20 || $data['userInfo']['status']==30) && !empty($data['isSelf'])): ?><a href="javascript:;" class="return btn_mark <?php echo $data['userInfo']['mark']==2?'active':'';?>" data-value="2">重点</a>
                    <a href="javascript:;" class="return btn_mark mr10 <?php echo $data['userInfo']['mark']==1?'active':'';?>" data-value="1">普通</a>
                    <?php if(!empty($access_list['CREATEORDER'])): ?><a href="javascript:;" class="return mr10 btn_reserve">创建订单</a><?php endif; ?>
                    <?php if(!empty($access_list['ALLOCATIONUSERCONTROL'])): ?><a href="javascript:;" class="return mr10 btn_allocation">转出</a><?php endif; ?>
                    <?php if(!empty($access_list['ABANDONUSER'])): ?><a href="javascript:;" class="return mr10 btn_abandon">放弃</a><?php endif; ?>
                <?php elseif(($data['userInfo']['status']==70) && !empty($data['isSelf'])): ?>
                    <a href="javascript:;" class="return btn_mark <?php echo $data['userInfo']['mark']==2?'active':'';?>" data-value="2">重点</a>
                    <a href="javascript:;" class="return btn_mark mr10 <?php echo $data['userInfo']['mark']==1?'active':'';?>" data-value="1">普通</a>
                    <?php if(!empty($access_list['CREATEORDER'])): ?><a href="javascript:;" class="return mr10 btn_reserve">创建订单</a><?php endif; ?>
                <?php elseif(($data['userInfo']['status']==160 && ($data['userInfo']['system_user_id'] == $userinfo['system_user_id']))): ?>
                    <?php if(!empty($access_list['REDEEMUSER'])): ?><a href="javascript:;" class="return mr10 btn_recover">赎回客户</a><?php endif; ?>
                <?php elseif(($data['userInfo']['status']==160 && empty($data['isSelf']))): ?>
                    <?php if(!empty($access_list['APPLYUSER'])): ?><if condition="(empty($data['isAuditList']))"> <!--add by cq-->
                            <a href="javascript:;" class="return mr10 btn_apply">申请转入</a><?php endif; endif; ?>
                </if>
            </div>
        </div>
        <div class="addContMiddle">
            <div class="title dn">
                有关&nbsp;<span class="red">13651441011</span>&nbsp;的客户
            </div>
            <div class="tab" style="width: auto;">
                <div class="cur">系统信息</div>
                <div>个人信息</div>
                <?php if(($data['userInfo']['status']==20 || $data['userInfo']['status']==30)): ?><div>回访记录</div>
                    <div class="dn">缴费信息</div>
                    <div>短信记录</div>
                <?php else: ?>
                    <div>回访记录</div>
                    <div>缴费信息</div>
                    <div>短信记录</div><?php endif; ?>
            </div>
            <!--  系统信息 start-->
            <div class="content active">
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">真实姓名&nbsp;:&nbsp;</div>
                            <div class="contenColRight auto">
                                <?php if(($data['userInfo']['status']==20 || $data['userInfo']['status']==30 || $data['userInfo']['status']==70) && !empty($data['isSelf'])): ?><input type="text" class="input" name="realname" placeholder="" value="<?php echo ($data['userInfo']['realname']); ?>">
                                <?php else: ?>
                                    <div class="contenColRight auto"><?php echo $data['userInfo']['realname']?$data['userInfo']['realname']:'--';?></div><?php endif; ?>
                                <input type="hidden" name="dn_realname" autocomplete="off" value="<?php echo $data['userInfo']['realname']?$data['userInfo']['realname']:'--';?>">
                            </div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">电子邮件&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight"><input type="text" class="input" name="email" value="<?php echo ($data['userInfo']['email']); ?>"></div>
                            <?php else: ?>
                                <div class="contenColRight"><?php echo $data['userInfo']['email']?$data['userInfo']['email']:'--';?></div><?php endif; ?>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">编号&nbsp;:&nbsp;</div>
                            <div class="contenColRight auto">
                                <div class="contenColRight auto"><?php echo !empty($data['userInfo']['createtime'])?date('Ymd',$data['userInfo']['createtime']):date('Ymd',$data['userInfo']['createtime']); echo ($data['userInfo']['user_id']); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">手机号码&nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if(($data['userInfo']['status']==20 || $data['userInfo']['status']==30) && !empty($data['isSelf'])): ?><input type="text" class="input" name="username" placeholder="" value="<?php echo decryptPhone($data['userInfo']['username'],C('PHONE_CODE_KEY'));?>">
                                <?php else: ?>
                                    <div class="contenColRight auto"><?php echo $data['userInfo']['username']?decryptPhone($data['userInfo']['username'],C('PHONE_CODE_KEY')):'--';?></div><?php endif; ?>
                                <input type="hidden" name="dn_username" autocomplete="off" value="<?php echo $data['userInfo']['username']?decryptPhone($data['userInfo']['username'],C('PHONE_CODE_KEY')):'--';?>">
                            </div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">固定电话&nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if(($data['userInfo']['status']==20 || $data['userInfo']['status']==30) && !empty($data['isSelf'])): ?><input type="text" class="input" name="tel" placeholder="" value="<?php echo ($data['userInfo']['tel']); ?>">
                                <?php else: ?>
                                    <div class="contenColRight auto"><?php echo $data['userInfo']['tel']?$data['userInfo']['tel']:'--';?></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">QQ&nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if(($data['userInfo']['status']==20 || $data['userInfo']['status']==30) && !empty($data['isSelf'])): ?><input type="text" class="input" name="qq" placeholder="" value="<?php echo $data['userInfo']['qq']!=0?$data['userInfo']['qq']:'';?>">
                                <?php else: ?>
                                    <div class="contenColRight auto"><?php echo $data['userInfo']['qq']!=0?$data['userInfo']['qq']:'--';?></div><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">所属渠道 &nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if(($data['userInfo']['status']==20 || $data['userInfo']['status']==30) && !empty($data['isSelf']) && empty($data['userInfo']['channel_id'])): ?><select name="channel_id">
                                        <?php if(is_array($data['channel']['data'])): foreach($data['channel']['data'] as $k=>$v): ?><option value="<?php echo ($v['channel_id']); ?>" <?php echo $data['userInfo']['channel_id']==$v['channel_id']?'selected="selected"':'';?>><?php echo ($v["channelname"]); ?></option>
                                            <?php if(!empty($v['children'])): if(is_array($v['children'])): foreach($v['children'] as $key=>$v2): ?><option value="<?php echo ($v2['channel_id']); ?>" <?php echo $data['userInfo']['channel_id']==$v2['channel_id']?'selected="selected"':'';?>>&nbsp;&nbsp;├─ <?php echo ($v2['channelname']); ?></option><?php endforeach; endif; endif; endforeach; endif; ?>
                                    </select>
                                <?php else: ?>
                                    <div class="contenColRight"><?php echo $data['userInfo']['channelnames']?$data['userInfo']['channelnames']:'--';?></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">搜索词&nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if(empty($data['userInfo']['searchkey']) && !empty($data['isSelf'])): ?><input type="text" class="input" name="searchkey" placeholder="" value="<?php echo ($data['userInfo']['searchkey']); ?>">
                                <?php else: ?>
                                    <div class="contenColRight auto"><?php echo (!empty($data['userInfo']['searchkey']) && $data['userInfo']['searchkey']!==0)?$data['userInfo']['searchkey']:'--';?></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">咨询页面 &nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if(!empty($data['userInfo']['interviewurl'])): ?><a href="<?php echo ($data['userInfo']['interviewurl']); ?>" class="watch" target="_blank">点击查看</a>
                                <?php else: ?>
                                    <?php if(!empty($data['isSelf'])): ?><input type="text" class="input" name="interviewurl" placeholder="" value="">
                                    <?php else: ?>
                                        <div class="contenColRight auto">--</div><?php endif; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">创建人 &nbsp;:&nbsp;</div>
                            <div class="contenColRight"><?php echo ($data['userInfo']['createuser_realname']); ?></div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">出库人 &nbsp;:&nbsp;</div>
                            <div class="contenColRight"><?php echo ($data['userInfo']['updateuser_realname']); ?></div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">出库时间&nbsp;:&nbsp;</div>
                            <div class="contenColRight auto"><?php echo ($data['userInfo']['updatetime']!=0)?date('Y-m-d H:i:s', $data['userInfo']['updatetime']):'--';?></div>
                        </div>
                    </div>
                </div>
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">状态&nbsp;:&nbsp;</div>
                            <div class="contenColRight" id="user_status"><?php echo $data['USER_STATUS'][$data['userInfo']['status']]['text'];?></div>
                        </div>
                        <?php if(($data['userInfo']['status']!=160 || $data['userInfo']['status']!=180)): ?><div class="contenCol">
                                <div class="contenColLeft">所属人&nbsp;:&nbsp;</div>
                                <div class="contenColRight"><?php echo ($data['userInfo']['system_realname']); ?></div>
                            </div><?php endif; ?>
                    </div>
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">所属中心&nbsp;:&nbsp;</div>
                            <div class="contenColRight"><?php echo ($data['userInfo']['zonename']); ?></div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">信息质量&nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if(($data['userInfo']['status']==20 || $data['userInfo']['status']==30) && !empty($data['isSelf'])): ?><select name="infoquality">
                                        <option value="1" <?php echo $data['userInfo']['infoquality']==1?'selected="selected"':'';?>>A</option>
                                        <option value="2" <?php echo $data['userInfo']['infoquality']==2?'selected="selected"':'';?>>B</option>
                                        <option value="3" <?php echo $data['userInfo']['infoquality']==3?'selected="selected"':'';?>>C</option>
                                        <option value="4" <?php echo $data['userInfo']['infoquality']==4?'selected="selected"':'';?>>D</option>
                                    </select>
                                <?php else: ?>
                                    <div class="contenColRight"><?php echo $data['USER_INFOQUALITY'][$data['userInfo']['infoquality']];?></div><?php endif; ?>
                            </div>
                        </div>
                        <?php if(($data['userInfo']['status']!=160 || $data['userInfo']['status']!=180)): ?><div class="contenCol">
                                <div class="contenColLeft">分配时间&nbsp;:&nbsp;</div>
                                <div class="contenColRight"><?php echo ($data['userInfo']['allocationtime']!=0)?date('Y-m-d H:i:s', $data['userInfo']['allocationtime']):'--';?></div>
                            </div><?php endif; ?>
                    </div>
                </div>
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">意向课程&nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if(($data['userInfo']['status']==20 || $data['userInfo']['status']==30) && !empty($data['isSelf'])): ?><select name="course_id">
                                        <!--<option value="0" selected="selected">请选择意向课程</option>-->
                                        <?php if(is_array($data['course'])): foreach($data['course'] as $k=>$v): ?><option value="<?php echo ($v["course_id"]); ?>" <?php echo $data['userInfo']['course_id']==$v['course_id']?'selected="selected"':'';?>><?php echo ($v["coursename"]); ?></option><?php endforeach; endif; ?>
                                    </select>
                                <?php else: ?>
                                    <?php if(is_array($data['course'])): foreach($data['course'] as $k=>$v): if(($data['userInfo']['course_id']==$v['course_id'])): ?><div class="contenColRight"><?php echo ($v["coursename"]); ?></div><?php endif; endforeach; endif; endif; ?>
                            </div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">学习平台&nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if(($data['userInfo']['status']==20 || $data['userInfo']['status']==30) && !empty($data['isSelf'])): ?><select name="learningtype">
                                    <option value="0" selected="selected">请选择学习平台</option>
                                        <?php if(is_array($data['learningtype'])): foreach($data['learningtype'] as $k=>$v): ?><option value="<?php echo ($v["num"]); ?>" <?php echo $data['userInfo']['learningtype']==$v['num']?'selected="selected"':'';?>><?php echo ($v["text"]); ?></option><?php endforeach; endif; ?>
                                    </select>
                                <?php else: ?>
                                    <div class="contenColRight"><?php echo $data['userInfo']['learningtype']?$data['USER_LEARNINGTYPE'][$data['userInfo']['learningtype']]['text']:'泽林';?></div><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">最后回访&nbsp;:&nbsp;</div>
                            <div class="contenColRight"><?php echo ($data['userInfo']['allocationtime']!=$data['userInfo']['lastvisit'] && $data['userInfo']['lastvisit']!=0)?date('Y-m-d H:i:s', $data['userInfo']['lastvisit']):'--';?></div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">跟进结果&nbsp;:&nbsp;</div>
                            <div class="contenColRight"><?php echo ($data['userInfo']['attitude_id']!=0 )?$data['USER_ATTITUDE'][$data['userInfo']['attitude_id']]['text']:'--';?></div>
                        </div>
                        <?php if($data['userInfo']['attitude_id'] == 2): ?><div class="contenCol">
                                <div class="contenColLeft">承诺到访&nbsp;:&nbsp;</div>
                                <div class="contenColRight"><?php echo $data['userInfo']['nextvisit']!=0?date('Y-m-d H:i:s', $data['userInfo']['nextvisit']):'--';?></div>
                            </div>
                        <?php else: ?>
                            <div class="contenCol">
                                <div class="contenColLeft">下次回访&nbsp;:&nbsp;</div>
                                <div class="contenColRight"><?php echo ($data['userInfo']['allocationtime']!=$data['userInfo']['nextvisit'] && $data['userInfo']['nextvisit']!=0)?date('Y-m-d H:i:s', $data['userInfo']['nextvisit']):'--';?></div>
                            </div><?php endif; ?>
                    </div>
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">实际到访&nbsp;:&nbsp;</div>
                            <div class="contenColRight"><?php echo $data['userInfo']['visittime']!=0?date('Y-m-d H:i:s', $data['userInfo']['visittime']):'--';?></div>
                        </div>
                    </div>
                    <div class="addRow clearfix">
                        <div class="contenCol applicationDetails w100 hauto">
                            <div class="contenColLeft">备注&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight remark" style="background: none;">
                                    <script id="editor1" name="advantage"  type="text/plain" ><?php echo htmlspecialchars_decode($data['userInfo']['remark']);?></script>
                                </div>
                            <?php else: ?>
                                <div class="contenColRight remark"><?php echo htmlspecialchars_decode($data['userInfo']['remark']);?></div><?php endif; ?>
                        </div>
                        <?php if((!empty($data['isSelf']))): ?><div class="contenCol">
                                <div class="contenColLeft">&nbsp;</div>
                                <div class="contenColRight"><input type="submit" class="nsSubmit" id="submit1" value="提交"></div>
                            </div><?php endif; ?>
                    </div>
                </div>
            </div>
            <!--  系统信息 end-->
            <!--  个人信息 start-->
            <div class="content">
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">性别&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight">
                                    <select name="sex">
                                        <option value="0" <?php echo $data['userInfo']['sex']==0?'selected="selected"':'';?>>未知</option>
                                        <option value="1" <?php echo $data['userInfo']['sex']==1?'selected="selected"':'';?>>男</option>
                                        <option value="2" <?php echo $data['userInfo']['sex']==2?'selected="selected"':'';?>>女</option>
                                    </select>
                                </div>
                            <?php else: ?>
                                <div class="contenColRight"><?php echo $data['userInfo']['sex']==1?'男':($data['userInfo']['sex']==2?'女':'未知');?></div><?php endif; ?>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">年龄&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight">
                                    <input type="text" class="input" name="birthday" placeholder="" value="<?php echo !empty($data['userInfo']['birthday'])?(date('Y')-date('Y',$data['userInfo']['birthday'])):'';?>">
                                </div>
                            <?php else: ?>
                                <div class="contenColRight"><?php echo !empty($data['userInfo']['birthday'])?(date('Y')-date('Y',$data['userInfo']['birthday'])):'--';?></div><?php endif; ?>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">身份证&nbsp;:&nbsp;</div>
                            <?php if(($data['userInfo']['status']==20 || $data['userInfo']['status']==30) && !empty($data['isSelf'])): ?><div class="contenColRight">
                                    <input type="text" class="input" name="identification" placeholder="" value="<?php echo ($data['userInfo']['identification']); ?>">
                                </div>
                            <?php else: ?>
                                <div class="contenColRight"><?php echo ($data['userInfo']['identification']); ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="addRow clearfix">
                        <div class="contenCol colWidth2">
                            <div class="contenColLeft">户籍所在地&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight auto">
                                    <input type="text" class="input w563" name="homeaddress" placeholder="" value="<?php echo ($data['userInfo']['homeaddress']); ?>">
                                </div>
                            <?php else: ?>
                                <div class="contenColRight"><?php echo ($data['userInfo']['homeaddress']); ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol colWidth2">
                            <div class="contenColLeft">目前居住地&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight auto"><input type="text" class="input w563" name="address" placeholder="" value="<?php echo ($data['userInfo']['address']); ?>"></div>
                            <?php else: ?>
                                <div class="contenColRight auto"><?php echo ($data['userInfo']['address']); ?></div><?php endif; ?>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">邮编&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight"><input type="text" class="input" name="postcode" placeholder="" value="<?php echo ($data['userInfo']['postcode']); ?>"></div>
                            <?php else: ?>
                                <div class="contenColRight auto"><?php echo ($data['userInfo']['postcode']); ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">学历&nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if((!empty($data['isSelf']))): ?><select name="education_id">
                                        <option value="0" <?php echo $data['userInfo']['education_id']==0?'selected="selected"':'';?>>未知</option>
                                        <?php if(is_array($data['educationAll'])): foreach($data['educationAll'] as $k=>$v): ?><option value="<?php echo ($v["education_id"]); ?>" <?php echo $data['userInfo']['education_id']==$v['education_id']?'selected="selected"':'';?>><?php echo ($v["educationname"]); ?></option><?php endforeach; endif; ?>
                                    </select>
                                <?php else: ?>
                                    <?php if(is_array($data['educationAll'])): foreach($data['educationAll'] as $k=>$v): if(($data['userInfo']['education_id'] == $v['education_id'])): ?><div class="contenColRight auto"><?php echo ($v["educationname"]); ?></div><?php endif; endforeach; endif; endif; ?>
                            </div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">专业&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight"><input type="text" class="input" name="major" placeholder="" value="<?php echo $data['userInfo']['major']?$data['userInfo']['major']:'';?>"></div>
                            <?php else: ?>
                                <div class="contenColRight limitDes" title="<?php echo $data['userInfo']['major']?$data['userInfo']['major']:'';?>"><?php echo $data['userInfo']['major']?$data['userInfo']['major']:'';?></div><?php endif; ?>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">毕业学校&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight"><input type="text" class="input" name="school" placeholder="" value="<?php echo ($data['userInfo']['school']); ?>"></div>
                            <?php else: ?>
                                <div class="contenColRight limitDes" title="<?php echo $data['userInfo']['school']?$data['userInfo']['school']:'';?>"><?php echo $data['userInfo']['school']?$data['userInfo']['school']:'';?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">紧急联系人&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight"><input type="text" class="input" name="urgentname" placeholder="" value="<?php echo ($data['userInfo']['urgentname']); ?>"></div>
                            <?php else: ?>
                                <div class="contenColRight auto"><?php echo !empty($data['userInfo']['urgentname'])?$data['userInfo']['urgentname']:'';?></div><?php endif; ?>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">紧急联系人手机&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight"><input type="text" class="input" name="urgentmobile" placeholder="" value="<?php echo !empty($data['userInfo']['urgentmobile'])?$data['userInfo']['urgentmobile']:'';?>"></div>
                            <?php else: ?>
                                <div class="contenColRight auto"><?php echo !empty($data['userInfo']['urgentmobile'])?$data['userInfo']['urgentmobile']:'';?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol w100" style="width:695px;">
                            <div class="contenColLeft">最近公司 &nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight auto"><input type="text" class="input w563" name="lastcompany" placeholder="" value="<?php echo ($data['userInfo']['lastcompany']); ?>"></div>
                            <?php else: ?>
                                <div class="contenColRight auto"><?php echo !empty($data['userInfo']['lastcompany'])?$data['userInfo']['lastcompany']:'';?></div><?php endif; ?>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">最近职位&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight"><input type="text" class="input" name="lastposition" placeholder="" value="<?php echo ($data['userInfo']['lastposition']); ?>"></div>
                            <?php else: ?>
                                <div class="contenColRight auto"><?php echo !empty($data['userInfo']['lastposition'])?$data['userInfo']['lastposition']:'';?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">工作年限&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight"><input type="text" class="input w80" name="workyear" placeholder="" value="<?php echo !empty($data['userInfo']['workyear'])?$data['userInfo']['workyear']:'';?>"><span>年</span></div>
                            <?php else: ?>
                                <div class="contenColRight auto"><?php echo !empty($data['userInfo']['workyear'])?$data['userInfo']['workyear']:'';?></div><?php endif; ?>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">目前薪资&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight"><input type="text" class="input" name="lastsalary" placeholder="格式如：5000~6000" value="<?php echo ($data['userInfo']['lastsalary']); ?>"></div>
                            <?php else: ?>
                                <div class="contenColRight auto"><?php echo !empty($data['userInfo']['lastsalary'])?$data['userInfo']['lastsalary']:'';?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">期望职位&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight"><input type="text" class="input" name="wantposition" placeholder="" value="<?php echo ($data['userInfo']['wantposition']); ?>"></div>
                            <?php else: ?>
                                <div class="contenColRight auto"><?php echo !empty($data['userInfo']['wantposition'])?$data['userInfo']['wantposition']:'';?></div><?php endif; ?>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">期望薪资&nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight"><input type="text" class="input" name="wantsalary" placeholder="格式如：5000~6000" value="<?php echo ($data['userInfo']['wantsalary']); ?>"></div>
                            <?php else: ?>
                                <div class="contenColRight auto"><?php echo !empty($data['userInfo']['wantsalary'])?$data['userInfo']['wantsalary']:'';?></div><?php endif; ?>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">求职状态 &nbsp;:&nbsp;</div>
                            <?php if((!empty($data['isSelf']))): ?><div class="contenColRight auto"><input type="text" class="input" name="workstatus" placeholder="" value="<?php echo ($data['userInfo']['workstatus']); ?>"></div>
                            <?php else: ?>
                                <div class="contenColRight auto"><?php echo !empty($data['userInfo']['workstatus'])?$data['userInfo']['workstatus']:'';?></div><?php endif; ?>
                        </div>

                    </div>
                </div>
                <div class="content-child">
                    <div class="addRow clearfix">
                        <div class="contenCol">
                            <div class="contenColLeft">英语水平&nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if((!empty($data['isSelf']))): ?><input type="text" class="input" name="englishstatus" placeholder="" value="<?php echo ($data['userInfo']['englishstatus']); ?>">
                                <?php else: ?>
                                    <div class="contenColRight auto"><?php echo !empty($data['userInfo']['englishstatus'])?$data['userInfo']['englishstatus']:'';?></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">英语级别&nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if((!empty($data['isSelf']))): ?><select name="englishlevel"  <?php echo empty($data['isSelf'])?'disabled':'';?>>
                                        <option value="0" <?php echo $data['userInfo']['englishlevel']==0?'selected="selected"':'';?>>其他</option>
                                        <option value="1" <?php echo $data['userInfo']['englishlevel']==1?'selected="selected"':'';?>>一级</option>
                                        <option value="2" <?php echo $data['userInfo']['englishlevel']==2?'selected="selected"':'';?>>二级</option>
                                        <option value="3" <?php echo $data['userInfo']['englishlevel']==3?'selected="selected"':'';?>>三级</option>
                                        <option value="4" <?php echo $data['userInfo']['englishlevel']==4?'selected="selected"':'';?>>四级</option>
                                        <option value="5" <?php echo $data['userInfo']['englishlevel']==5?'selected="selected"':'';?>>五级</option>
                                    </select>
                                <?php else: ?>
                                    <div class="contenColRight auto"><?php echo $data['userInfo']['englishlevel']?$data['userInfo']['englishlevel'].'级':'其他';?></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="contenCol">
                            <div class="contenColLeft">电脑级别&nbsp;:&nbsp;</div>
                            <div class="contenColRight">
                                <?php if((!empty($data['isSelf']))): ?><select name="computerlevel"  <?php echo empty($data['isSelf'])?'disabled':'';?>>
                                        <option value="0" <?php echo $data['userInfo']['computerlevel']==0?'selected="selected"':'';?>>其他</option>
                                        <option value="1" <?php echo $data['userInfo']['computerlevel']==1?'selected="selected"':'';?>>一级</option>
                                        <option value="2" <?php echo $data['userInfo']['computerlevel']==2?'selected="selected"':'';?>>二级</option>
                                        <option value="3" <?php echo $data['userInfo']['computerlevel']==3?'selected="selected"':'';?>>三级</option>
                                        <option value="4" <?php echo $data['userInfo']['computerlevel']==4?'selected="selected"':'';?>>四级</option>
                                    </select>
                                <?php else: ?>
                                    <div class="contenColRight auto"><?php echo $data['userInfo']['computerlevel']?$data['userInfo']['computerlevel'].'级':'其他';?></div><?php endif; ?>
                            </div>
                        </div>
                        <?php if((!empty($data['isSelf']))): ?><div class="contenCol">
                                <div class="contenColLeft">&nbsp;</div>
                                <div class="contenColRight"><input type="submit" class="nsSubmit" id="submit2" value="提交"></div>
                            </div><?php endif; ?>
                    </div>
                </div>
            </div>
            <!--  个人信息 end-->
            <!-- 回访记录 start -->
            <div class="content">
                <div class="listRight">
                    <?php if((!empty($data['isSelf']))): ?><div class="addRow clearfix">
                        	<div class="reWay clearfix">
                        		<span class="reTit"><em>*</em>回放方式：</span>
	                        	<ol class="clearfix" id="waytype">
                                    <?php if(is_array($data['callback'])): foreach($data['callback'] as $k=>$v): ?><li data-value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></li><?php endforeach; endif; ?>
	                        	</ol>
                        	</div>

                        	<div class="results clearfix">
                        		<span class="reTit"><em>*</em>跟进结果：</span>
	                        	<ol class="clearfix" id="attitude_id">
                                    <?php if(is_array($data['attitude'])): foreach($data['attitude'] as $k=>$v): if($v['num'] != 10): ?><li data-value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></li><?php endif; endforeach; endif; ?>
	                        	</ol>
                        	</div>
                        	<div class="reTime clearfix">
                        		<div class="contenColLeft" id="uptime"><i>*</i>下次回访：</div>
                                <div class="contenColRight tabTime">
                                    <input type="text" class="input w120 afTime" name="nextvisit" placeholder="请选择日期" value="">
                                    <select name="nextvisit_h" class="w50 mr0">
                                        <?php $__FOR_START_12239__=7;$__FOR_END_12239__=24;for($i=$__FOR_START_12239__;$i < $__FOR_END_12239__;$i+=1){ ?><option <?php echo ($i==9)?'selected="true"':'';?>  value="<?php echo ($i); ?>"><?php echo ($i); ?></option><?php } ?>
                                    </select>
                                    <span>时</span>
                                    <select name="nextvisit_i" class="w50 mr0">
                                        <?php $__FOR_START_7516__=0;$__FOR_END_7516__=60;for($i=$__FOR_START_7516__;$i < $__FOR_END_7516__;$i+=1){ ?><option value="<?php echo ($i); ?>"><?php echo ($i); ?></option><?php } ?>
                                    </select>
                                    <span>分</span>
                                </div>
                        	</div>
                        </div>
                        <div class="addRow mb20 clearfix">
                        <div class="contenCol w100 hauto clearfix">
                            <div class="contenColLeft"><i>*</i>备注&nbsp;:&nbsp;</div>
                            <div class="contenColRight backrecord">
                                <script id="editor2" name="advantage"  type="text/plain" ></script>
                            </div>
                        </div>
                        <div class="contenCol clearfix">
                            <div class="contenColLeft">&nbsp;</div>
                            <div class="contenColRight"><input type="submit" id="submit3" class="nsSubmit" value="提交"></div>
                        </div>
                    </div><?php endif; ?>
                    <!-- 回访记录 -非回库状态-->
                        <ul class="clearfix" id="callback_body">
                        <!--<li class="active">-->
                            <!--<div class="list-l">-->
                                <!--<img src="/Public/images/personalInfo_01-01.jpg" title="">-->
                            <!--</div>-->
                            <!--<div class="list-r">-->
                                <!--<i></i>-->
                                <!--<div class="wrBox2">-->
                                    <!--<div class="wrTriangle"></div>-->
                                    <!--<div class="wrBox2Tit">-->
                                        <!--<div>叶静</div>-->
                                        <!--<div>2016-03-12 20:12:10</div>-->
                                        <!--<div>电话回访</div>-->
                                    <!--</div>-->
                                    <!--<div class="wrBox2Cont">-->
                                        <!--<div class="wrReason">-->
                                            <!--<p class="backTitle">承诺到访</p>-->
                                            <!--<p class="backCon">现在在职</p>-->
                                            <!--<p class="backTime">承诺到访时间：2016-03-10 10：22：55</p>-->
                                        <!--</div>-->
                                    <!--</div>-->
                                <!--</div>-->
                            <!--</div>-->
                        <!--</li>-->

                        <?php if(is_array($data['callbackList'])): foreach($data['callbackList'] as $k=>$v): ?><li>
                            <div class="list-l">
                                <img src="<?php echo $v['face']?$v['face']:'/Public/images/personalInfo_01-01.jpg';?>">
                            </div>
                            <div class="list-r">
                                <i></i>
                                <div class="wrBox2">
                                    <div class="wrTriangle"></div>
                                    <div class="wrBox2Tit">
                                        <div><?php echo ($v["realname"]); ?></div>
                                        <div><?php echo date('Y-m-d H:i:s', $v['callbacktime']);?></div>
                                        <div><?php echo $data['USER_CALLBACK'][$v['waytype']]['text'];?>回访</div>
                                    </div>
                                    <div class="wrBox2Cont">
                                        <div class="wrReason">
                                            <p class="backTitle"><?php echo $data['USER_ATTITUDE'][$v['attitude_id']]['text'];?></p>
                                            <div class="backCon"><?php echo htmlspecialchars_decode($v['remark']);?></div>
                                            <?php if($v['nexttime'] != 0): ?><p class="backTime"><?php echo $v['attitude_id']==2?'承诺到访':'下次回访';?>时间：<?php echo date('Y-m-d H:i:s', $v['nexttime']);?></p><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li><?php endforeach; endif; ?>
                    </ul>
                </div>
            </div>
            <!-- 回访记录 end -->
            <!-- 订单信息 start -->
            <div class="content">
                <div class="listRight" id="orderCont">
                    <ul class="clearfix" id="order_body">
                    </ul>
                </div>
            </div>
            <!-- 订单信息 end -->
            <!-- 短信记录 start -->
            <div class="content">
                <div class="listRight" id="msgCont">
                    <ul class="clearfix" id="">
                    	<!--  
                    		<i class="current"></i>  //  当前项
                    		<div class="wrBox2 msgCurr"> + <div class="msgStatus msgSendSuccess"></div> 发送成功且颜色高亮
                    		<div class="wrBox2"> + <div class="msgStatus msgFailedToSend"></div>  发送失败且颜色低灰
                    	-->
                    </ul>
                </div>
            </div>
            <!-- 短信记录 end -->
            
        </div>
    </div>
</div>

<!-- 申请转入 S -->
<div class="reApplyBox popup dn" id="popup1">
    <div class="alBoxCont">
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>所属渠道:</div>
            <div class="alRowRight" style="width: 360px;">
                <select name="apply_channel_id">
                    <option value="0" selected="selected">请选择渠道</option>
                    <?php if(is_array($data['channel']['data'])): foreach($data['channel']['data'] as $k=>$v): ?><optgroup label='<?php echo ($v["channelname"]); ?>'></optgroup>
                        <!--<option disabled value="<?php echo ($v['channel_id']); ?>"><?php echo ($v["channelname"]); ?></option>-->
                        <?php if(!empty($v['children'])): if(is_array($v['children'])): foreach($v['children'] as $key=>$v2): ?><option value="<?php echo ($v2['channel_id']); ?>">&nbsp;&nbsp;├─ <?php echo ($v2['channelname']); ?></option><?php endforeach; endif; endif; endforeach; endif; ?>
                </select>
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft">信息质量:</div>
            <div class="alRowRight" style="width: 360px;">
                <select name="apply_infoquality">
                    <option value="1" selected="selected">A</option>
                    <option value="2" selected="selected">B</option>
                    <option value="3" selected="selected">C</option>
                    <option value="4" selected="selected">D</option>
                </select>
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft">预转出人:</div>
            <div class="alRowRight">
                <input type="text" class="alInp" name="apply_to_system_user_name" autocomplete="off" style="width: 204px;" disabled="disabled">
                <button class="btn_apply_tosystem " style="width: 52px;">添加</button>
                <input type="hidden" name="apply_to_system_user_id" value="" autocomplete="off">
                <input type="hidden" name="allocation_flag" value="1">
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft">搜索词:</div>
            <div class="alRowRight">
                <input type="text" class="alInp" name="apply_searchword">
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft">咨询页面:</div>
            <div class="alRowRight">
                <input type="text" class="alInp" name="apply_interviewurl">
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft">转介绍:</div>
            <div class="alRowRight singleBox">
                <label for="alCorrect">
                    <input type="radio" value="1" class="alRadio" id="alCorrect" name="apply_alWhether">
                    <span>是</span>
                </label>
                <label for="alWrong">
                    <input type="radio" value="0" class="alRadio" id="alWrong" name="apply_alWhether" checked="checked">
                    <span>否</span>
                </label>
            </div>
        </div>
        <div class="alRow clearfix dn">
            <div class="alRowLeft"><i>&#42</i>转介绍人手机:</div>
            <div class="alRowRight">
                <input type="tel" class="alInp" name="apply_introducermobile">
            </div>
        </div>
        <div class="alRow clearfix" style="height: 119px;">
            <div class="alRowLeft"><i>&#42</i>申请理由:</div>
            <div class="alRowRight" style="height: 119px;">
                <div class="reasonBox">
                    <textarea name="apply_applyreason" cols="30" rows="10" style="width: 355px; height: 119px; border: 1px solid rgb(204, 204, 204);"></textarea>
                </div>
            </div>
        </div>
        <div class="alRow clearfix" style="height: 119px;">
            <div class="alRowLeft">客户备注:</div>
            <div class="alRowRight" style="height: 119px;">
                <div class="reasonBox">
                    <script id="apply_remak" name="advantage" style="width: 355px; height: 119px;" type="text/plain" ></script>
                </div>
            </div>
        </div>
        <div class="alRow clearfix" style="margin-top: 68px;">
            <div class="alRowLeft">&nbsp;</div>
            <div class="alRowRight">
                <input type="submit" class="alSubmit" id="apply_submit" value="提交">
            </div>
        </div>
    </div>
</div>
<!-- 申请转入 E -->

<!-- 赎回客户 S -->
<div class="reApplyBox popup dn" id="popup2">
    <div class="alBoxCont">
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>回访方式:</div>
            <div class="alRowRight" style="width: 360px;">
                <select name="recover_waytype">
                    <?php if(is_array($data['callback'])): foreach($data['callback'] as $k=>$v): ?><option value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></option><?php endforeach; endif; ?>
                </select>
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>跟进结果 :</div>
            <div class="alRowRight" style="width: 360px;">
                <select name="recover_attitude_id" autocomplete="off">
                    <?php if(is_array($data['attitude'])): foreach($data['attitude'] as $k=>$v): ?><!--<?php if(($v['num'] > 2 && $v['num'] != 10)): ?>-->
                            <option value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></option>
                        <!--<?php endif; ?>--><?php endforeach; endif; ?>
                </select>
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>下次回访:</div>
            <div class="alRowRight pzTime clearfix">
                <input type="text" class="input w120 afTime" name="recover_nextvisit" placeholder="请输入日期" value="">
                <select name="recover_nextvisit_h" class="w50 mr0">
                    <?php $__FOR_START_14437__=0;$__FOR_END_14437__=24;for($i=$__FOR_START_14437__;$i < $__FOR_END_14437__;$i+=1){ ?><option <?php echo ($i=="9")?'selected="true"':'';?> value="<?php echo ($i); ?>"><?php echo ($i); ?></option><?php } ?>
                </select>
                <span>时</span>
                <select name="recover_nextvisit_i" class="w50 mr0">
                    <?php $__FOR_START_31034__=0;$__FOR_END_31034__=60;for($i=$__FOR_START_31034__;$i < $__FOR_END_31034__;$i+=1){ ?><option value="<?php echo ($i); ?>"><?php echo ($i); ?></option><?php } ?>
                </select>
                <span>分</span>
            </div>
        </div>

        <div class="alRow clearfix" style="height: 171px;">
            <div class="alRowLeft"><i>&#42</i>备注:</div>
            <div class="alRowRight" style="height: 168px;">
                <div class="reasonBox">
                    <textarea name="recover_remark" cols="30" rows="10"></textarea>
                </div>
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft">&nbsp;</div>
            <div class="alRowRight">
                <input type="submit" class="alSubmit" id="recover_submit" value="提交">
            </div>
        </div>
    </div>
</div>
<!-- 赎回客户 E -->

<!-- 创建订单 S -->
<div class="reApplyBox popup dn" id="panel2">
    <div class="alBoxCont" style="padding-top: 18px;">
        <div class="alRow clearfix">
            <div class="alRowLeft"></div>
            <div class="alRowRight" id="reserve_hint">

            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>真实姓名:</div>
            <div class="alRowRight">
                <input type="text" class="alInp" name="reserve_realname" value="<?php echo !empty($data['userInfo']['realname'])?$data['userInfo']['realname']:'';?>">
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>手机号码:</div>
            <div class="alRowRight">
                <input type="text" class="alInp" name="reserve_username" value="<?php echo $data['userInfo']['username']?decryptPhone($data['userInfo']['username'],C('PHONE_CODE_KEY')):'';?>">
            </div>
        </div>
        <!-- <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>收款方式:</div>
            <div class="alRowRight">
                <select name="reserve_receivetype" autocomplete="off">
                    <option value="0" selected="selected">选择方式</option>
                    <?php if(is_array($data['USER_RECEIVETYPE'])): foreach($data['USER_RECEIVETYPE'] as $key=>$v): ?><option value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></option><?php endforeach; endif; ?>
                </select>
            </div>
        </div> -->
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>订金:</div>
            <div class="alRowRight forecastingTips">
                <input type="text" class="alInp" name="reserve_subscription">
                <span>不得少于100元</span>
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft">&nbsp;</div>
            <div class="alRowRight">
                <input type="submit" class="alSubmit" id="reserve_submit" value="提交">
            </div>
        </div>
    </div>
</div>
<!-- 创建订单 E -->

<!-- 用户转出 S -->
<div id="panel3" class="panel3 dn">
    <div class="panelConcent" style="height: 440px;">
        <div class="div clearfix" style="margin-top: 0px">
            <select name="allocation_roleselect" autocomplete="off">
                <option value="0">全部用户组</option>
                <?php if(is_array($data['departmentAll']['data'])): foreach($data['departmentAll']['data'] as $k=>$v): ?><option value="$v['departmentname_id']" disabled><?php echo ($v["departmentname"]); ?></option>
                    <?php if(is_array($data['roleAll']['data'])): foreach($data['roleAll']['data'] as $k2=>$v2): if($v['department_id'] == $v2['department_id']): ?><option value="<?php echo ($v2['id']); ?>">&nbsp;&nbsp;├─ <?php echo ($v2["name"]); ?></option><?php endif; endforeach; endif; endforeach; endif; ?>
            </select>
            <input type="text" name="allocation_realname" value="" placeholder="输入姓名">
            <button class="nsSearchSubmit">搜索</button>
        </div>

        <div class="Capacity" style="height: 344px;">
            <div class="overflow">
                <dl class="proTit clearfix">
                    <dt class="wOne proSequence clearfix"><span>姓名</span></dt>
                    <dt class="wTwo">所属中心</dt>
                    <dt class="wThr">
	                    <p>线上推广</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wFou">
	                    <p>招聘网站</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wFiv">
	                    <p>在线简历</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wSix">
	                    <p>线下院校</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wSev">
	                    <p>朋友/亲戚</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wEig">
	                    <p>自然网络</p>
	                    <p>A &nbsp;&nbsp; B &nbsp;&nbsp;C&nbsp;&nbsp; D</p>
                    </dt>
                    <dt class="wNin">操作</dt>
                </dl>
                <div id="allocation_body">

                </div>


            </div>
        </div>

    </div>
</div>
<div id="search_body" style="display: none">
</div>
<!-- 用户转出 E -->

<!-- 放弃 S -->
<div class="reApplyBox popup dn" id="panel4" style="height: 280px;">
    <div class="alBoxCont">
		<div class="alRow clearfix">
			<div class="alRowLeft">真实姓名:</div>
			<div class="alRowRight realname">--</div>
		</div>
		<div class="alRow clearfix">
			<div class="alRowLeft">手机号码:</div>
			<div class="alRowRight mobile">--</div>
		</div>
        <div class="alRow clearfix">
            <div class="alRowLeft alGiveUp"><i>&#42</i>放弃原因:</div>
            <div class="alRowRight" style="width: 360px;">
                <select name="abandon_attitude_id">
                    <option value="0" selected="selected">请选择放弃原因</option>
                    <?php if(is_array($data['attitude'])): foreach($data['attitude'] as $k=>$v): if(($v['num'] > 2 && $v['num'] != 10)): ?><option value="<?php echo ($v["num"]); ?>"><?php echo ($v["text"]); ?></option><?php endif; endforeach; endif; ?>
                </select>
            </div>
        </div>
        <div class="alRow clearfix">
            <div class="alRowLeft"><i>&#42</i>备注:</div>
            <div class="alRowRight" style="height: 116px;">
                <textarea name="abandon_remark" class="abandon_remark" style="width:358px;height: 116px;border: 1px solid #ccc;" cols="30" rows="10"></textarea>
            </div>
        </div>
        <div class="alRow clearfix" >
            <div class="alRowLeft">&nbsp;</div>
            <div class="alRowRight">
                <input type="submit" class="alSubmit" id="abandon_submit" value="提交">
            </div>
        </div>
    </div>
</div>
<!-- 放弃 E -->
<input type="hidden" name="temp_user_id" value="<?php echo ($data['userInfo']['user_id']); ?>" autocomplete="off">

<script type="text/javascript" src="/Public/js/jquery-1.9.1.min.js"></script>
<script src="/Public/js/glDatePicker/glDatePicker.js"></script>
<script src="/Public/js/placeholder.js"></script>
<!-- 投递简历弹出框1 工作地址不符提示 S -->
<script type="text/javascript" src="/Public/js/ueditor/ueditor.simple.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="/Public/js/ueditor/ueditor.all.min.js"></script>
<script src="/Public/js/layer/layer.js"></script>
<script src="/Public/js/common_ajax.js?v=20160823"></script>
<script src="/Public/js/userList.js?v=20160823"></script>
<script type="text/javascript" src="/Public/js/findCustomer.js?v=20160823"></script>
<script>
    //是否动态加载缴费记录
    getFeeLog();
    getSmsLog();
    var apply_remak='';
    var ue = UE.getEditor('editor1',{
        toolbars: [
            ['fullscreen', 'source','fontsize','fontfamily', 'undo', 'redo','underline', 'bold','simpleupload','spechars','justifyleft','justifyright','justifycenter','emotion']
        ],
        initialFrameWidth:890,
        initialFrameHeight:200,       
        enableAutoSave:false,
        elementPathEnabled:false,
        maximumWords:1000,
        autoFloatEnabled:false
    });
    // 实例化编辑器
    var ue2 = UE.getEditor('editor2',{
        toolbars: [
            ['fullscreen', 'source','fontsize','fontfamily', 'undo', 'redo','underline', 'bold','spechars','justifyleft','justifyright','justifycenter','emotion']
        ],
        initialFrameWidth:890,
        initialFrameHeight:200,
        pasteplain:true,
        enableAutoSave:false,
        elementPathEnabled:false,
        maximumWords:250,
        autoFloatEnabled:false
    });
    //实例化编辑器
    $(function(){
        $('#submit1').click(function () {
            var data = {
                type : 'edituser',
                realname : $(':input[name="realname"]').val(),
                email : $(':input[name="email"]').val(),
                username : $(':input[name="username"]').val(),
                tel : $(':input[name="tel"]').val(),
                qq : $(':input[name="qq"]').val(),
                channel_id : $(':input[name="channel_id"]').val(),
                infoquality : $(':input[name="infoquality"]').val(),
                course_id : $(':input[name="course_id"]').val(),
                learningtype : $(':input[name="learningtype"]').val(),
                interviewurl : $(':input[name="interviewurl"]').val(),
                searchkey : $(':input[name="searchkey"]').val(),
                remark : ue.getContent()
            };
            common_ajax(data, '', 'no');
        });
        $('#submit2').click(function () {
            var data = {
                type : 'editinfo',
                sex : $(':input[name="sex"]').val(),
                birthday : $(':input[name="birthday"]').val(),
                identification : $(':input[name="identification"]').val(),
                homeaddress : $(':input[name="homeaddress"]').val(),
                address : $(':input[name="address"]').val(),
                learningtype : $(':input[name="learningtype"]').val(),
                postcode : $(':input[name="postcode"]').val(),
                major : $(':input[name="major"]').val(),
                school : $(':input[name="school"]').val(),
                education_id : $(':input[name="education_id"]').val(),
                urgentname : $(':input[name="urgentname"]').val(),
                urgentmobile : $(':input[name="urgentmobile"]').val(),
                lastcompany : $(':input[name="lastcompany"]').val(),
                lastposition : $(':input[name="lastposition"]').val(),
                workyear : $(':input[name="workyear"]').val(),
                lastsalary : $(':input[name="lastsalary"]').val(),
                wantposition : $(':input[name="wantposition"]').val(),
                wantsalary : $(':input[name="wantsalary"]').val(),
                workstatus : $(':input[name="workstatus"]').val(),
                englishstatus : $(':input[name="englishstatus"]').val(),
                englishlevel : $(':input[name="englishlevel"]').val(),
                computerlevel : $(':input[name="computerlevel"]').val(),
            };
            common_ajax(data, '', 'no');
        });
        $('#submit3').click(function () {
            if($('#waytype .curr').length==0){
                layer.msg('请选择回访方式',{icon:2});
            }else if($('#attitude_id .curr').length==0){
                layer.msg('请选择跟进结果',{icon:2});
            }else if($(':input[name="nextvisit"]').val()=='请选择日期'){
                layer.msg('请选择日期',{icon:2});
            }else{
                var waytype = $('#waytype .curr').attr('data-value');
                var attitude_id = $('#attitude_id .curr').attr('data-value');
                var data = {
                    type : 'addcallback',
                    waytype : waytype,
                    attitude_id : attitude_id,
                    nextvisit : $(':input[name="nextvisit"]').val(),
                    nextvisit_hi : $(':input[name="nextvisit_h"]').val()+":"+$(':input[name="nextvisit_i"]').val(),
                    remark : ue2.getContent()
                };
                common_ajax(data, '', 'no',callbackli);
            }
        });

        //放弃自动填充备注
        $(':input[name="abandon_attitude_id"]').change(function(){
            var remarkObj = $('.abandon_remark');
            if(remarkObj.val()=='' && $(this).val()!=10 && $(this).val()!=0){
                var text = ' '+$(':input[name="abandon_attitude_id"]').find("option:selected").text()+' ';
                remarkObj.val(text);
            }else if(remarkObj.val()!='' && $(this).val()!=10 && $(this).val()!=0){
                var remarkText = remarkObj.val();
                var text = $(':input[name="abandon_attitude_id"]').find("option:selected").text();
                remarkObj.val(remarkText.replace(/([^"]*) ([^"]*) ([^"]*)/g, "$1 "+text+" $3"));
            }else{
                $('.abandon_remark').val('');
            }
        });
        //------------------------------------申请转入客户--------------------------------------
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
        //提交申请转入
        $('#apply_submit').click(function(){
            if($(':input[name="apply_alWhether"]:checked').val()==1){
                if($(':input[name="apply_introducermobile"]').val().length!=11){
                    layer.msg('请输入正确的转介绍人手机号码', {icon:2});
                    return;
                }
            }
            var data = {
                type:'submit',
                user_id:"<?php echo ($data['userInfo']['user_id']); ?>",
                infoquality: $(':input[name="apply_infoquality"]').val(),
                channel_id: $(':input[name="apply_channel_id"]').val(),
                searchword: $(':input[name="apply_searchword"]').val(),
                interviewurl: $(':input[name="apply_interviewurl"]').val(),
                introducermobile: $(':input[name="apply_introducermobile"]').val(),
                applyreason : $(':input[name="apply_applyreason"]').val(),
                remark : apply_remak.getContent(),
                to_system_user_id : $(':input[name="apply_to_system_user_id"]').val(),
            };

            if($(':input[name="apply_channel_id"]').val() == 0){
                layer.msg('请选择渠道', {icon:2});
                return;
            }
            if($(':input[name="apply_applyreason"]').val().length == 0){
                layer.msg('请输入申请理由', {icon:2});
                return;
            }
            common_ajax(data, applyUser_href, 'reload');
        });

    });
    function callbackli(){
        var d = new Date();
        var d_h = d.getHours();
        var d_i = d.getMinutes();
        if(d_h.length==1){
            d_h = '0'+d_h;
        }
        if(d_i.length==1){
            d_i = '0'+d_i;
        }
        var time_str = d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate()+' '+d_h+':'+ d_i;
        var status = "<?php echo ($data['userInfo']['status']); ?>";
        var name = "<?php echo $system_user['realname'];?>";
        var face = "<?php echo $system_user['face']?$system_user['face']:'/Public/images/personalInfo_01-01.jpg';?>";
	    var waytype = $('#waytype .curr').html();
        var attitude_id = $('#attitude_id .curr').html();
        var remark = ue2.getContent();
        var nextvisit = $(':input[name="nextvisit"]').val();
        var _nextvisit = nextvisit.split('/');
        if(_nextvisit[2]==undefined){
            _nextvisit = nextvisit.split('-');
        }
        var time_h = $(':input[name="nextvisit_h"]').val();
        var time_i = $(':input[name="nextvisit_i"]').val();
        if(time_h.length==1){
            time_h = '0'+time_h;
        }
        if(time_i.length==1){
            time_i = '0'+time_i;
        }
        var nexttime = _nextvisit[0]+'-'+_nextvisit[1]+'-'+_nextvisit[2]+' '+time_h+":"+time_i+':00';
        //生成HTML
        var str =  '<li><div class="list-l"><img src="'+face+'" ></div> <div class="list-r"> <i></i> <div class="wrBox2"> <div class="wrTriangle"></div> <div class="wrBox2Tit"> <div>'+name+'</div> <div>'+time_str+'</div> <div>'+waytype+'回访</div> </div> <div class="wrBox2Cont"> <div class="wrReason"> <p class="backTitle">'+attitude_id+'</p> <div class="backCon">'+remark+'</div> <p class="backTime">下次回访时间：'+nexttime+'</p> </div> </div> </div> </div> </li>';
        ue2.setContent('');
        $(':input[name="waytype"]').find("option").attr('selected',false);
        $(':input[name="waytype"]').find("option").eq(0).attr('selected',true);
        $(':input[name="attitude_id"]').find("option").attr('selected',false);
        $(':input[name="attitude_id"]').find("option").eq(0).attr('selected',true);
        //处理
        $('#callback_body').prepend(str);
        if(status==20){
            $('#user_status').html('待跟进');
            status = 30;
        };

    };
    //异步加载缴费记录
    function getFeeLog(){
        var data = {
            type : 'getFeeLogs'
        };
        common_ajax2(data, "<?php echo U('System/User/detailUser',array('id'=>$data['user_id']));?>", 'no', getHtml);
        function getHtml(reflag){
            if(reflag.code==0){
                var setHtml = '';
                $.each(reflag.data, function(k, v){
                    if(v.status==30 || v.status==40 || v.status==50) {
                        setHtml += '<li class="green">';
                    }else{
                        setHtml += '<li>';
                    }
                    setHtml += '<div class="list-l"> <img src="/Public/images/personalInfo_01-01.jpg" title=""> </div> <div class="list-r"> <i></i> <div class="wrBox2"> <div class="wrTriangle"></div> <div class="wrBox2Top">';
                    //提示ICON
                    if(v.status==10){
                        setHtml += '<div class="topStatus topPending"></div>';
                    }else if(v.status==20){
                        setHtml += '<span class="topStatus topFail"></span>';
                    }else if(v.status==30){
                        setHtml += '<span class="topStatus topPass"></span>';
                    }else if(v.status==40){
                        setHtml += '<span class="topStatus topPaymentIn"></span>';
                    }else if(v.status==50){
                        setHtml += '<span class="topStatus topPaymentCompletion"></span>';
                    }else if(v.status==60){
                        setHtml += '<div class="topStatus topPartialRefund"></div>';
                    }else{
                        setHtml += '<div class="topStatus topFullRefund"></div>';
                    }
                    if(v.status==20){
                        setHtml += '<table class="tableFail" cellpadding="0" cellspacing="0" >';
                    }else{
                        setHtml += '<table cellpadding="0" cellspacing="0" >';
                    }
                    setHtml += '<tr><td><span class="desOrderNum">订单号：<em>'+v.user_id+v.order_id+'</em></span></tr></td><tr><td><span>实际缴费：<em>'+v.cost+'</em></span></td><td><span>欠费总额：<em>'+ v.sparecost+'</em></span></td><td><span>进班课程：<em>'+v.course_name+'</em></span> </td><td><span>学习方式：<em>'+v.studytype_name+'</em></span></td></tr><tr><td><span>付款类型：<em>'+v.loan_institutions_name+'</em></span></td><td><span>贷款金额：<em>'+v.loan_institutions_cost+'</em></span></td><td><span>学费总额：<em>'+v.coursecost+'</em></span></td><td><span>优惠金额：<em>'+v.discountcost+'</em></span></td></tr> <tr><td><span>实际学费：<em>'+v.paycount+'</em></span></td><td><span>创建时间：<em>'+v.create_time+'</em></span></td></tr></table>';
                    //优惠方式
                    if(v.discount_arr){
                        setHtml += '<div class="preferential clearfix"> <span>优惠方式：</span> <ul class="clearfix">';
                        $.each(v.discount_arr, function(k3, v3){
                            setHtml += '<li>'+v3.dname+'￥'+v3.dmoney+'</li>';
                        });
                        setHtml += '</ul></div>';
                    };

                    setHtml += '</div>';
                    //交易记录
                    if(v.logs){
                        setHtml += '<div class="wrBox2Cont"><table cellpadding="0" cellspacing="0">';
                        $.each(v.logs, function(k2, v2){
                            if(v2.paytype==1){
                                setHtml += '<tr> <td>收款方式： '+v2.payway_name+'</td> <td>收款金额： '+v2.cost+'</td> <td>收款人： '+v2.system_user_name+'</td> <td>收款时间： '+v2.practicaltime+' </td> </tr>';
                            }else{
                                setHtml += '<tr> <td>退款方式： '+v2.payway_name+'</td> <td>退款金额： '+v2.cost+'</td> <td>退款人： '+v2.system_user_name+'</td> <td>退款时间： '+v2.practicaltime+' </td> </tr>';
                            };
                        });
                        setHtml += '</table></div>';
                    };

                    setHtml += '</div></div></li>';
                });
                $('#order_body').html(setHtml);
            };
        };
    };
    //获取短信记录
    function getSmsLog(){
        var data = {
            type : 'getSmsLogs'
        };
        common_ajax2(data, window.location.href, 'no', getSmsHtml);
        function getSmsHtml(reflag){
            $('#msgCont').children('ul').empty();
            if(reflag.code==0){
                var smsHtml = '';
                $.each(reflag.data, function(k,v){
                    if(v.face){
                        var face = v.face;
                    }else{
                        var face = '/Public/images/personalInfo_01-01.jpg';
                    }
                    smsHtml += ' <li><div class="list-l"><img src="'+face+'"></div> <div class="list-r"> <i class="current"></i> ';
                    if(v.sendstatus==0){
                        smsHtml += '<div class="wrBox2 msgCurr"> <div class="wrTriangle"></div> <div class="wrBox2Top"> <div class="msgStatus msgSendSuccess"></div> <div class="msgListInfo">';
                    }else{
                        smsHtml += '<div class="wrBox2"> <div class="wrTriangle"></div> <div class="wrBox2Top"> <div class="msgStatus msgFailedToSend"></div> <div class="msgListInfo">';
                    }
                    smsHtml += '<span>'+v.realname+'</span><span>'+v.send_time+'</span><span>手机短信</span></div>';
                    if(v.sendstatus==0){
                        smsHtml += '<div class="msgListCont">'+v.content+'</div></div> </div> </div> </li>';
                    }else{
                        smsHtml += '<div class="msgListCont">'+v.senderror+'</div></div> </div> </div> </li>';
                    }
                });
                $('#msgCont').children('ul').html(smsHtml);
            }
        };
    };
</script>
</body>
</html>