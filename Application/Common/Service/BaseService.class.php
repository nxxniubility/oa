<?php
/*
* Cookie服务接口
* @author luoyu
*
*/
namespace Common\Service;

use Common\Model\BaseModel;

class BaseService extends BaseModel {
	
	//不检查数据库，虚拟表
    protected $autoCheckFields = false,$system_user_id,$system_user;
	
    //初始化
    public function _initialize() {
        parent::_initialize();
        $this->system_user_id = session('system_user_id');
        $this->system_user = session('system_user');
    }
	/**
     * 开启事务
     * @author Echo
     */
    public function startTrans() {
        M()->startTrans();
    }

    /**
     * 提交事务
     * @author Echo
     */
    public function commit() {
        M()->commit();
    }

    /**
     * 回滚事务
     * @author Echo
     */
    public function rollback() {
        M()->rollback();
    }

    /*
    * 对缓存数据进行处理
    * $array =array('data'=>'', 'count'=>) $order="id asc"  $page="1,10" 页码,显示数 $where=array(''=>)
    * @author zgt
    */
    protected function disposeArray($array,$order=null,$page=null,$where=null){
        if(!empty($array['data'])){
            $array_list = $array['data'];
        }else{
            $array_list = $array;
        }
        //对缓存条件筛选 XXXX 模糊搜索
        unset($where['page']);unset($where['order']);
        if(!empty($where)){
            $where = array_filter($where);
            foreach($where as $k=>$v){
                $array_list = $this->disposeArray_where($array_list ,$k ,$v);
            }
        }
        //对缓存数据进行排序
        if(!empty($order)){
            $order = explode(' ', $order);
            uasort($array_list, function($a, $b) use($order) {
                $al = ($a[$order[0]]);
                $bl = ($b[$order[0]]);
                if($al==$bl)return 0;
                if($order[1]=='asc')return ($al<$bl)?-1:1;
                else return ($al>$bl)?-1:1;
            });
            $array_list = array_values($array_list);
        }
        if(!empty($array['count'])) $array['count'] = count($array_list);
        //对缓存进行分页
        if(!empty($page)){
            //分页数据
            $page = explode(',', $page);
            $department_new = $array_list;
            $array_list = null;
            foreach($department_new as $k=>$v){
                if($k>=(($page[0])) && $k<($page[0]+$page[1])){
                    $array_list[] = $v;
                }
            }
        }
        $array['data'] = $array_list;
        return $array;
    }
    
    //对缓存条件筛选
    public function disposeArray_where($array, $key, $value){
        $value_link = null;
        foreach($array as $k=>$v){
            if(is_array($value)){
                if(!is_array($value[1])) $value[1] = explode(',', $value[1]);
                if(strtoupper($value[0])=='IN'){
                    if(is_array($v[$key])){
                        if(array_intersect($v[$key],$value[1])){
                            $department_new[] = $v;
                        }
                    }elseif(in_array($v[$key],$value[1])) {
                        $department_new[] = $v;
                    }
                }elseif(strtoupper($value[0])=='NEQ'){
                    if(!in_array($v[$key],$value[1])) $department_new[] = $v;
                }elseif(strtoupper($value[0])=='LIKE'){
                    if(strpos($v[$key], $value[1][0])!==false) $department_new[] = $v;
                }
            }else{
                if( count(explode(',', $value))>1 ){
                    $value = $value_arr = explode(',', $value);
                    unset($value_arr[0]);
                    if(strtoupper($value[0])=='IN'){
                        if(is_array($v[$key])){
                            if(array_intersect($v[$key],$value_arr)){
                                $department_new[] = $v;
                            }
                        }elseif(in_array($v[$key],$value_arr)) {
                            $department_new[] = $v;
                        }
                    }elseif(strtoupper($value[0])=='NEQ'){
                        if(!in_array($v[$key],$value_arr)) $department_new[] = $v;
                    }elseif(strtoupper($value[0])=='LIKE'){
                        if(strpos($v[$key], $value_arr[0])!==false) $department_new[] = $v;
                    }elseif(in_array($v[$key],$value)){
                        $department_new[] = $v;
                    }
                }else{
                    if(is_array($v[$key])){
                        if(in_array($value, $v[$key])){
                            $department_new[] = $v;
                        }
                    }elseif($v[$key]==$value) {
                        $department_new[] = $v;
                    }
                }
            }
        }
        return $department_new;
    }
}