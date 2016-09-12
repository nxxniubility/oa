<?php
/*
|--------------------------------------------------------------------------
| Base基础模型
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：
| updatename：
*/
namespace Common\Model;
use Think\Model;

class BaseModel extends Model{
    
    // 写入数据前的回调方法 包括新增和更新
    protected function _before_write(&$data) {
        // echo __FUNCTION__;
        // print_r($data);
    }
    
    // 插入数据前的回调方法
    protected function _before_insert(&$data,$options) {
        // echo __FUNCTION__;
        // print_r($data);
        // print_r($options);
    }
    
    // 插入成功后的回调方法
    protected function _after_insert($data,$options) {
        // echo __FUNCTION__;
        // print_r($data);
        // print_r($options);
    }
    
    // 更新数据前的回调方法
    protected function _before_update(&$data,$options) {

//        echo __FUNCTION__;
//        print_r($data);
//        print_r($options);exit;
    }
    // 更新成功后的回调方法
    protected function _after_update($data,$options) {
//         echo __FUNCTION__;
//         print_r($data);
//         print_r($options);exit;
    }
    
    // 删除数据前的回调方法
    protected function _before_delete($options) {
        // echo __FUNCTION__;
        // print_r($options);
    }
    
    // 删除成功后的回调方法
    protected function _after_delete($data,$options) {
        // echo __FUNCTION__;
        // print_r($data);
        // print_r($options);
    }
    
    // 查询成功后的回调方法
    protected function _after_select(&$resultSet,$options) {
        // echo __FUNCTION__;
        // print_r($resultSet);
        // print_r($options);
    }
    public function batch_update($key_value_arr,$key_field,$update_field)
	{
	   if(empty($key_value_arr))return false;
	   $sql = "UPDATE ".$this->getTableName()." SET ".$update_field." = CASE  ".$key_field."  "; 
	   foreach ($key_value_arr as $id => $value) {
		   
			$sql .= sprintf("  WHEN %d THEN %d ", $id, $value)."  ";
	   }	
	   $ids=array_keys($key_value_arr);
	   $sql .= "  END WHERE ".$key_field." IN (".implode(',',$ids).")";	
	   return $this->execute($sql);
	}
    /**
     * 记录日志
     * @param  $idus 增=1 删=2 改=3 查=4
     * @param  $old_data 旧数据数组
     * @param  $new_data 新数据数组
     * @param  $options 条件数组
     */
    private function logs($idus,$old_data,$new_data,$options) {
        
    }

}