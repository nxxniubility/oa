<?php
/*
* 节点服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class NodeService extends BaseService
{
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /**
     * 获取列表
     * @return array
     */
    protected function _getList()
    {
        $list['data'] = D('Node')->getList();
        $list['count'] = D('Node')->getCount();
        return $list;
    }

    /*
    |--------------------------------------------------------------------------
    | 获取所有节点-文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getNodeList($param)
    {
        $param['where']['status'] = 1;
        $param['where']['node_id'] = !empty($param['where']['node_id'])?$param['where']['node_id']:0;
        $param['order'] = 'sort desc';
        $param['page'] = !empty($param['page'])?$param['page']:null;
        if( F('Cache/node') ){
            $nodeAll = F('Cache/node');
        }else{
            $nodeAll = $this->_getList();
            F('Cache/node', $nodeAll);
        }
        $nodeAll = $this->disposeArray($nodeAll,  $param['order'], $param['page'],  $param['where']);//数组分级
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $nodeAll['data'] = $Arrayhelps->createTree($nodeAll['data'], $param['where']['node_id'], 'id', 'pid');
        return array('code'=>0, 'data'=>$nodeAll);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加节点---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addNode($param)
    {
        //必须参数
        if(empty($param['name'])) return array('code'=>300,'msg'=>'请输入方法名称');
        if(empty($param['title'])) return array('code'=>301,'msg'=>'请输入方法标题');
        $result = D('Node')->addData($param);
        //插入数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/node')) {
                $new_info = D('Node')->getFind(array("id"=>$result['data']));
                $cahce_all = F('Cache/node');
                $cahce_all['data'][] = $new_info;
                $cahce_all['count'] =  $cahce_all['count']+1;
                F('Cache/node', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 修改节点---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editNode($param)
    {
        //必须参数
        if(empty($param['node_id'])) return array('code'=>300,'msg'=>'参数异常');
        if(empty($param['name'])) return array('code'=>301,'msg'=>'请输入方法名称');
        if(empty($param['title'])) return array('code'=>302,'msg'=>'请输入方法标题');
        $result = D('Node')->editData($param,$param['node_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/node')) {
                $new_info = D('Node')->getFind(array("id"=>$param['node_id']));
                $cahce_all = F('Cache/node');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['id'] == $param['node_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
                F('Cache/node', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 删除节点详情---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delNode($param)
    {
        //必须参数
        if(empty($param['node_id'])) return array('code'=>300,'msg'=>'参数异常');
        $result = D('Node')->delete($param['node_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/node')) {
                $cahce_all = F('Cache/node');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['id'] == $param['node_id']){
                        unset($cahce_all['data'][$k]);
                        $cahce_all['count'] = $cahce_all['count']-1;
                    }
                }
                F('Cache/node', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 获取所有节点-文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getNodeInfo($param)
    {
        //必须参数
        if(empty($param['node_id'])) return array('code'=>300,'msg'=>'参数异常');
        if( F('Cache/node') ) {
            $node_list = F('Cache/node');
        }else{
            $node_list = $this->_getList();
            F('Cache/node', $node_list);
        }
        foreach($node_list['data'] as $k=>$v){
            if($v['id']==$param['node_id']){
                $node_info = $v;
            }
        }
        return array('code'=>'0', 'data'=>$node_info);
    }

}