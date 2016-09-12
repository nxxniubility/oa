<?php
/*
|--------------------------------------------------------------------------
| 模板表模型
|--------------------------------------------------------------------------
| createtime：2016-05-03
| updatetime：2016-05-03
| updatename：zgt
*/
namespace Common\Model;

use Common\Model\SystemModel;

class PagesModel extends SystemModel
{
    protected $pagesDb;

    public function _initialize()
    {
    }

    public function getPagesList($request)
    {
        if ($request['pagestype_id'] == 0) {
            $pagesList['data'] = M('pages')->where("terminal_id = $request[terminal_id] and status=1")->select();
        }else{
            $pagesList['data'] = M('pages')->where($request)->select();
        }
        return $pagesList;
    }
    /*
     * 获取所有模板内容-缓存
     * @author zgt
     * @return array
     */
    public function getAllPages($order='createtime desc',$page=null,$where=array('status'=>1))
    {
        $DB_PREFIX = C('DB_PREFIX');
        if (F('Cache/Promote/pages')) {
            $pagesAll = F('Cache/Promote/pages');
        } else {
            $pagesAll['data'] = $this
                ->where($where)
                ->order($DB_PREFIX.'pages.createtime DESC')
                ->select();
            $pagesAll['count'] = $this->count();
            F('Cache/Promote/pages',$pagesAll);
        }

        $pagesAll = $this->disposeArray($pagesAll, $order, $page, $where);
        return $pagesAll;
    }

    /*
     * 获取所有模板分类
     * @author nxx
     * @return array
     */
    public function getPagesType($data)
    {
        $data['status'] = 1;
        return M("pages_type")->where($data)->select();
    }

    /*
     * 获取模板内容-缓存
     * @author zgt
     * @return array
     */
    public function getPagesInfo($pages_id)
    {
        if (F('Cache/Promote/pages')) {
            $pagesAll = F('Cache/Promote/pages');
            foreach($pagesAll['data'] as $k=>$v){
                if($v['pages_id']==$pages_id){
                    $pagesInfo = $v;
                }
            }
        } else {
            $pagesInfo = $this->where(array('pages_id'=>$pages_id))->find();
        }
        return $pagesInfo;
    }

    /*
     * 获取模板相关联计划列表
     * @author zgt
     * @return array
     */
    public function getPagesPromote($pages_id,$where=null,$limit='0,10')
    {
        $DB_PREFIX = C('DB_PREFIX');
        $where["{$DB_PREFIX}promote.status"] = 1;
        $where["{$DB_PREFIX}promote.pc_pages_id|{$DB_PREFIX}promote.m_pages_id"] = $pages_id;
        $promoteList['data'] = M('promote')
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

        $promoteList['count'] = M('promote')
            ->join("LEFT JOIN __PROID__ on __PROID__.proid_id=__PROMOTE__.proid_id")
            ->join("LEFT JOIN __CHANNEL__ on __CHANNEL__.channel_id=__PROID__.channel_id")
            ->join("LEFT JOIN __SYSTEM_USER__ on __SYSTEM_USER__.system_user_id=__PROID__.system_user_id")
            ->where($where)
            ->count();

        return $promoteList;
    }

    /*
     * 根据terminal_id获取模板内容-缓存
     * @author zgt
     * @return array
     */
    public function getPages($terminal_id)
    {
        if (F('Cache/Promote/pages')) {
            $pagesAll = F('Cache/Promote/pages');
            foreach($pagesAll['data'] as $k=>$v){
                if($v['terminal_id']==$terminal_id){
                    $pagesInfo = $v;
                }
            }
        } else {
            $pagesInfo = $this->where(array('terminal_id'=>$terminal_id))->find();
        }
        return $pagesInfo;
    }

    /**
     * 添加模板
     * @author zgt
     */
    public function addPages($data)
    {
        $data['status'] = 1;
        $data['createtime'] = time();
        $result = $this->data($data)->add();
        if ($result!==false){
            $data['pages_id'] = $result;
            if (F('Cache/Promote/pages')) {
                $pagesAll = F('Cache/Promote/pages');
                $pagesAll['data'][] = $data;
                $pagesAll['count'] = ($pagesAll['count']+1);
                F('Cache/Promote/pages', $pagesAll);
            }
            return $result;
        }
        return false;
    }

    /**
     * 修改模板
     * @author zgt
     */
    public function editPages($data, $pages_id)
    {
        $result = $this->where(array('pages_id'=>$pages_id))->save($data);
        if ($result!==false){
            if (F('Cache/Promote/pages')) {
                $newInfo = $this->where(array('pages_id'=>$pages_id))->find();
                $pagesAll = F('Cache/Promote/pages');
                foreach($pagesAll['data'] as $k=>$v){
                    if($v['pages_id'] == $pages_id){
                        $pagesAll['data'][$k] = $newInfo;
                    }
                }
                F('Cache/Promote/pages', $pagesAll);
            }
            return true;
        }
        return false;
    }
    /**
     * 获取模板 导航
     * @author zgt
     */
    public function getPagesNav($pages_id)
    {
        $DB_PREFIX = C('DB_PREFIX');
        return M('pagesnav')
            ->where(array($DB_PREFIX.'pagesnav.pages_id'=>$pages_id))
            ->join('__PAGES__ ON __PAGES__.pages_id=__PAGESNAV__.pages_nav_id')
            ->order($DB_PREFIX.'pagesnav.sort asc')
            ->select();
    }
    /**
     * 添加模板 导航
     * @author zgt
     */
    public function addPagesNav($data,$pages_id)
    {
        $add['pages_id'] = $pages_id;
        M('pagesnav')->where(array('pages_id'=>$pages_id))->delete();
        if(!empty($data)){
            $data = explode(',', $data);
            foreach($data as $k=>$v){
                $v = explode('@@', $v);
                $add['pages_nav_id'] = $v[0];
                $add['nav_name'] = $v[1];
                $add['sort'] = ($k+1);
                $result = M('pagesnav')->data($add)->add();
                if(!$result) return false;
            }
        }
        return true;
    }

    /*
     * 获取模板分类-缓存
     * @author zgt
     * @return array
     */
    public function getAllPagesType()
    {
        if (F('Cache/Promote/pagesType')) {
            $pagesTypeAll = F('Cache/Promote/pagesType');
        } else {
            $pagesTypeAll = M('pages_type')->where(array('status'=>1))->select();
            F('Cache/Promote/pagesType',$pagesTypeAll);
        }

        return $pagesTypeAll;
    }

    /**
     * 添加模板分类
     * @author zgt
     */
    public function addPagesType($data)
    {
        $data['status'] = 1;
        $result = M('pages_type')->data($data)->add();
        if ($result!==false){
            $data['pagestype_id'] = $result;
            if (F('Cache/Promote/pagesType')) {
                $cacheAll = F('Cache/Promote/pagesType');
                $cacheAll[] = $data;
                F('Cache/Promote/pagesType', $cacheAll);
            }
            return $result;
        }
        return false;
    }

    /**
     * 修改模板分类
     * @author zgt
     */
    public function editPagesType($data,$pagestype_id)
    {
        $result = M('pages_type')->where(array('pagestype_id'=>$pagestype_id))->save($data);
        if ($result!==false){
            if (F('Cache/Promote/pagesType')) {
                $newInfo = M('pages_type')->where(array('pagestype_id'=>$pagestype_id))->find();
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
            return true;
        }
        return false;
    }
    /**
     * 查看模板备注
     * @author zgt
     */
    public function getPagesRemark($pages_id,$system_user_id)
    {
        $remarklt = M('pages_remark')->where(array('pages_id'=>$pages_id,'system_user_id'=>$system_user_id))->find();
        return $remarklt;
    }

    /**
     * 添加模板备注
     * @author zgt
     */
    public function addPagesRemark($remark,$pages_id,$system_user_id)
    {
        $data['remark'] = $remark;
        $remarklt = M('pages_remark')->where(array('pages_id'=>$pages_id,'system_user_id'=>$system_user_id))->find();
        if (empty($remarklt)){
            $data['pages_id'] = $pages_id;
            $data['system_user_id'] = $system_user_id;
            $result = M('pages_remark')->data($data)->add();
        }else{
            $result = M('pages_remark')->where(array('pages_id'=>$pages_id,'system_user_id'=>$system_user_id))->save($data);
        }
        if($result!==false) return true;
        return false;
    }

    public function getAllPagesnav($where,$field){
        return M('pagesnav')->where($where)->field($field)->select();
    }
}