<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
    <title>系统首页-<?php echo ($siteinfo["sitename"]); ?></title>
    <link rel="stylesheet" href="/Public/css/common.css">
    <link rel="stylesheet" href="/Public/css/rightsManagement.css">
    <link rel="stylesheet" href="/Public/css/jquery.treetable.css">
    <link rel="stylesheet" href="/Public/css/jquery.treetable.theme.default.css">
    <link rel="stylesheet" href="/Public/css/addPermissions.css">
</head>
<body>
<div class="wrapBox">
    <div class="rightsCont">
        <div class="rightsContTop clearfix">
            <div class="topTit l"><span class="masterList">职位权限管理</span></div>
            <div class="topRight r">
                <a href="<?php echo ($data['url_addPosition']); ?>" class="addPermissions">添加职位权限</a>
            </div>
        </div>
        <div class="rightsMiddle">
            <dl class="regionTit  clearfix">
                <dt class="wOne regionSequence clearfix">
                    <span>排序</span>
                    <i class="oergList" onclick="location.href='<?php echo ($data['url_sort']); ?>'"></i>
                </dt>
                <dt class="wTwo regionSequence clearfix">
                    <span>ID</span>
                    <i class="oergList" onclick="location.href='<?php echo ($data['url_id']); ?>'"></i>
                </dt>
                <dt class="wThr">职位名称</dt>
                <dt class="wFou">所属部门</dt>
                <dt class="wFiv">职位描述</dt>
                <dt class="wSix">状态</dt>
                <dt class="wSev">操作</dt>
            </dl>
            <?php if(is_array($data['roleAll']['data'])): foreach($data['roleAll']['data'] as $k=>$v): ?><dl class="clearfix">
                    <dd class="wOne regionSequence clearfix">
                        <input type="tel" class="sequenceInp" value="<?php echo ($v["sort"]); ?>" oldSort="<?php echo ($v["sort"]); ?>"  data-id="<?php echo ($v["id"]); ?>" placeholder="0" maxlength="4" autocomplete="off">
                    </dd>
                    <dd class="wTwo regionSequence clearfix role_id"><?php echo ($v["id"]); ?></dd>
                    <dd class="wThr"><?php echo ($v["name"]); ?>&nbsp;</dd>
                    <dd class="wFou"><?php echo ($v["departmentname"]); ?>&nbsp;</dd>
                    <dd class="wFiv"><?php echo ($v["remark"]); ?>&nbsp;</dd>
                    <dd class="wSix"><?php echo $v['display']==1?'启用':'禁用'; ?></dd>
                    <dd class="wSev">
                        <a href="javascript:;" class="rightsSelect"><i></i></a>
                        <div class="otherOperation">
                            <div class="triangle"></div>
                            <div class="otherIcon">
                                <ul>
                                    <li>
                                        <a href="javascript:;" class="selectPermissions" sid="<?php echo ($v["id"]); ?>">
                                            <span class="set"></span>
                                            <em>权限设置</em>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $data['url_editPosition'].'?role_id='.$v['id'] ?>">
                                            <span class="modify"></span>
                                            <em>修改</em>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:del_position('<?php echo ($v["id"]); ?>');">
                                            <span class="delete"></span>
                                            <em>删除</em>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </dd>
                </dl><?php endforeach; endif; ?>
        </div>

        <div class="collegaPage" style="height: 35px;">
            <a class="sort"  id="sort" href="javascript:void(0)">排序</a>
            <?php echo ($data['paging']); ?>
        </div>
    </div>
</div>
<input type="hidden" value="" name="role_id">
<div class="competenceBox">
    <div class="addPerTop clearfix">
        <span>选择权限</span>
        <div class="addPerClose"></div>
    </div>
    <div class="boxMiddle">
        <div class="boxRows">
            <table id="boxTabel" width="100%" border="0" cellpadding="4" cellspacing="1" class="table treeTable">
                <tbody>
                <?php echo ($data['nodeHtml']); ?>
                </tbody>
            </table>
        </div>
    </div>
    <input type="button" class="addSubmit" value="提交">

</div>
<script src="/Public/js/jquery-1.7.2.js"></script>
<script src="/Public/js/rightsManagement.js"></script>
<script src="/Public/js/layer/layer.js"></script>
<script src="/Public/js/jquery.treetable.js"></script>
<script>
    $(function(){
        //树配置
        $("#boxTabel").treeTable({
            expandable: true,
        });
		//修改排序值
		$('#sort').click(function(){
			var  ids_sort="";
			$(".sequenceInp").each(function(index, element) {
				if( $(this).val()!=$(this).attr('oldSort') ){							
					ids_sort+=','+$(this).attr('data-id')+'-'+$(this).val();
				}
			});
			if(ids_sort!="")ids_sort=ids_sort.substr(1);					
			var data = {
				sort_data:ids_sort,
				type:'sort'
			};
			common_ajax(data,'<?php echo ($data['url_dispostPosition']); ?>','reload');
			
		});
	
		//修改排序值
		$('.SequenceInp').blur(function(){
			
			var v=parseInt($.trim($(this).val()));
			
			if(!v)
			{
				$(this).val($(this).attr('oldSort'));
			}else{
				$(this).val(v);
			}
		})
       
        //修改权限-前置
        $('.addSubmit').click(function(){
            var role_id = $(':input[name="role_id"]').val();
            if(role_id!=''){
                //获取node_id
                var access='';
                if($('.radio:checked').length>0){
                    for(var i=0;i<$('.radio:checked').length;i++){
                        var Obj = null;
                        Obj = $('.radio:checked').eq(i);
                        if(i==0){
                            access+=Obj.val()+'-'+Obj.attr('pid')+'-'+Obj.attr('level');
                        }else{
                            access+=','+Obj.val()+'-'+Obj.attr('pid')+'-'+Obj.attr('level');
                        }
                    }
                }
                edit_access(role_id,access);
            }
        })
    })
    //职位删除
    function del_position(role_id){
        layer.confirm('确定要删除该职位？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var data = {
                role_id:role_id,
                type:'del'
            };
            common_ajax(data,'<?php echo ($data['url_dispostPosition']); ?>','reload');
        }, function(){});
    }
    //职位权限修改
    function edit_access(role_id,access){
        layer.load(2);
        var data = {
            role_id:role_id,
            type:'access',
            access:access
        };
        $.ajax({
            url:'<?php echo ($data['url_dispostPosition']); ?>',
            dataType:'json',
            type:'post',
            data:data,
            success:function(reflag){
                layer.closeAll('loading');
                if(reflag.code && reflag.code!=0){
                    layer.closeAll();
                    layer.msg(reflag.msg,{icon:2});
                }else{
                    layer.closeAll();
                    layer.msg(reflag.msg,{icon:1});
                }
                $(':input[name="role_id"]').val('');
            },
            error:function(){
                layer.closeAll();
                layer.msg('网络异常,请稍后再试！',{icon:2});
            }
        });
    }
</script>
<script src="/Public/js/common_ajax.js"></script>
</body>
</html>