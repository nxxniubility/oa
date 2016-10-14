<?php
/*
|--------------------------------------------------------------------------
| System基础模型
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：
| updatename：
*/
namespace Common\Model;
use Common\Model\BaseModel;

class SystemModel extends BaseModel{

    /*
     * 对缓存数据进行处理
     * $array =array('data'=>'', 'count'=>) $order="id asc"  $page="1,10" 页码,显示数 $where=array(''=>)
     * @author zgt
    */
    protected function disposeArray($array,$order=null,$page=null,$where=null){
        //对缓存数据进行排序
        if(!empty($order)){
            $order = explode(' ', $order);
            uasort($array['data'], function($a, $b) use($order) {
                $al = ($a[$order[0]]);
                $bl = ($b[$order[0]]);
                if($al==$bl)return 0;
                if($order[1]=='asc')return ($al<$bl)?-1:1;
                else return ($al>$bl)?-1:1;
            });
        }
        //对缓存条件筛选 %%XXXX 模糊搜索
        if(!empty($where)){
            foreach($where as $k=>$v){
                if(!empty($v)) $array['data'] = $this->disposeArray_where($array['data'] ,$k ,$v);
            }
        }
        $array['count'] = count($array['data']);
        //对缓存进行分页
        if(!empty($page)){
            //分页数据
            $page = explode(',', $page);
            foreach($array['data'] as $k=>$v){
                $department_new[] = $v;
            }
            $array['data'] = null;
            foreach($department_new as $k=>$v){
                if($k>=(($page[0]-1)*$page[1]) && $k<($page[0]*$page[1])){
                    $array['data'][] = $v;
                }
            }
        }

        return $array;
    }
    //对缓存条件筛选
    public function disposeArray_where($array, $key, $value){
        $value_link = null;
        if(strpos($value,'%%')!==false) {
            $value_link = explode('%%', $value);
        }
        foreach($array as $k=>$v){
            if(!empty($value_link[1])){
                if(strpos($v[$key], $value_link[1])!==false) $department_new[] = $v;
            }else{
                if($v[$key]==$value) $department_new[] = $v;
            }
        }
        return $department_new;
    }


    public function getNum($data){
        $array =array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $nums = count($array);
        for ($i=0; $i < $nums; $i++) { 
            if ($array[$i] === $data) {
                $num = $i+1;
                return $num;
            }
        }
    }



}