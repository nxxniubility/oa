<?php
namespace Org\Arrayhelps;

/**
 * 数组处理类
 * @author Sunles
 * 
 */
class Arrayhelps{
    
    /**
     * 数组无限分级
     * @author  Sunles
     * @return array
     */
    public function createTree($array,$pid=0,$idname="id",$pidname="pid"){
        $result = array();
        foreach($array as $key => $val){
            if($val[$pidname] == $pid) {
                $tmp = $array[$key];unset($array[$key]);
                count($this->createTree($array,$val[$idname],$idname,$pidname)) > 0 && $tmp['children'] = $this->createTree($array,$val[$idname],$idname,$pidname);
                $result[$key] = $tmp;
            }
        }
        return $result;
    }
    /**
     * 数组无限分级
     * @author  Echo
     * @return array
     */
    public function tree($array,$id,$idname='id',$pidname='pid') {
        $result = array();
        foreach($array as $k=>$v) {
            if ($v[$pidname] == $id) {
                unset($array[$k]);
                $result[$v[$idname]] = $v;
                $result[$v[$idname]]['children'] = $this->tree($array,$v[$idname],$idname,$pidname);
            }
        }
        return $result;
    }
    /**
     * 数组无限分级
     * @author  Echo
     * @return array
     */
    public function son($array,$id,$idname='id',$pidname='pid') {
        static $result = array();
        foreach($array as $k=>$v) {
            if ($v[$pidname] == $id) {
                unset($array[$k]);
                array_push($result,$v);
                $this->son($array,$v[$idname],$idname,$pidname);
            }
        }
        return $result;
    }
    /**
     * 指定子级ID找父级
     * @author Sunles
     * @return array
     */
    public function parentFind($array,$id,$idname="id",$pidname="pid"){
        $arr = array();
        foreach($array as $k => $v){
            if($id == $v[$idname]){
                if($v[$pidname] != 0){
                    $arr = $this->parentFind($array, $v[$pidname],$idname,$pidname);
                }
                $arr[] = $v;
            }
        }
        return $arr;
    }
    
    /**
     * 指定父级ID找子集
     * @author Sunles
     * @return array
     */
    public function subFind($array,$pid,$idname="id",$pidname="pid"){
        $arr= array();
        foreach($array as $k => $v){
            if($pid == $v[$pidname]){
                $sub = $this->subFind($array, $v[$idname],$idname,$pidname);
                if(!empty($sub)) $arr[] = $sub;
                $arr[] = $v;
            }
        }
        return $arr;
    }
    
    /**
     * 指定父级ID找子集 一维数组
     * @author Sunles
     * @return array
     */
    public function subFinds($array,$id,$idname="id",$pidname="pid",$arr=""){
        
        foreach($array as $k => $v){
            if($id == $v[$pidname]){
                $arr[] = $v;
                $arr = $this->subFinds($array, $v[$idname],$idname,$pidname,$arr);
            }
        }
        return $arr;
    }
}