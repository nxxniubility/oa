<?php
namespace System\Controller;
use Common\Controller\SystemController;

class RbacController extends SystemController{
    
    //控制器前置
    public function _initialize(){
        parent::_initialize();
    }
    
    /************节点管理************/
    
    /*节点列表*/
    public function node(){
        $node = D('Node')->getAllNode();
        $array = array();
        //构建树状结构所需数据
        foreach($node as $k =>$r){
            $r['status']  = $r['status']==1 ? '<font color="red">√</font>' :'<font color="blue">×</font>';
            $r['submenu'] = $r['level']==3 ? '<font color="#cccccc">添加子菜单</font>' : "<a href='".U('System/Rbac/addNode',array('pid'=>$r['id']))."'>添加子菜单</a>";
            $r['edit']    = $r['level']==0 ? '<font color="#cccccc">修改</font>' : "<a href='".U('System/Rbac/editNode',array('id'=>$r['id']))."'>修改</a>";
            $r['del']     = $r['level']==0 ? '<font color="#cccccc">删除</font>' : "<a onClick='return confirmurl(\"".U('Rbac/delNode',array('id'=>$r['id']))."\",\"确定删除该菜单吗?\")' href='javascript:void(0)'>删除</a>";
            switch ($r['display']) {
                case 1:
                    $r['display'] = '不显示';
                    break;
                case 2:
                    $r['display'] = '主菜单';
                    break;
                case 3:
                    $r['display'] = '子菜单';
                    break;
            }
            switch ($r['level']) {
                case 0:
                    $r['level'] = '非节点';
                    break;
                case 1:
                    $r['level'] = '模块';
                    break;
                case 2:
                    $r['level'] = '控制器';
                    break;
                case 3:
                    $r['level'] = '方法';
                    break;
            }
            $array[]      = $r;
        }
    
        $str  = "<tr class='table_content'>
				    <td align='center'><input type='text' class='sort' value='\$sort' name='sort[\$id]'></td>
				    <td>\$id</td>
				    <td>\$spacer \$title (\$name)</td>
				    <td>\$level</td>
				    <td>\$status</td>
				    <td>\$display</td>
					<td>
						\$submenu | \$edit | \$del
					</td>
				  </tr>";
    
        $Tree = new \Org\Arrayhelps\Tree();
        $Tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $Tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $Tree->init($array);
        $html_tree = $Tree->get_tree(0, $str);
        $this->assign('html_tree',$html_tree);
    
        $btn[0]['text'] = '添加节点';
        $btn[0]['url'] = U('System/Rbac/addNode');
        $this->assign('btn',$btn);
        $this->display();
    }
    
    /*添加节点*/
    public function addNode(){
        if(IS_POST){
            $node['name'] = I('post.name');
            $node['title'] = I('post.title');
            $node['status'] = I('post.status');
            $node['remark'] = I('post.remark');
            $node['sort'] = I('post.sort');
            $node['pid'] = I('post.pid');
            $node['level'] = I('post.level');
            $node['display'] = I('post.display');
    
            /*检查数据*/
            if(empty($node['name'])) $this->error('节点名字不能为空');
            if(empty($node['title'])) $this->error('节点描述不能为空');
    
            $nodeInfo = M('Node')->add($node);
            if(!$nodeInfo) $this->error('添加失败');
            $this->success('添加成功');
        }else{
    
            $where['level'] = array('ELT',2);
            $nodeInfo = M('Node')->where($where)->order('sort ASC')->select();
    
            $pid  = I('get.pid');
            if(!empty($pid)){
                foreach($nodeInfo as $k => $v){
                    if($v['id'] == $pid){
                        $select_title = $v['title'];
                        $select_name = $v['name'];
                    }
                }
            }
    
            $this->assign('select_title',$select_title);
            $this->assign('select_name',$select_name);
    
            $arrayhelps = new \Org\Arrayhelps\Arrayhelps();
            $nodeInfo = $arrayhelps->createTree($nodeInfo);
             
            $this->assign('pid',$pid);
            $this->assign('nodeinfo',$nodeInfo);
            $this->assign('status_1','checked="checked"');
            $this->assign('status_0','');
            $this->display();
        }
    }
    
    /*编辑节点*/
    public function editNode(){
    
        if(IS_POST){
            $node['id'] = I('post.id');
            $node['name'] = I('post.name');
            $node['title'] = I('post.title');
            $node['status'] = I('post.status');
            $node['remark'] = I('post.remark');
            $node['sort'] = I('post.sort');
            $node['pid'] = I('post.pid');
            $node['level'] = I('post.level');
            $node['display'] = I('post.display');
    
            /*检查数据*/
            if(empty($node['id'])) $this->error('缺省参数');
            if(empty($node['name'])) $this->error('节点名字不能为空');
            if(empty($node['title'])) $this->error('节点描述不能为空');
    
            $nodeInfo = M('Node')->save($node);
    
            if($nodeInfo === false) $this->error('修改失败');
            $this->success('修改成功');
    
        }else{
            $id = I('get.id');
            if(empty($id)) $this->error('缺省参数');
            $nodeInfo = D('Node')->getAllNode();
            foreach ($nodeInfo as $k => $v){
                if($v['id'] == $id) $nodeid = $v;
            }
    
            foreach($nodeInfo as $k => $v){
                if($nodeid['pid'] == $v['id']){
                    $select_title = $v['title'];
                    $select_name = $v['name'];
                }
            }
    
            $arrayhelps = new \Org\Arrayhelps\Arrayhelps();
            $nodeInfo = $arrayhelps->createTree($nodeInfo);
            $this->assign('id',$id);
            $this->assign('select_title',$select_title);
            $this->assign('select_name',$select_name);
            $this->assign('pid',$nodeid['pid']);
            $this->assign('nodeinfo',$nodeInfo);
            $this->assign('nodeid',$nodeid);
            if($nodeid['status'] == 1) {
                $this->assign('status_1','checked="checked"');
                $this->assign('status_0','');
            }else{
                $this->assign('status_1','');
                $this->assign('status_0','checked="checked"');
            }
            $this->display('addNode');
        }
    }
    
    /*删除节点*/
    public function delNode(){
        $id = I('get.id');
        if(empty($id))  $this->error('缺省参数');
        if(M('Node')->where(array('pid'=>$id))->find()) {
            $this->error('请先删除其子节点');
        }
        $nodeInfo = M('Node')->delete($id);
        if($nodeInfo ===  false) $this->error('删除失败');
        $this->success('删除成功');
    }
}