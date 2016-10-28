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
        $param['status'] = 1;
        $param['node_id'] = !empty($param['node_id'])?$param['node_id']:0;
        $param['order'] = !empty($param['order'])?$param['order']:'sort desc';
        $param['page'] = !empty($param['page'])?$param['page']:null;
        if( F('Cache/node') ){
            $nodeAll = F('Cache/node');
        }else{
            $nodeAll = $this->_getList();
            F('Cache/node', $nodeAll);
        }
        $nodeAll = $this->disposeArray($nodeAll,  $param['order'], $param['page'],  $param);//数组分级
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $nodeAll['data'] = $Arrayhelps->createTree($nodeAll['data'], $param['node_id'], 'id', 'pid');
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
        if(empty($param['node_id'])) return array('code'=>301,'msg'=>'参数异常');
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

    /*
    |--------------------------------------------------------------------------
    | 获取父节点
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getNodeParentInfo($param)
    {
        //必须参数
        if(empty($param['id'])) {
            return array('code'=>301,'msg'=>'参数异常');
        }
        $info = D("Node")->where("id = $param[id]")->find();
        $node_info = D("Node")->where("id = $info[pid]")->find();
        return array('code'=>0, 'data'=>$node_info);
    }

    /*
    |--------------------------------------------------------------------------
    | 提交自定义节点操作
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function subNodes($nodes)
    {
        if(empty($nodes)){
                return array('code'=>301, 'msg'=>'没有选择节点');
            }
            $nodes = explode(',', $nodes);
            $system_user_id = $this->system_user_id;
            D('DefineNodes')->where("system_user_id = $system_user_id")->delete();
            foreach ($nodes as $k => $v) {
                if (!empty($v)) {
                    $data = array('system_user_id'=>$system_user_id, 'node_id'=>$v, 'sort'=>$k);
                    $result = D('DefineNodes')->addData($data);
                    if ($result['code'] != 0) {
                        session('default_nodes', null);
                        return array($result['code'], '数据添加失败');
                    }
                }
            }
            session('default_nodes', null);
            $userDefaultNodes = D('SystemUser', 'Service')->getUserDefaultNodes();
            session('default_nodes', $userDefaultNodes['data']);
            return array('code'=>0, 'data'=>$userDefaultNodes['data']);
    }

    /*
    获取自身的自定义节点
    nxx
    */
    protected function _getUserInfoNodes()
    {
        $userDefaultNodes = D('SystemUser', 'Service')->getUserDefaultNodes();
        if (empty($userDefaultNodes['data'])) {
            $tempData = session('sidebar');
            $i = 0;
            foreach ($tempData as $key => $value) {
                if ($i<8) {
                    foreach ($value['children'] as $k1 => $v1) {
                        if ($v1 && $i<8) {
                            $userDefaultNodes['data'][$i] = $v1;
                            $i++;
                        }else{
                            continue;
                        }
                    }
                }else{
                    continue;
                }
            }
        }
        return array('code'=>0, 'data'=>$userDefaultNodes['data']);
    }

    /*拼接自定义快捷栏数据*/
    public function getNodesData()
    {
        //自定义导航class
        $navClass = array('0' => 'sbOne', '1' => 'sbTwo', '2' => 'sbThr', '3' => 'sbFou', '4' => 'sbFiv', '5' => 'sbSix', '6' => 'sbSev', '7' => 'sbEig');
        $userDefaultNodes = $this->_getUserDefineNodes();
        foreach ($userDefaultNodes as $k => $v) {
            $in_array[] = $v['node_id'];
            $nodeData = D('Node', 'Service')->getNodeParentInfo(array('id'=>$v['node_id']));
            $url = U('System/' . $nodeData['data']['name'] . '/' . $v['name']);
            $userDefaultNodes[$k]['url'] = $url;
        }
        $data['navClass'] = $navClass;
        $data['userDefaultNodes'] = $userDefaultNodes;
        return array('code'=>0, 'data'=>$data);
    }

    /*
    *获取用户自定义的节点;
    * @author  nxx
    */
    protected function  _getUserDefineNodes()
    {
        if (session('default_nodes')) {
            $result['data'] = session('default_nodes');
        } else {
            $result = $this->_getUserInfoNodes();
            session('default_nodes', $result['data']);
        }
        return $result['data'];
    }

}