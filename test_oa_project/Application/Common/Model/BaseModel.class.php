<?php
namespace Common\Model;
use Think\Model;
use Org\Util\Tool;
/**
 * 基础模型
 * @author Echo
 */
class BaseModel extends ValidateModel {
    
    protected $cacheOptions = array(); //保留缓存的key

    // 写入数据前的回调方法 包括新增和更新
    protected function _before_write(&$data) {
        // echo __FUNCTION__;
        // print_r($data);
    }

    // 插入数据前的回调方法
    protected function _before_insert(&$data, $options) {
        // echo __FUNCTION__;
        // print_r($data);
        // print_r($options);
    }

    // 插入成功后的回调方法
    protected function _after_insert($data, $options) {
        // echo __FUNCTION__;
        // print_r($data);
        // print_r($options);
    }

    // 更新数据前的回调方法
    protected function _before_update(&$data, $options) {
        
    }

    // 更新成功后的回调方法
    protected function _after_update($data, $options) {
        
    }

    // 删除数据前的回调方法
    protected function _before_delete($options) {
        // echo __FUNCTION__;
        // print_r($options);
    }

    // 删除成功后的回调方法
    protected function _after_delete($data, $options) {
        // echo __FUNCTION__;
        // print_r($data);
        // print_r($options);
    }

    // 查询成功后的回调方法
    protected function _after_select(&$resultSet, $options) {
        // echo __FUNCTION__;
        // print_r($resultSet);
        // print_r($options);
    }

    //查询前的回调方法
    protected function _before_select() {
        
    }
    /**
     * 新增数据
     * @author shipeng
     */
    public function getAdd($data){
        $addid = $this->add($data);
        if($addid){
            return array('status'=>true,'id'=>$addid);
        }else{
            return array('status'=>false,'id'=>'');
        }
    }
    /**
     * 获取单条数据
     * @param 条件 $where
     * @param 字段 $field
     * @return Ambigous <mixed, boolean, NULL, string, unknown, multitype:, object>
     */
   
    public function getOne($where,$field="*",$order='',$join=''){
        return $this->getFind($where, $field,$order,$join);
    }
    /**
     * @param 当前页面参数 $page
     * @param 每页数据 $rowCoung
     */
    public function limitPage($page, $rowCoung = 15) {
        $page = (int) $page;
        if ($page < 1)
            $page = 1;
        $this->limit(($page - 1) * $rowCoung, $rowCoung);
        return $this;
    }

    /**
     * @author xanxus
     * 查询一条数据
     * @author xanxus
     * @param $where 条件
     * @param $field 显示字段
     * @param $order 排序
     * @param $limit 
     * @param $join
     * @param $group
     * @param $cacheEnabled  是否开启缓存	
     */
    public function getFind($where = array(), $field = '*', $order = '', $join = '', $group = '', $cacheEnabled = false) {
        $select = $this->where($where)->field($field);
        if (!empty($join))
            $select->join($join);
        if (!empty($order))
            $select->order($order);
        if (!empty($group))
            $select->group($group);
        if ($cacheEnabled)
            $select->cache(true);
        return $select->find();
    }

    /**
     * @author xanxus
     * 类似 getField
     * @author xanxus
     * @param $where 条件
     * @param $field 显示字段
     * @param $sepa 
     * @param $cacheEnabled  是否开启缓存	
     */
    public function getFieldBy($where = array(), $field, $sepa = null, $cacheEnabled = false) {
        $select = $this->where($where);
        if ($cacheEnabled)
            $select->cache(true);
        return $select->getField($field, $sepa);
    }

    /**
     * @author xanxus
     * 查询所有数据
     * @author xanxus
     * @param $where 条件
     * @param $field 显示字段
     * @param $order 排序
     * @param $limit 
     * @param $join
     * @param $group
     * @param $cacheEnabled  是否开启缓存	
     */
    public function getSelect($where = array(), $field = '*', $order = '', $limit = '', $join = '', $group = '', $cacheEnabled = false) {
        $select = $this->where($where)->field($field);
        if (!empty($join))
            $select->join($join);
        if (!empty($order))
            $select->order($order);
        if (!empty($limit))
            $select->limit($limit);
        if (!empty($group))
            $select->group($group);
        if ($cacheEnabled)
            $select->cache(true);
        return $select->select();
    }

    /**
     * 
     * @author xanxus
     * 查询所有数据
     * @author xanxus
     * @param $where 条件
     * @param $field 显示字段
     * @param $join
     * @param $group
     * @param $cacheEnabled  是否开启缓存	
     */
    public function getResultCount($where = array(), $field = '*', $join = '', $group = '', $cacheEnabled = false) {
        if (!empty($join) && !empty($group)) {
            $select = $this->field("count(*)")->where($where)->join($join)->group($group);
            if ($cacheEnabled)
                $select->cache(true);
            $result = $select->select();
        }else if (empty($join) && !empty($group)) {
            $select = $this->field("count(*)")->where($where)->group($group);
            if ($cacheEnabled)
                $select->cache(true);
            $result = $select->select();
        }else if (!empty($join) && empty($group)) {
            $select = $this->field($field)->where($where)->join($join);
            if ($cacheEnabled)
                $select->cache(true);
            $result = $select->count();
        }else {
            $select = $this->field($field)->where($where);
            if ($cacheEnabled)
                $select->cache(true);
            $result = $select->count();
        }
        return $result;
    }

    /**
     * 单个/批量各个表的数据
     * @param array $data 
     * @param $otherData $otherData
     * author xanxus
     */
    public function addData($data, $otherData = array()) {
        if (!$data = $this->create($data)) {
            return array("code" => 0, "msg" => $this->getError(), "data" => "");
        } else {
            $returnDatas = array();
            M()->startTrans();
            if (!$info = $this->add($data)) {
                M()->rollback();
                return array("code" => 0, "msg" => "添加失败", "data" => "");
            }
            $returnDatas[$this->name] = $info;
            if (!empty($otherData)) {
                foreach ($otherData as $k => $v) {
                    if (!$newDatas = D($k)->create($v)) {
                        M()->rollback();
                        return array("code" => 0, "msg" => $this->getError(), "data" => "");
                        break;
                    }
                    if (!$newInfo = D($k)->add($v)) {
                        M()->rollback();
                        return array("code" => 0, "msg" => "添加失败", "data" => "");
                        break;
                    }
                    $returnDatas[D($k)->name] = $newInfo;
                }
            }
            M()->commit();
            return array("code" => 0, "msg" => $this->getError(), "data" => $returnDatas);
        }
    }

    /**
     * 批量更新数据
     * @param $key_value_arr键值数组
     * @param $key_field 主键
	 * @param $update_field 更新字段
     * author luoyu
     */  
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
	public function get($where,$field="*") {
        return $this->getFind($where,$field);
    }
}
