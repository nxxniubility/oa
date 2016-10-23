<?php
namespace Common\Service;
use Common\Service\DataService;
use Common\Service\BaseService;
class ProidService extends BaseService
{

    /**
     * 获取客服代码
     * @author nxx   
     */
    public function getOwnServicecode($where)
    {
        $where['system_user_id'] = $this->system_user_id;
        $where['status'] = 1;
        $servicecodeList = D("Servicecode")->getList($where);
        if ($servicecodeList) {
            foreach ($servicecodeList as $key => $servicecode) {
                $terminal = D("Terminal")->where("terminal_id = $servicecode[terminal_id]")->find();
                $servicecode['terminalname'] = $terminal['terminalname'];
                $servicecodeList[$key] = $servicecode;
            }
            return array('code'=>'0', 'data'=>$servicecodeList);
        }
        return array('code'=>'201', 'msg'=>"么有客服代码哎");

    }

    /**
     * 添加客服代码
     * @author nxx                           
     * @return array
     */
    public function addServicecode($param)
    {
        if(!str_replace(' ', '', $param['title'])) {
            return array('code'=>301, 'msg'=>'标题不能为空');
        }
        if(!str_replace(' ', '', $param['terminal_id'])) {
            return array('code'=>302, 'msg'=>'请选择终端类型');
        }
        if(!str_replace(' ', '', $param['url'])) {
            return array('code'=>303, 'msg'=>'链接不能为空');
        }
        $parant = "#http://(.*?)\.#i";
        if (!preg_match($parant, $param['url'])) {
            return array('code'=>305, 'msg'=>'请输入正确的链接');
        }
        if(!str_replace(' ', '', $param['servicecode'])){
            return array('code'=>304, 'msg'=>'客服代码不能为空');
        }
        $param['system_user_id'] = $this->system_user_id;
        $servicecode['createtime'] = time();
        $servicecode = array_merge_recursive($servicecode,$param);
        $result = D("Servicecode")->addData($servicecode);
        if ($result['code']==0){
            return array('code'=>'0', 'data'=>$result['data']);
        }
        return array('code'=>$result['code'], 'msg'=>$result['msg']);
    }

    /**
     * 修改客服代码
     * @author nxx
     * @return array
     */
    public function editServicecode($request,$servicecode_id)
    {
        if ($request['sign'] == 'del') {
            $result = D('Servicecode')->delData($servicecode_id);
            if ($result['code'] == 0) {
                return array('code'=>0, 'msg'=>'删除成功');
            }
            return array('code'=>$result['code'], 'msg'=>'删除失败');
        }
        if(!isset($servicecode_id)){
            return array('code'=>301, 'msg'=>'参数异常');
        }
        if(!str_replace(' ', '', $request['title'])) {
            return array('code'=>302, 'msg'=>'标题不能为空');
        }
        if(!str_replace(' ', '', $request['terminal_id'])) {
            return array('code'=>303, 'msg'=>'请选择终端类型');
        }
        if(!str_replace(' ', '', $request['url'])) {
            return array('code'=>304, 'msg'=>'链接不能为空');
        }
        $parant = "#http://(.*?)\.#i";
        if (!preg_match($parant, $request['url'])) {
            return array('code'=>305, 'msg'=>'请输入正确的链接');
        }
        if(!str_replace(' ', '', $request['servicecode'])){
            return array('code'=>306, 'msg'=>'客服代码不能为空');
        }
        $request['system_user_id'] = $this->system_user_id;
        $result = D("Servicecode")->editData($request, $servicecode_id);
        if ($result['code'] == 0) {
            return array('code'=>0, 'msg'=>"修改成功");
        }
        return array('code'=>201, 'msg'=>$result['msg']);
    }

    /**
     * 客服代码详情
     * @author nxx
     * @return array
     */
    public function detailServicecode($param)
    {
        if(empty($param['servicecode_id'])){
            return array('code'=>301, 'msg'=>'请求参数异常');
        }
        $servicecodeInfo = D("Servicecode")->getFind($param);
        if ($servicecodeInfo['code'] != 0) {
            return array('code'=>201, 'msg'=>'暂无内容');
        }
        return array('code'=>'0', 'data'=>$servicecodeInfo);
    }

    /*
     * 获取所有终端表-缓存
     * @author nxx
     * @return array
     */
    public function getAllTerminal()
    {
        if (F('Cache/Promote/terminal')) {
            $terminalAll = F('Cache/Promote/terminal');
        } else {
            $terminalAll = D("Terminal")->getList(array('status'=>1));
            F('Cache/Promote/terminal', $terminalAll);
        }
        return array('code'=>0, 'data'=>$terminalAll);
    }

    /*
    删除终端
    @author nxx
    */
    public function delTerminal($terminal_id)
    {
        $result = D("Terminal")->delData($terminal_id);
        if ($result['code'] != 0) {
            return array('code'=>301, 'msg'=>'删除失败');
        }
        $terminalAll = D("Terminal")->getList(array('status'=>1));
        F('Cache/Promote/terminal', $terminalAll);
        return array('code'=>0, 'msg'=>'删除成功');
    }

    /*
     * 获取所有模板内容
     * @author nxx
     * @return array
     */
    public function getPagesList($request)
    {
        if ($request['pagestype_id'] == 0) {
            $pagesList['data'] = D('Pages')->getList(array('terminal_id'=>$request['terminal_id'], 'status'=>1));
        }else{
            $pagesList['data'] = D('Pages')->getList($request);
        }
        return array('code'=>0, 'data'=>$pagesList);
    }

    /*
     * 获取所有模板内容
     * @author nxx
     * @return array
     */
    public function getAllPages($order='createtime desc',$page=null,$where=array('status'=>1))
    {
        if (F('Cache/Promote/pages')) {
            $pagesAll = F('Cache/Promote/pages');
            if (!$pagesAll) {
                $pagesAll = D("Pages")->where($where)->order($order)->select();
                F('Cache/Promote/pages',$pagesAll);
            }
        } else {
            $pagesAll['data'] = D("Pages")
                ->where($where)
                ->order($order)
                ->select();
            $pagesAll['count'] = D("Pages")->count();
            F('Cache/Promote/pages',$pagesAll);
        }
        $pagesAll = $this->disposeArray($pagesAll, $order, $page, $where);
        if (!$pagesAll) {
            return array('code'=>'201', 'msg'=>'没有内容');
        }
        return array('code'=>'0', 'data'=>$pagesAll);
    }

    /*
     * 获取备注
     * @author nxx
     * @return array
     */
    public function getRemark($param)
    {
        $param['systemuser_id'] = $this->system_user_id;
        $result = D("PagesRemark")->getFind($param);
        return array('code'=>0, 'data'=>$result);
    }

    /*
     * 终端快捷操作
     * @author nxx
     * @return array
     */
    public function operateTerminal($param)
    {
        if(empty($param['addTerminal']) && empty($param['editTerminal'])){
             return array('code'=>301, 'msg'=>'未发现需要修改或添加内容');
        }
        if(!empty($param['addTerminal'])){
            $addTerminal = explode('@@',$param['addTerminal']);
            foreach($addTerminal as $k=>$v){
                if(!empty($v) && $addFlag){
                    $add_data['terminalname'] = $v;
                    $addFlag = $this->addTerminal($add_data);
                    if ($addFlag['code'] != 0) {
                        return array('code'=>201, 'msg'=>'添加失败');
                    }
                }
            }
        }
        if(!empty($param['editTerminal'])){
            $editTerminal = explode('@@',$param['editTerminal']);
            foreach($editTerminal as $k=>$v){
                if(!empty($v) && $editFlag!==false){
                    $new_v = explode('==',$v);
                    $edit_data['terminalname'] = $new_v[1];
                    $editFlag = $this->editTerminal($edit_data, $new_v[0]);
                    if ($editFlag['code'] != 0) {
                        return array('code'=>202, 'msg'=>'修改失败');
                    }
                }
            }
        }
        return array('code'=>0, 'msg'=>'操作成功');
    }

    /*
     * 模板分类快捷操作
     * @author nxx
     * @return array
     */
    public function operatePagesType($param)
    {
        if(empty($request['addPagesType']) && empty($request['editPagesType'])){
           return array('code'=>301, 'msg'=>'未发现需要修改或添加内容');
        }
        if(!empty($request['addPagesType'])){
            $addPagesType = explode('@@',$request['addPagesType']);
            foreach($addPagesType as $k=>$v){
                if(!empty($v) && $addFlag){
                    $new_v = explode('==',$v);
                    $add_data['typename'] = $new_v[0];
                    $add_data['terminal_id'] = $new_v[1];
                    $addFlag = $this->addPagesType($add_data);
                    if ($addFlag['code'] != 0) {
                        return array('code'=>202, 'msg'=>'添加失败');
                    }
                }
            }
        }
        if(!empty($request['editPagesType'])){
            $editPagesType = explode('@@',$request['editPagesType']);
            foreach($editPagesType as $k=>$v){
                if(!empty($v) && $editFlag!==false){
                    $new_v = explode('==',$v);
                    $edit_data['typename'] = $new_v[1];
                    $add_data['terminal_id'] = $new_v[2];
                    $editFlag = $this->editPagesType($edit_data, $new_v[0]);
                    if ($editFlag['code'] != 0) {
                        return array('code'=>202, 'msg'=>'修改失败');
                    }
                }
            }
        }
        return array('code'=>0, 'msg'=>'操作成功');

    }

    /*
     * 获取所有模板分类
     * @author nxx
     * @return array
     */
    public function getPagesType($data)
    {
        $data['status'] = 1;
        $datas = D("PagesType")->getList($data);
        return array('code'=>0,'data'=>$datas);
    }

    /*
     * 获取模板内容-缓存
     * @author nxx
     * @return array
     */
    public function getPagesInfo($param)
    {
        if (!$param['pages_id']) {
            return array('code'=>301, 'msg'=>'参数异常');
        }
        if (F('Cache/Promote/pages')) {
            $pagesAll = F('Cache/Promote/pages');
            foreach($pagesAll['data'] as $k=>$v){
                if($v['pages_id']==$param['pages_id']){
                    $pagesInfo = $v;
                }
            }
        } else {
            $pagesInfo = D("Pages")->getFind($param);
        }
        return array('code'=>0, 'data'=>$pagesInfo);
    }

    /*
     * 获取模板相关联计划列表
     * @author nxx
     * @return array
     */
    public function getPagesPromote($pages_id,$where=null,$limit='0,10')
    {
        if ($pages_id) {
            return array('code'=>301, 'msg'=>'参数异常');
        }
        $DB_PREFIX = C('DB_PREFIX');
        $where["{$DB_PREFIX}promote.status"] = 1;
        $where["{$DB_PREFIX}promote.pc_pages_id|{$DB_PREFIX}promote.m_pages_id"] = $pages_id;
        $promoteList['data'] = D('Promote')
            ->field(
                array(
                    "{$DB_PREFIX}promote.plan",
                    "{$DB_PREFIX}promote.keyword",
                    "{$DB_PREFIX}promote.createtime",
                    "{$DB_PREFIX}channel.channelname",
                    "{$DB_PREFIX}system_user.realname"
                )
            )
            ->join("LEFT JOIN __PROID__ on __PROID__.proid_id=__PROMOTE__.proid_id")
            ->join("LEFT JOIN __CHANNEL__ on __CHANNEL__.channel_id=__PROID__.channel_id")
            ->join("LEFT JOIN __SYSTEM_USER__ on __SYSTEM_USER__.system_user_id=__PROID__.system_user_id")
            ->where($where)
            ->order("{$DB_PREFIX}promote.createtime DESC")
            ->limit($limit)
            ->select();

        $promoteList['count'] = D('Promote')
            ->join("LEFT JOIN __PROID__ on __PROID__.proid_id=__PROMOTE__.proid_id")
            ->join("LEFT JOIN __CHANNEL__ on __CHANNEL__.channel_id=__PROID__.channel_id")
            ->join("LEFT JOIN __SYSTEM_USER__ on __SYSTEM_USER__.system_user_id=__PROID__.system_user_id")
            ->where($where)
            ->count();

        return array('code'=>0,'data'=>$promoteList);
    }

    // /*
    //  * 根据terminal_id获取模板内容-缓存
    //  * @author nxx
    //  * @return array
    //  */
    // public function getPages($terminal_id)
    // {
    //     if (F('Cache/Promote/pages')) {
    //         $pagesAll = F('Cache/Promote/pages');
    //         foreach($pagesAll['data'] as $k=>$v){
    //             if($v['terminal_id']==$terminal_id){
    //                 $pagesInfo = $v;
    //             }
    //         }
    //     } else {
    //         $pagesInfo = D("Pages")->where(array('terminal_id'=>$terminal_id))->find();
    //     }
    //     return $pagesInfo;
    // }

    /**
     * 添加模板
     * @author nxx
     */
    public function addPages($param)
    {
        $param['litpic'] = $param['image'];
        if(empty($param['subject'])){
            return array('code'=>301, 'msg'=>'模板主题不能为空');
        }
        if(empty($param['terminal_id'])){
            return array('code'=>302, 'msg'=>'请选择终端');
        }
        if(empty($param['pagestype_id'])){
            return array('code'=>303, 'msg'=>'请选择模板分类');
        }
        if(empty($param['course_id']) && $param['course_id']!=0){
            return array('code'=>304, 'msg'=>'请选择课程');
        }
        if(empty($param['image'])){
            return array('code'=>305, 'msg'=>'请上传图片');
        }
        $param['status'] = 1;
        $param['createtime'] = time();
        $result = D("Pages")->addData($param);
        if ($result['code'] == 0){
            $param['pages_id'] = $result;
            if (F('Cache/Promote/pages')) {
                $pagesAll = F('Cache/Promote/pages');
                $pagesAll['data'][] = $param;
                $pagesAll['count'] = ($pagesAll['count']+1);
                F('Cache/Promote/pages', $pagesAll);
            }
            return array('code'=>0, 'data'=>$result);
        }
        return array('code'=>201, 'msg'=>'添加模板失败');
    }

    /**
     * 修改模板
     * @author nxx
     */
    public function editPages($data, $pages_id)
    {
        if (!$pages_id) {
            return array('code'=>301, 'msg'=>'参数异常');
        }
        $data['litpic'] = $data['image'];
        if(empty($data['subject'])) {
            return array('code'=>301, 'msg'=>'模板主题不能为空');
        }
        if(empty($data['terminal_id'])) {
            return array('code'=>302, 'msg'=>'请选择终端');
        }
        if(empty($data['pagestype_id'])) {
            return array('code'=>303, 'msg'=>'请选择模板分类');
        }
        if(empty($data['course_id']) && $data['course_id']!=0) {
            return array('code'=>304, 'msg'=>'请选择课程');
        }
        if(empty($data['image'])) {
            return array('code'=>305, 'msg'=>'请上传图片');
        }
        $result = D("Pages")->editData($data, $pages_id);
        if ($result['code'] == 0){
            if (F('Cache/Promote/pages')) {
                $newInfo = D("Pages")->getFind(array('pages_id'=>$pages_id));
                $pagesAll = F('Cache/Promote/pages');
                foreach($pagesAll['data'] as $k=>$v){
                    if($v['pages_id'] == $pages_id){
                        $pagesAll['data'][$k] = $newInfo;
                    }
                }
                F('Cache/Promote/pages', $pagesAll);
            }
            return array('code'=>0, 'msg'=>'修改成功');
        }
        return array('code'=>201, 'msg'=>'修改失败');
    }
    /**
     * 获取模板 导航
     * @author nxx
     */
    public function getPagesNav($pages_id)
    {
        $DB_PREFIX = C('DB_PREFIX');
        $inf = D('Pagesnav')
            ->where(array($DB_PREFIX.'pagesnav.pages_id'=>$pages_id))
            ->join('__PAGES__ ON __PAGES__.pages_id=__PAGESNAV__.pages_nav_id')
            ->order($DB_PREFIX.'pagesnav.sort asc')
            ->select();
        return array('code'=>0, 'data'=>$inf);
    }
    /**
     * 添加模板 导航
     * @author nxx
     */
    public function createPagesNav($data,$pages_id)
    {
        if ($pages_id) {
            return array('code'=>301, 'msg'=>"参数异常");
        }
        $add['pages_id'] = $pages_id;
        D('Pagesnav')->where(array('pages_id'=>$pages_id))->delete();
        if(!empty($data)){
            $data = explode(',', $data);
            foreach($data as $k=>$v){
                $v = explode('@@', $v);
                $add['pages_nav_id'] = $v[0];
                $add['nav_name'] = $v[1];
                $add['sort'] = ($k+1);
                $result = D('Pagesnav')->addData($add);
                if($result['code'] != 0) {
                    return array('code'=>201, 'msg'=>"添加导航失败");
                }
            }
        }
        return array('code'=>0, 'msg'=>"添加导航成功");
    }

    /*
     * 获取模板分类-缓存
     * @author nxx
     * @return array
     */
    public function getAllPagesType()
    {
        if (F('Cache/Promote/pagesType')) {
            $pagesTypeAll = F('Cache/Promote/pagesType');
        } else {
            $pagesTypeAll = D('PagesType')->getList(array('status'=>1));
            F('Cache/Promote/pagesType',$pagesTypeAll);
        }

        return array("code"=>0,'data'=>$pagesTypeAll);
    }

    /**
     * 添加模板分类
     * @author nxx
     */
    public function addPagesType($data)
    {
        $data['status'] = 1;
        $result = D('PagesType')->data($data)->add();
        if ($result!==false){
            $data['pagestype_id'] = $result;
            if (F('Cache/Promote/pagesType')) {
                $cacheAll = F('Cache/Promote/pagesType');
                $cacheAll[] = $data;
                F('Cache/Promote/pagesType', $cacheAll);
            }
            return array("code"=>0,'data'=>$result);
        }
        return array("code"=>1,'msg'=>'添加模板分类失败');
    }

    /**
     * 修改模板分类
     * @author nxx
     */
    public function editPagesType($data,$pagestype_id)
    {
        $result = D('PagesType')->editData($data, $pagestype_id);
        if ($result['code'] == 0){
            if (F('Cache/Promote/pagesType')) {
                $newInfo = D('PagesType')->getFind(array('pagestype_id'=>$pagestype_id));
                $cacheAll = F('Cache/Promote/pagesType');
                foreach($cacheAll as $k=>$v){
                    if($v['pagestype_id'] == $pagestype_id){
                        if(!empty($data['satus']) && $data['satus']==0){
                            unset($cacheAll[$k]);
                        }else{
                            $cacheAll[$k] = $newInfo;
                        }
                    }
                }
                F('Cache/Promote/pagesType', $cacheAll);
            }
            return array('code'=>0,'msg'=>'修改模板分类成功');
        }
        return array('code'=>201,'msg'=>'修改模板分类失败');
    }
    /**
     * 查看模板备注
     * @author nxx
     */
    public function getPagesRemark($pages_id,$system_user_id)
    {
        $remarklt = D('PagesRemark')->getFind(array('pages_id'=>$pages_id,'system_user_id'=>$system_user_id));
        return array('code'=>0, 'data'=>$remarklt);
    }

    /**
     * 添加模板备注
     * @author nxx
     */
    public function addPagesRemark($remark,$pages_id,$system_user_id)
    {
        if ($pages_id) {
            return array('code'=>301,'msg'=>'参数异常');
        }
        $data['remark'] = $remark;
        $remarklt = D('PagesRemark')->where(array('pages_id'=>$pages_id,'system_user_id'=>$system_user_id))->find();
        if (empty($remarklt)){
            $data['pages_id'] = $pages_id;
            $data['system_user_id'] = $system_user_id;
            $result = D('PagesRemark')->data($data)->add();
        }else{
            $result = D('PagesRemark')->where(array('pages_id'=>$pages_id,'system_user_id'=>$system_user_id))->save($data);
        }
        if($result!==false) {
            array('code'=>0,'msg'=>'备注添加成功');
        }else{
            return array('code'=>201,'msg'=>'备注添加失败');
        }
    }
        
    /**
    模板导航
    */
    public function getAllPagesnav($where,$field){
        return D('Pagesnav')->where($where)->field($field)->select();
    }


    /**
     * 修改终端表
     * @author nxx
     */
    public function editTerminal($data, $terminal_id)
    {
        if (!$terminal_id) {
            return array('code'=>301,'msg'=>'参数异常');
        }
        $TerminalDb = D('Terminal');
        $result = $TerminalDb->editData($data, $terminal_id);
        if ($result!==false){
            if (F('Cache/Promote/terminal')) {
                $newInfo = $TerminalDb->getFind(array('terminal_id'=>$terminal_id));
                $cacheAll = F('Cache/Promote/terminal');
                foreach($cacheAll['data'] as $k=>$v){
                    if($v['terminal_id'] == $terminal_id){
                        if(!empty($data['satus']) && $data['satus']==0){
                            unset($cacheAll['data'][$k]);
                        }else{
                            $cacheAll['data'][$k] = $newInfo;
                        }
                    }
                }
                F('Cache/Promote/terminal', $cacheAll);
            }
            return array('code'=>0,'msg'=>'成功');
        }
        return array('code'=>201,'msg'=>'失败');
    }


    /**
     * 添加终端表
     * @author nxx
     */
    public function addTerminal($data)
    {
        $data['status'] = 1;
        $result = D('Terminal')->addData($data);
        if (!$result['data']){
            $data['terminal_id'] = $result;
            if (F('Cache/Promote/terminal')) {
                $cacheAll = F('Cache/Promote/terminal');
                $cacheAll[] = $data;
                F('Cache/Promote/terminal', $cacheAll);
            }
            return array('code'=>0,'data'=>$result);
        }
        return array('code'=>201,'msg'=>'添加失败');;
    }

    /*
    |--------------------------------------------------------------------------
    | 推广账号管理
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function proidList($pro, $limit)
    {
        $pro['system_user_id'] = $this->system_user_id;
        $order = 'createtime desc';
        $proidDb = D("Proid");
        $servicecodeDb = D('Servicecode');
        $getProList['data'] = $proidDb->getList($pro, null, $order, $limit);
        $getProList['count'] = $proidDb->getCount($pro);
        foreach ($getProList['data'] as $key => $pro) {
            $channe = D('Channel')->getFind(array('channel_id'=>$pro['channel_id']));
            $getProList['data'][$key]['channelname'] = $channe['channelname'];
            if ($pro['pcservice_id']) {
                $pcservice = $servicecodeDb->getFind(array('servicecode_id'=>$pro['pcservice_id']));
                $getProList['data'][$key]['pcservice'] = $pcservice['title'];
            }
            if ($pro['mservice_id']) {
                $mservice = $servicecodeDb->getFind(array('servicecode_id'=>$pro['mservice_id']));
                $getProList['data'][$key]['mservice'] = $mservice['title'];
            }
        }
        return array('code'=>0,'data'=>$getProList);
    }

    /**
     * 添加账号
     * @author Nixx
    */
    public function createProid($proid)
    {   
        $proidDb = D("Proid");
        $proid['system_user_id'] = $this->system_user_id;
        if (!str_replace(' ', '', $proid['accountname'])) {
            return array('code'=>301,'msg'=>'请填写账号名称');
        }
        if (!str_replace(' ', '', $proid['channel_id'])) {
            return array('code'=>302,'msg'=>'请选择渠道');
        }
        $parant = "#http://(.*?)\.#i";
        if (!preg_match($parant, $proid['domain'])) {
            return array('code'=>303, 'msg'=>'请输入正确的域名');
        }

        if (!str_replace(' ', '', $proid['totalcode'])) {
            return array('code'=>305,'msg'=>'请填写统计代码');
        }
        if (!$proid['pcservice_id'] || !$proid['mservice_id']) {
            return array('code'=>306,'msg'=>'请选择PC客服代码或移动客服代码');
        }
        $proid['status'] = 1;
        $pro = $proidDb->getFind($proid);
        if ($pro) {
            return array('code'=>201, 'msg'=>'已存在相同的推广账号');
        }
        $proid['createtime'] = time();
        $proid_id = $proidDb->addData($proid);
        if ($proid_id['code'] != 0) {
            return array('code'=>202, 'msg'=>$proid_id['msg']);
        }
        return array('code'=>0, 'msg'=>'创建推广账号成功');

    }

    /**
     * 修改账号
     * @author Nixx
    */
    public function editProid($proid)
    {
        if (!$proid['proid_id']) {
            return array('code'=>301, 'msg'=>'参数丢失');
        }
        $proid['system_user_id'] = $this->system_user_id;
        if (!str_replace(' ', '', $proid['accountname'])) {
            return array('code'=>301,'msg'=>'请填写账号名称');
        }
        if (!str_replace(' ', '', $proid['channel_id'])) {
            return array('code'=>302,'msg'=>'请选择渠道');
        }
        $parant = "#http://(.*?)\.#i"; 
        if (!preg_match($parant, $proid['domain'])) {
            return array('code'=>303, 'msg'=>'请输入正确的域名');
        }

        if (!str_replace(' ', '', $proid['totalcode'])) {
            return array('code'=>305,'msg'=>'请填写统计代码');
        }
        if (!$proid['pcservice_id'] || !$proid['mservice_id']) {
            return array('code'=>306,'msg'=>'请选择PC客服代码或移动客服代码');
        }
        $proid['status'] = 1;
        $backInfo = D('Proid')->editData($proid, $proid['proid_id']);
        if ($backInfo['code'] != 0) {
            return array('code'=>$backInfo['code'], 'msg'=>$backInfo['msg']);
        }
        return array('code'=>'0', 'msg'=>'修改推广账号成功');

    }

    /**
     * 获取账号详情
     * @author Nixx
    */
    public function getProInfo($param)
    {
        if (!$param['pc_pages_id']) {
            unset($param['pc_pages_id']);
        }
        if (!$param['m_pages_id']) {
            unset($param['m_pages_id']);
        }
        if (!$param['proid_id']) {
             return array('code'=>301, 'msg'=>'参数异常');
        }
        $param['satus'] = 1;
        $proInfo = D("Proid")->getFind($param);
        if (!$proInfo) {
            return array('code'=>201,'msg'=>'没有信息');
        }
        $channelInfo = D("Channel")->getFind(array('channel_id'=>$proInfo['channel_id']));
        $proInfo['channelName'] = $channelInfo['channelname'];
        if ($proInfo['pcservice_id']) {
            $pcService = D("Servicecode")->getFind(array('servicecode_id'=>$proInfo['pcservice_id'], 'status'=>1));
        }
        if ($proInfo['mservice_id']) {
            $mService = D("Servicecode")->getFind(array('servicecode_id'=>$proInfo['mservice_id'], 'status'=>1));
        }     
        $proInfo['pcservice'] = $pcService['title'];
        $proInfo['mservice'] = $mService['title'];
        return array('code'=>0,'data'=>$proInfo);
    }

    /**
     * 删除推广账号
     * @author Nxx
     */
    public function deleteProid($proid_id)
    {
        if (!$proid_id) {
            return array('code'=>301, 'msg'=>'请选择要删除的账号');
        }
        $proid['status'] = 0;
        D()->startTrans();
        $updateproid = D("Proid")->editData($proid, $proid_id);
        $updatepromote = D("Promote")->where("proid_id = $proid_id")->delete();
        $updateprolev = D("ProLev")->where("proid_id = $proid_id")->delete();
        if ($updateproid['code'] == 0 && $updatepromote && $updateprolev) {
            D()->commit();
            return array("code"=>0,'msg'=>'删除推广账号成功');
        }
        D()->rollback();
        return array("code"=>201,'msg'=>'删除推广账号失败');
    }

    /**
     * 删除推广计划
     * @author Nxx
     */
    public function deletePro($pro)
    {   
        $promoteInfo = $this->getPromoteInfo($pro);
        $reback = D("Promote")->delData($pro['promote_id']);
        if ($reback === false) {
            return array("code"=>201,'msg'=>'删除推广计划失败');
        }
        return array("code"=>0,'msg'=>'删除推广计划成功');
    }

    /**
     * 获取pro_lev
     * @author Nixx
    */
    public function getProLevInfo($prolev)
    {
        $prolev['status'] = 1;
        $prolevInfo = D("ProLev")->getFind($prolev);
        if (!$prolevInfo) {
            return array('code'=>301, 'msg'=>'暂无数据');
        }
        return array('code'=>0, 'data'=>$prolevInfo);
    }


    /**
     * 创建pro_lev
     * @author Nixx
    */
    public function createProLev($prolev)
    {
        $result = D("ProLev")->addData($prolev);
        if (!$result['data']) {
            return array("code"=>201,'msg'=>'创建pro_lev失败');
        }
        return array('code'=>0, 'data'=>$result['data']);
    }


    /**
     * 添加推广计划
     * @author Nixx
    */
    public function createPromote($promote)
    {
        $promote['plan'] = str_replace(' ', '', $promote['plan']);
        $promote['planunit'] = str_replace(' ', '', $promote['planunit']);
        if (!$promote['plan'] && $promote['planunit']) {
            return array('code'=>301, 'msg'=>'请填写计划');
        }
        if ($promote['plan'] && !$promote['planunit']) {
            return array('code'=>302, 'msg'=>'请填写计划单元');
        }
        if (!str_replace(' ', '', $promote['keyword'])) {
            return array('code'=>303, 'msg'=>'请填写关键词');
        }
        if (!$promote['pc_pages_id'] || !$promote['m_pages_id']) {
            return array('code'=>304, 'msg'=>'PC或者移动专题页不能为空');
        }
        $promoteDb = D("Promote");
        $proLevDb = D("ProLev");
        unset($promote['pcservice']);
        unset($promote['mservice']);
        unset($promote['pc_pages']);
        unset($promote['m_pages']);
        unset($promote['createtime']);
        if (!$promote['plan']) {
            unset($promote['plan']);
        }
        if (!$promote['planunit']) {
            unset($promote['planunit']);
        }
        $promote['status'] = 1;
        //若无则执行添加操作
        $promoteInfo = $promoteDb->getFind($promote);
        if ($promoteInfo) {
            return array("code"=>0,'data'=>$promoteInfo['promote_id']);
        }else{
            $promote['createtime'] = time();
            $result = $promoteDb->addData($promote);
            if ($result['code'] != 0) {
                return array("code"=>$result['code'],'msg'=>$result['msg']);
            }
            return array("code"=>0,'data'=>$result['data']);
        }
    }


    /**
     * 获取计划列表
     * @author Nixx
    */
    public function getPromoteList($promote, $limit="0,15")
    {
        $promoteDb = D("Promote");
        $servicecodeDb = D("Servicecode");
        $promote['system_user_id'] = $this->system_user_id;
        $promote['status'] = 1;
        $getPromoteListAll['count'] = $promoteDb->getCount($promote);
        $proid = D("Proid")->getFind(array('proid_id'=>$promote['proid_id'], 'status'=>1));
        $promotes = $promoteDb->getList($promote, null, null, $limit);
        if (!$promotes) {
            return array('code'=>201, 'msg'=>'暂无数据');
        }
        foreach ($promotes as $key => $promote) {
            if ($promote['pcservice_id']) {
                $pcservice = $servicecodeDb->getFind(array('servicecode_id'=>$promote['pcservice_id']));
                $promote['pcservice'] = $pcservice['url'];
            }else{
                $pcservice = $servicecodeDb->getFind(array('servicecode_id'=>$promote['pcservice_id']));
                $promote['pcservice'] = $pcservice['url'];
            }
            if ($promote['mservice_id']) {
                $mservice = $servicecodeDb->getFind(array('servicecode_id'=>$promote['mservice_id']));
                $promote['mservice'] = $mservice['url'];
            }else{
                $mservice = $servicecodeDb->getFind(array('servicecode_id'=>$promote['mservice_id']));
                $promote['mservice'] = $mservice['url'];
            }
            $promote['pc_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$promote['promote_id']}&dev=2";
            $promote['m_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$promote['promote_id']}&dev=1";
            $promotes[$key] = $promote; 
        }   
        $getPromoteListAll['promoteList'] = $promotes;
        return array('code'=>0, 'data'=>$getPromoteListAll);
    }

    /**
     * 获取批量修改计划的搜索条件
     * @author Nixx
    */
    public function getProLevPlanunitList($param)
    {
        $param['status'] = 1;
        $proLevPlanunitList = D("ProLev")->getList($param);
        if (!$proLevPlanunitList) {
            return array('code'=>201,'msg'=>"暂无数据");
        }
        return array('code'=>0,'data'=>$proLevPlanunitList);
    }

    /*
    拼接pro_lev_id符串
     */
    public function getIdString($promote)
    {
        foreach($promote as $pro){
            $includedString = $includedString . ",$pro[pro_lev_id]";
        }
        return array('code'=>0, 'data'=>$includedString);
    }

    /**
     * 获取计划
     * @author Nixx
    */
    public function getProLevPlanList($proid_id)
    {
        $proLevPlanList = D("ProLev")->where("proid_id = $proid_id and pid = 0 and pro_lev_id!=0 and status=1")->select();

        if (!$proLevPlanList) {
            return array('code'=>1, 'msg'=>'获取计划失败');
        }       
        return array('code'=>0, 'data'=>$proLevPlanList);
    }

    /**
     * 查找设置模板
     * @author   Nxx
     */
    public function getSetPages($setPages)
    {
        $setPages['system_user_id'] = $this->system_user_id;
        $setPages['status'] = 1;
        $pages = D("Setpages")->getList($setPages);
        return array('code'=>0,'data'=>$pages);
    }


    /**
     * 添加设置模板
     * @author   Nxx
     */ 
    public function createSetPages($setPages)
    {
        if (!$setPages['pagesname']) {
            return array('code'=>301, 'msg'=>'请填写模板名称');
        }
        if (!$setPages['sign']) {
            return array('code'=>302, 'msg'=>'请至少选择1个表头');
        }
        $setPages['sign'] = explode(',',$setPages['sign']);
        foreach ($setPages['sign'] as $key => $sign) {
            $setPages['sign'][$key] = explode('-',$sign);
        }            
        $setPages['system_user_id'] = $this->system_user_id;
        $set['system_user_id'] = $setPages['system_user_id'];
        $set['pagesname'] = $setPages['pagesname'];
        $set['status'] = 1;
        $result = D("Setpages")->getFind($set);   
        if ($result['code'] != 0) {
            return array('code'=>303, 'msg'=>'模板名已存在');
        }       
        $set['type'] = $setPages['type'];
        if ($setPages['channel_id']) {
            $result = D("Setpages")->getFind(array('system_user_id'=>$set['system_user_id'], 'channel_id'=>$setPages['channel_id'], 'status'=>1, 'type'=>$setPages['type']));   
            if ($result['data']) {
                return array('code'=>304, 'msg'=>'该渠道已存在模板');
            }
            $set['channel_id'] = $setPages['channel_id'];
        }else{
            $set['channel_id'] = 0;
        }
        $set['createtime'] = time();    
        $setpages_id = D("Setpages")->addData($set);
        $setpages_id = $setpages_id['data'];
        if (!$setpages_id) {
            $error['code'] = 201;
            $error['msg'] = '模板添加失败';
            return $error;
        }   
        foreach ($setPages['sign'] as $key => $pages) {
            $arr[] = $pages[0];
        }
        if (count($arr)>count(array_unique($arr))) {
            $del = D("Setpages")->where("setpages_id = $setpages_id")->delete();
            $error['code'] = 305;
            $error['msg'] = '请不要重复选择表头';
            return $error;
        }   
        foreach ($setPages['sign'] as $key => $pages) {
            $pageInfo['pagehead'] = strtoupper($pages[0]);
            $pageInfo['headname'] = $pages[1];
            $pageInfo['setpages_id'] = $setpages_id;
            $result = D("Setpageinfo")->data($pageInfo)->add();
            if (!$result) {
                $updat = D("Setpages")->where("setpages_id = $setpages_id")->delete();
                $error['code'] = 202;
                $error['msg'] = '模板表头设置失败';
                return $error;
            }
        }
        return array('code'=>0, 'data'=>$setpages_id);
    }

    /**
     * 查找设置模板详情
     * @author   Nxx
     */
    public function getSetPagesInfos($setpages_id)
    {
        $setpagesInfos = D("Setpageinfo")->where("setpages_id = $setpages_id")->order('pagehead')->select();
        return array('code'=>0,'data'=>$setpagesInfos);
    }

    /**
     * 修改设置模板
     * @author   Nxx
     */
    public function editSetPages($setPages)
    {
        if (!$setPages['pagesname']) {
            return array('code'=>301, 'msg'=>'请填写模板名称');
        }
        $setPages['system_user_id'] = $this->system_user_id;
        if ($setPages['type'] == 2) {
            if (!$setPages['channel_id']) {
                return array('code'=>302, 'msg'=>'请选择渠道');
            }
        }
        if (!$setPages['sign']) {
            return array('code'=>303, 'msg'=>'请至少选择1个表头');
        }
        $setPages['sign'] = explode(',', $setPages['sign']);
        foreach ($setPages['sign'] as $key => $pages) {
            $setPages['sign'][$key] = explode('-', $pages);
            $pages = strtoupper($pages[0]);
            $arr[] =$pages[0];
        }
        if (count($arr)>count(array_unique($arr))) {
            return array('code'=>304, 'msg'=>'请不要重复选择表头');
        }
        foreach ($setPages['sign'] as $key => $value) {
            $array[] = $setPages['sign'][$key][1];
            $value[0] = strtoupper($value[0]);
            $setPages['sign'][$key] = $value;
        }
        if (!in_array('username', $array) && !in_array('qq', $array) && !in_array('tel', $array)) {
            return array('code'=>305, 'msg'=>'手机-QQ-固话至少有一个');
        }
        D("Setpageinfo")->startTrans();
        D("Setpageinfo")->where("setpages_id = $setPages[setpages_id]")->delete();
        $up = D('Setpages')->where("setpages_id = $setPages[setpages_id]")->save($setPages);
        foreach ($setPages['sign'] as $key => $pages) {
            $pageInfo['pagehead'] = strtoupper($pages[0]);
            $pageInfo['headname'] = $pages[1];
            $pageInfo['setpages_id'] = $setPages['setpages_id'];
            $result = D("Setpageinfo")->data($pageInfo)->add();
            if (!$result) {
                D("Setpageinfo")->rollback();
                $updat = D("Setpages")->where("setpages_id = $setPages[setpages_id]")->delete();
                return array('code'=>201, 'msg'=>'模板表头设置失败');
            }
        }
        D("Setpageinfo")->commit();     
        return array('code'=>0, 'msg'=>'修改成功');
    }

    /**
     * 删除设置模板
     * @author   Nxx
     */
    public function delSetPages($setPages)
    {
        $set['status'] = 0;
        $delInfo = D("Setpageinfo")->where("setpages_id = $setPages[setpages_id]")->delete();
        if ($delInfo === false) {
            return array('code'=>201,'msg'=>'失败');
        }
        $updateSetPages = D("Setpages")->where("setpages_id = $setPages[setpages_id] and status=1")->save($set);
        if ($updateSetPages === false) {
            return array('code'=>202,'msg'=>'失败');
        }
        return array('code'=>0,'msg'=>'成功');
    }

    /**
     * 查找设置模板详情
     * @author   Nxx
     */
    public function getSetPagesInfo($setpages_id)
    {
        $setPagesInfo = D("Setpageinfo")->where("setpages_id = $setpages_id")->select();
        return array('code'=>0,'data'=>$setPagesInfo);
    }

    /**
     * 获取客服代码
     * @author Nixx
    */
    public function getServicecode($servicecode)
    {
        $servicecode = D("Servicecode")->where($servicecode)->find();
        return array('code'=>0,'data'=>$servicecode);
    }

    // /**
    //  * 获取计划详情
    //  * @author Nixx
    // */
    // public function getPromInfo($promote_id)
    // {
    //     $proInfo = D("Promote")->where("promote_id = $promote_id and status=1")->find();
    //     if (!$proInfo) {
    //         return array('code'=>201,'msg'=>"获取详情失败");
    //     }
    //     return array('code'=>0,'data'=>$proInfo);
    // }

    /**
     * 获取指定计划,单条
     * @author Nixx
    */
    public function getPromoteInfo($promote)
    {
        $promote['status'] = 1;
        $proInfo = D("Promote")->getFind($promote);
        if (!$proInfo) {
            return array('code'=>201,'msg'=>'没有数据');
        }
        $proid = D("Proid")->getFind(array('proid_id' => $proInfo['proid_id'], 'status'=>1));
        if (!$proInfo['pcservice_id']) {
            $proInfo['pcservice_id'] = $proid['pcservice_id'];
        }
        if (!$proInfo['mservice_id']) {
            $proInfo['mservice_id'] = $proid['mservice_id'];
        }
        $pcservice = D("Servicecode")->getFind(array('servicecode_id'=>$proInfo['pcservice_id']));
        $proInfo['pcservice'] = $pcservice['url'];
        $mservice = D("Servicecode")->getFind(array('servicecode_id'=>$proInfo['mservice_id']));
        $proInfo['mservice'] = $mservice['url'];
        $proInfo['pc_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$proInfo['promote_id']}&dev=2";
        $proInfo['m_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$proInfo['promote_id']}&dev=1";
        return array('code'=>0,'data'=>$proInfo);
    }

    /**
     * 单个修改计划
     * @author Nixx
    */
    public function editPromote($promote)
    {
        $updatepromote = D("Promote")->editData($promote, $promote['promote_id']);
        if ($updatepromote['code'] != 0) {
            return array('code'=>$updatepromote['code'],'msg'=>$updatepromote['msg']);
        }
        return array('code'=>0,'msg'=>'修改成功');
    }


    /**
     * 根据关键字模糊搜索账号下所有计划
     * @author   Nxx
     */
    public function searchName($keyword, $proid_id)
    {
        $promote["status"] = array("eq",1);
        $promote["proid_id"] = array("eq",$proid_id);
        $promote["keyword"] = array("like","%$keyword%");
        $promoteList = D("Promote")->getList($promote); 
        if (!$promoteList) {
            return array('code'=>201,'msg'=>'没有数据');
        }
        return array('code'=>0,'data'=>$promoteList);
    }

    /**
     * 根据pro_lev_id获取所有计划
     * @author Nixx
    */
    public function getPromoteInfoByProlevid($pro_lev_id)
    {
        $prolev['status'] = 1;
        $prolev['pid'] = $pro_lev_id;
        $result = D("ProLev")->where($prolev)->select();    
        if ($result) {
            foreach ($result as $key => $res) {
                unset($res['proid_id']);
                unset($res['promote_id']);
                $rresult[] = $res;
            }
            return array('code'=>0,'data'=>$rresult);
        }
        $prolev['pro_lev_id'] = $pro_lev_id;
        unset($prolev['pid']);      
        $result = D("ProLev")->where($prolev)->find();
        unset($result['proid_id']);
        unset($result['promote_id']); 
        $rresult[] = $result;
        if ($rresult) {
            return array('code'=>0,'data'=>$rresult);
        }    
        return array('code'=>201,'msg'=>'没有数据');
    }

    /**
     * 获取指定计划列表
     * @author Nixx
    */
    public function getPromoteInfos($promote)
    {
        $promote['status'] = 1;
        $proInfos = D("Promote")->getList($promote);
        if (!$proInfos) {
            return array('code'=>0,'msg'=>'没有数据');
        }
        foreach ($proInfos as $key => $proInfo) {
            $proid = D("Proid")->where("proid_id = $proInfo[proid_id] and status=1")->find();
            if (!$proInfo['pcservice_id']) {
                $proInfo['pcservice_id'] = $proid['pcservice_id'];
            }
            if (!$proInfo['mservice_id']) {
                $proInfo['mservice_id'] = $proid['mservice_id'];
            }
            $pcservice = D("Servicecode")->where("servicecode_id = $proInfo[pcservice_id]")->find();
            $proInfo['pcservice'] = $pcservice['url'];
            $mservice = D("Servicecode")->where("servicecode_id = $proInfo[mservice_id]")->find();
            $proInfo['mservice'] = $mservice['url'];
            $proInfo['pc_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$proInfo['promote_id']}&dev=2";
            $proInfo['m_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$proInfo['promote_id']}&dev=1";
            $proInfos[$key] = $proInfo;
        }
        return array('code'=>0,'data'=>$proInfos);
    }

    /**
     * 批量修改计划
     * @author Nixx
    */ 
    public function editPromoteInfo($promote)
    {
        if ($promote['pro_lev_id']) {
            if ($promote['mark'] == 1) {  //计划
                $prolevList = D("ProLev")->where("pid = $promote[pro_lev_id] and status=1")->select();
                foreach ($prolevList as $key => $plv) {
                    if (!$pro_lev_ids) {
                        $pro_lev_ids = $plv['pro_lev_id'];
                    }else{
                        $pro_lev_ids = $pro_lev_ids.",$plv[pro_lev_id]";
                    }
                }
                $where['pro_lev_id'] = array("IN", $pro_lev_ids);
                $where['status'] = 1;
                $proList = D("Promote")->where($where)->select();
            }elseif($promote['mark'] == 2){  //单元
                $proList = D("Promote")->where("pro_lev_id = $promote[pro_lev_id] and status=1")->select();
            }
            $includedString = $this->getPromoteidString($proList);      
            if (!$includedString) {
                return array('code'=>301,'msg'=>'没有数据');
            }
            $promotes['promote_id'] = array("IN", $includedString);
            $promotes['status'] = 1;
            unset($promote['pro_lev_id']);
            unset($promote['mark']);
            unset($promote['proid_id']);

            $updatepromote = D("Promote")->where($promotes)->save($promote);
            if ($updatepromote === false) {
                return array('code'=>202,'msg'=>'修改失败');
            }
        }else{
            $proid_id = $promote['proid_id'];
            $proid['pc_pages_id'] = $promote['pc_pages_id'];
            $proid['m_pages_id'] = $promote['m_pages_id'];
            $updatepromote = D("Promote")->where("proid_id = $proid_id")->save($proid);
            if ($updatepromote === false) {
                return array('code'=>203,'msg'=>'修改失败');
            }
        }
        return array('code'=>0,'msg'=>'修改成功');
    }

    /**
     * 获取推广账号
     * @author Nixx
    */
    public function getProList($pro = null, $limit='0,10')
    {
        $order = 'createtime desc';
        $getProList['data'] =  D("Proid")->where($pro)->order($order)->limit($limit)->select();
        $getProList['count'] =  D("Proid")->where($pro)->count();
        foreach ($getProList['data'] as $key => $pro) {
            $channe = D("Channel")->where("channel_id = $pro[channel_id]")->find();
            $getProList['data'][$key]['channelname'] = $channe['channelname'];
            if ($pro['pcservice_id']) {
                $pcservice = D("Servicecode")->where("servicecode_id = $pro[pcservice_id]")->find();
                $getProList['data'][$key]['pcservice'] = $pcservice['title'];
            }
            if ($pro['mservice_id']) {
                $mservice = D("Servicecode")->where("servicecode_id = $pro[mservice_id]")->find();
                $getProList['data'][$key]['mservice'] = $mservice['title'];
            }
        }   
        return array('code'=>0,'data'=>$getProList);
    }

    /*
    * 拼接推广计划id字符串查询条件
    * @author Nixx
    */
    public function getPromoteidString($proList)
    {

        foreach($proList as $pros){
            if (!$includedString) {
                $includedString = "$pros[promote_id]";
            }else{
                $includedString = $includedString . ",$pros[promote_id]";
            }
        }
        return $includedString;
    }

    /*
    导入推广计划
    */
    public function inputPlan($datas, $pro, $request, $proid_id)
    {
        session('faile_input', null);
        session('success_input', null);
        session('newPromoteList', null);
        session('letters', null);
        /*对生成的数组进行字段对接*/
        foreach ($pro as $key => $p) {
            foreach ($datas as $k => $v){
                if ($k>1) {
                    for ($i=0; $i < count($v); $i++) {
                        $keys = array_keys($v);
                        foreach ($keys as $k2 => $v1) {
                            if ($p[0] == $v1) {
                                $promoteList[$k-2]["$p[1]"] = $v[$v1];
                            }
                        }
                    }
                    $promoteList[$k-2]['proid_id'] = $proid_id;
                    $promoteList[$k-2]['pc_pages_id'] = $request['pcPagesType_id'];
                    $promoteList[$k-2]['m_pages_id'] = $request['mPagesType_id'];
                }
            }
        }
        //对数据进行处理
        foreach ($promoteList as $key => $promote) {
            $servicecode['system_user_id'] = $this->system_user_id;
            $servicecode['status'] = 1;
            if ($promote['pcservice']) {
                $servicecode['servicecode'] = $promote['pcservice'];
                $servicecode['terminal_id'] = 2; //PC端
                $pcsercode = $this->getServicecode($servicecode);
                $pcserviceInfo = $pcsercode['data'];
                if ($pcserviceInfo) {
                    $promote['pcservice_id'] = $pcserviceInfo['servicecode_id'];
                }else{
                    $pcser = $this->addServicecode($servicecode);
                    $pcservice_id = $pcser['data'];
                    if ($pcservice_id) {
                        $promote['pcservice_id'] = $pcservice_id;
                    }
                }
            }else{
                $promote['pcservice_id'] = $proidInfo['pcservice_id'];
            }
            if ($promote['mservice']) {
                $servicecode['servicecode'] = $promote['mservice'];
                $servicecode['terminal_id'] = 1; //移动端
                $msercode = $this->getServicecode($servicecode);
                $mserviceInfo = $msercode['data'];
                if ($mserviceInfo) {
                    $promote['mservice_id'] = $mserviceInfo['servicecode_id'];
                }else{
                    $mser = $this->addServicecode($servicecode);
                    $mservice_id = $mser['data'];
                    if ($mservice_id) {
                        $promote['mservice_id'] = $mservice_id;
                    }
                }
            }else{
                $promote['mservice_id'] = $proidInfo['mservice_id'];
            }             
            if ($promote['pc_pages']) {
                preg_match("/promote=([0-9]*)/", $promote['pc_pages'], $m);
                if($m){
                    $promote['promote_id']=$m[1];
                } 
            }elseif($promote['m_pages']){
                preg_match("/promote=([0-9]*)/", $promote['pc_pages'], $m);
                if($m){
                    $promote['promote_id']=$m[1];
                } 
            }
            unset($promote['pc_pages']);
            unset($promote['m_pages']);
            if (!$promote['keyword'] || (!$promote['plan'] && $promote['planunit']) || ($promote['plan'] && !$promote['planunit'])) {
                $errorData[$key] = $promoteList[$key];
                $errorData[$key]['msg'] = '缺少关键字、有单元无计划，不是请联系程序猿哥哥';
                unset($promoteList[$key]);
            }
            $promoteList[$key] = $promote;
        }
        foreach ($promoteList as $key => $promote) {               
            if ($promote['promote_id']) {
                $info = $this->getPromoteInfo(array('promote_id'=>$promote['promote_id']));
                $promoteInfo = $info['data'];
            }else{
                $pross['plan'] = $promote['plan'];
                $pross['planunit'] = $promote['planunit'];
                $pross['keyword'] = $promote['keyword'];
                $pross['proid_id'] = $proid_id;
                $info = $this->getPromoteInfo($pross);
                $promoteInfo = $info['data'];
            }
            if ($promoteInfo) {
                if (!$promote['pc_pages_id']) {
                    $promote['pc_pages_id'] = $promoteInfo['pc_pages_id'];
                }
                if (!$promote['m_pages_id']) {
                    $promote['m_pages_id'] = $promoteInfo['m_pages_id'];
                }
                $promote['promote_id'] = $promoteInfo['promote_id'];
                $updatepromote = $this->editPromote($promote);
                $own['promote_id'] = $promote['promote_id'];
                $proInfo = $this->getPromoteInfo($own);
                $ps['plan'] = $proInfo['data']['plan'];
                $ps['planunit'] = $proInfo['data']['planunit'];
                $ps['keyword'] = $proInfo['data']['keyword'];
                $ps['pc_pages'] = $proInfo['data']['pc_pages'];
                $ps['m_pages'] = $proInfo['data']['m_pages'];
                $newPromoteList[$key] = $ps;
            }else{
                $prolev['proid_id'] = $promote['proid_id'];
                $prolev['status'] = 1;
                //只有计划没有单元
                if ($promote['plan'] && !$promote['planunit']) {
                    unset($promote['planunit']);
                    $prolev['name'] = $promote['plan'];
                    $proLevInfo = $this->getProLevInfo($prolev);
                    if ($proLevInfo['code'] != 0) {
                        $pro_lev_id = $this->createProLev($prolev);
                        $promote['pro_lev_id'] = $pro_lev_id['data'];
                    }else{
                        $promote['pro_lev_id'] = $proLevInfo['data']['pro_lev_id'];
                    }
                }//有计划有单元
                 elseif ($promote['plan'] && $promote['planunit']) {
                    $prolev['name'] = $promote['plan'];
                    $proLevInfo = $this->getProLevInfo($prolev);
                    if ($proLevInfo['code'] != 0) {
                        $plan_lev_id = $this->createProLev($prolev);
                        $prolev['name'] = $promote['planunit'];
                        $prolev['pid'] = $plan_lev_id['data'];
                        $pro_lev_id = $this->createProLev($prolev);
                        $prolev['pid'] = 0; //重置pid为0
                        $promote['pro_lev_id'] = $pro_lev_id['data'];
                    }else{
                        $prolev['name'] = $promote['planunit'];
                        $prolev['pid'] = $proLevInfo['data']['pro_lev_id'];
                        $punitLevInfo = $this->getProLevInfo($prolev);
                        if ($punitLevInfo['code'] == 0) {
                            $promote['pro_lev_id'] = $punitLevInfo['data']['pro_lev_id'];
                        }else{
                            $pro_lev_id = $this->createProLev($prolev);
                            $promote['pro_lev_id'] = $pro_lev_id['data'];
                        }
                        $prolev['pid'] = 0; //重置pid为0
                    }   
                }
                $result = $this->createPromote($promote);
                if ($result['code'] != 0) {
                    unset($promoteList[$key]);
                    continue;
                }else{
                    $own['promote_id'] = $result['data'];
                    $info = $this->getPromoteInfo($own);
                    if ($info['code'] == 0) {
                        $proInfo = $info['data'];
                        $ps['plan'] = $proInfo['plan'];
                        $ps['planunit'] = $proInfo['planunit'];
                        $ps['keyword'] = $proInfo['keyword'];
                        $ps['pc_pages'] = $proInfo['pc_pages'];
                        $ps['m_pages'] = $proInfo['m_pages'];
                        $newPromoteList[$key] = $ps;
                    }
                }
            }
        }
        session('faile_input', $errorData);
        session('success_input', $promoteList);
        session('newPromoteList', $newPromoteList);           
        session('letters', $datas[1]);   
    }

    /*
    * 导出计划数据处理
    */
    public function outputPlan($request, $proid_id)
    {
        $excel_key_value = array(
            'plan'=>'推广计划名称',
            'planunit'=>'推广单元名称',
            'keyword'=>'关键词名称',
            'pc_pages'=>'PC模板',
            'm_pages'=>'移动模板',
        );
        $letter = array('A','B','C','D','E');
        $new_result = array();
        if(!$proid_id) {
            return array('code'=>301, 'msg'=>'缺省参数');
        }
        //若带了关键词则只导出该计划，否则导出所有单元或者该账号下所有计划
        if (!empty($request['keyword'])) {
            $pList = $this->searchName($request['keyword'], $proid_id);
            $promoteList = $pList['data'];
            if ($pList['code'] != 0) {
                return array('code'=>$pList['code'], 'msg'=>$pList['msg']);
            }
            foreach ($promoteList as $key => $promote) {
                $p['promote_id'] = $promote['promote_id'];
                $datas = $this->getPromoteInfo($p);
                $result = $datas['data'];
                unset($result['proid_id']);
                unset($result['promote_id']);
                $rresult[$key] = $result;
            }
            foreach ($rresult as $key => $value) {
                foreach ($excel_key_value as $key2 => $value2) {
                    $new_result[$key][$key2] = $value[$key2];
                }
            }            
        }elseif($request['pro_lev_id'] || $request['pro_lev_ids']){
            if ($request['pro_lev_id']) {
                $datas = $this->getPromoteInfoByProlevid($request['pro_lev_id']);
                if($datas['code'] != 0) {
                    return array('code'=>$datas['code'], 'msg'=>$datas['msg']);
                }
            }elseif ($request['pro_lev_ids']) {
                $datas = $this->getPromoteInfoByProlevid($request['pro_lev_ids']);
                if($datas['code'] != 0) {
                    return array('code'=>$datas['code'], 'msg'=>$datas['msg']);
                } 
            }
            $result = $datas['data'];
            foreach ($result as $key => $res) {
                unset($res['pid']);
                unset($res['name']);
                unset($res['remark']);
                unset($res['status']);
                $sel[] = $res['pro_lev_id'];
            }
            $res['pro_lev_id'] = array("IN", $sel);
            $p = $this->getPromoteInfos($res);
            $promoteList = $p['data'];
            foreach ($promoteList as $k => $value) {        
                foreach ($excel_key_value as $key => $value1) {
                    $new_result[$k][$key] = $value[$key];
                }
            }
        }else{
            $pro['proid_id'] = $proid_id;
            $p = $this->getPromoteInfos($pro);
            $promoteList = $p['data'];
           
            foreach ($promoteList as $k => $value) {        
                foreach ($excel_key_value as $key => $value1) {
                    $new_result[$k][$key] = $value[$key];
                }
            }   
        }
        $filename = "proPlan";
        return array('code'=>0, 'data'=>outExecl($filename,array_values($excel_key_value),$new_result,$letter));
    }

}