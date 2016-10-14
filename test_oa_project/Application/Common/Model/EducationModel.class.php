<?php
/*
|--------------------------------------------------------------------------
| 学历表
|--------------------------------------------------------------------------
| createtime：2016-04-28
| updatetime：2016-04-28
| updatename：zgt
*/
namespace Common\Model;
use Common\Model\SystemModel;

class EducationModel extends SystemModel
{
    protected $educationDb;

    public function _initialize(){

    }

    /*
     * 学历表
     * @author zgt
     * @return array
     */
    public function getAllEducation(){
        if( F('Cache/Personnel/education') ){
            $educationAll = F('Cache/Personnel/education');
        }else{
            $educationAll = $this
                ->where('status=1')
                ->select();
            F('Cache/Personnel/education', $educationAll);
        }
        return $educationAll;
    }



    /*
     * 学历
     * @author nxx
     * @return array
     */
    public function getEducationInfo($education_id){
         if( F('Cache/Personnel/education') ){
            $educationList = F('Cache/Personnel/education');
            foreach ($educationList as $key => $education) {
                if ($education_id == $education['education_id']) {
                    return $education['educationname'];
                }
            }
        }else{
            $education = $this
                ->where("education_id = $education_id and status=1")
                ->find();
            $educationList = $this
            ->where('status=1')
            ->select();
            F('Cache/Personnel/education', $educationList);
        }
        return $education['educationname'];
    }





}