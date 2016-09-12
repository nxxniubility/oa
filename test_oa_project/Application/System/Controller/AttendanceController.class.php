<?php
/*
|--------------------------------------------------------------------------
| attendance控制器
|--------------------------------------------------------------------------
| createtime：2016-04-20
| updatetime：
| updatename：
*/
namespace System\Controller;
use Common\Controller\SystemController;
class AttendanceController extends SystemController {

    protected $attendanceDb;
    public function _initialize(){
        parent::_initialize();
        $this->attendanceDb = D("Common/attendance");
    }

    /*
    每月考勤统计
    @author Nixx
    */
    public function month()
    {
        $request = I("post.");
        $date = $request['date'];
        unset($request['date']);
        foreach ($request as $key => $req) {
            if ($req) {
                $searchCriteria[$key] = $req;
            }
        }
        $systemUserAttendanceList = $this->attendanceDb->getSystemUserAttendanceList($searchCriteria,$date);
        if (!$systemUserAttendanceList) {
            $this->ajaxReturn(1,'无效请求');
        }
        $this->ajaxReturn(0,$systemUserAttendanceList);
    }

    /*
    实时考勤统计
    @author Nixx
    */
    public function day()
    {
        # code...
    }

    /*
    实时考勤修改申请
    @author Nixx
    */
    public function edit()
    {
        # code...
    }



}