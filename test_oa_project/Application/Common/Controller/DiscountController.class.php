<?php
namespace Common\Controller;

use Common\Controller\BaseController;

class DiscountController extends BaseController
{


    //缴费优惠项目表
    public function getList(){
        if(F('Cache/discount/list')){
            $discount = F('Cache/discount/list');
        }else{
            $discount = D('Discount')->where('type=1')->select();
            F('Cache/discount/list', $discount);
        }
        if(!empty($discount)){
            //数组分级
            $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
            $newAll = $Arrayhelps->createTree($discount, 0, 'discount_id', 'pid');
        }else{
            $newAll = $discount;
        }
        return array('code'=>0, 'data'=>$newAll);
    }

    //缴费优惠项目表详情
    public function getInfo($discount_id){
        if(F('Cache/discount/list')){
            $discount_cahe = F('Cache/discount/list');
            foreach($discount_cahe as $k=>$v){
                if($v['discount_id']==$discount_id){
                    $discount = $v;
                }
            }
        }else{
            $discount = D('Discount')->where(array('type'=>1,'discount_id'=>$discount_id))->find();
        }

        return array('code'=>0, 'data'=>$discount);
    }
}