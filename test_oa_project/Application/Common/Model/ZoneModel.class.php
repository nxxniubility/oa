<?php
namespace Common\Model;
use Common\Model\BaseModel;

class ZoneModel extends BaseModel
{
    protected $_id='zone_id';
    public function _initialize(){
        parent::_initialize();
    }

    //自动验证
    protected $_validate = array(
        array('channelname', 'checkSpecialCharacter', array('code'=>'201','msg'=>'名称不能含有特殊字符！'), 0, 'callback'),
        array('channelname', '0,15', array('code'=>'202','msg'=>'名称不能大于15字符！'), 0, 'length'),
    );

    /*
    |--------------------------------------------------------------------------
    | 获取单条记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getFind($where=null, $field='*', $join=null)
    {
        return $this->field($field)->where($where)->join($join)->find();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $field='*', $order=null, $limit=null, $join=null)
    {
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }


    /*
    |--------------------------------------------------------------------------
    | 获取总数
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where=null, $join=null)
    {
        return $this->where($where)->join($join)->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 添加
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addData($data)
    {
        // 如果创建失败 表示验证没有通过 输出错误提示信息
        if (!$this->create($data)){
            return $this->getError();
        }else{
            $re_id = $this->add($data);
            return array('code'=>0,'data'=>$re_id);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 修改
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editData($data,$id)
    {
        // 如果创建失败 表示验证没有通过 输出错误提示信息
        if (!$this->create($data)){
            return $this->getError();
        }else{
            $re_flag = $this->where(array($this->_id=>$id))->save($data);
            return array('code'=>0,'data'=>$re_flag);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 删除
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delData($id)
    {
        return $this->where(array($this->_id=>$id))->delete();
    }

    /*
  	role_id 获取想关联的ID
  	@author nxx
  	*/
  	public function getZoneIds($zone_id = 0)
  	{
  		if (F('Cache/Zone/zone')) {
  			$zoneList = F('Cache/Zone/zone');
  		}else{
  			$zoneList = $this->where("status=1")->select();
  			F('Cache/Zone/zone', $zoneList);
  		}
  		//数组分级
  		$Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
  		$newZoneList = $Arrayhelps->subFinds($zoneList,$zone_id,'zone_id','parentid');
  		foreach($zoneList as $k=>$v){
  			if($v['zone_id']==$zone_id){
  				$newZoneList[] = $v;
  			}
  		}
  		return $newZoneList;
  	}

}
