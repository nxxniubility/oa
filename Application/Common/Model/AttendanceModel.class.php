<?php
/*
|--------------------------------------------------------------------------
| 区域模型
|--------------------------------------------------------------------------
| createtime：2016-04-20
| updatetime：
| updatename：
*/
namespace Common\Model;
use Common\Model\SystemModel;
class AttendanceModel extends SystemModel{

 	protected $attendanceDb;
    public function _initialize(){
        $this->attendanceDb = M('attendance');
    }


	/*
     * 获取员工考勤记录列表
     * @author Nixx
     * @return array
     */
    public function getSystemUserAttendanceList($searchCriteria, $date)
    {
		$start_time = strtotime($date);
		$num = date('t', strtotime("$date"));
		$start_month_day = 1;
		for($i=0;$i<$num;$i++) {	  
		    for($j=0;$j<$num;$j++){
		        $month_day_arr[$i][$j] = $date.'-'."$start_month_day";
		        $start_month_day++;
		    }
		    if($start_month_day > $total_month_day){
		        break;
		    }
		}
		//$includedString = "";
		foreach ($month_day_arr as $key => $days) {
			foreach ($days as $key => $day) {
				$includedString .= ",$day";
			}
			
		}		
		$daySt['zl_attendance.date'] = array("IN", $includedString);
    	$systemUserDb = D("SystemUser");
    	$systemUserList = $systemUserDb->getSystemUserAll($searchCriteria,$order=null,$limit='0,10');
    	if (!$systemUserList) {
    		return null;
    	}
    	foreach ($systemUserList as $key => $systemUser) {
    		//正常上班天数统计
			$systemUserAttendanceList[$key]['normalDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and attendancestatus = 0 and $daySt and status = 1")->count();
			//查询迟到天数统计
			$systemUserAttendanceList[$key]['laterDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and attendancestatus = 1 and $daySt and status = 1")->count();
			//早退天数统计
			$systemUserAttendanceList[$key]['earlyDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and attendancestatus = 2 and $daySt and status = 1")->count();
			//旷工天数统计
			$systemUserAttendanceList[$key]['absenteeismDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and attendancestatus = 4 and $daySt and status = 1")->count();
			//加班数统计,单位:小时
			$userAttendanceList = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and $daySt and status = 1")->field('overtime')->select();
			foreach ($userAttendanceList as $userAttendance) {
				$systemUserAttendanceList[$key]['overtimeHours'] += $userAttendance['overtime'];
			}
			$systemUserAttendanceList[$key]['overtimeDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and overtime = 1 and $daySt and status = 1")->count();
			//调休天数统计
			$systemUserAttendanceList[$key]['daysOffDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and leave = 4 and $daySt and status = 1")->count();
			//出差天数统计
			$systemUserAttendanceList[$key]['onBusinessDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and leave = 5 and $daySt and status = 1")->count();
			//病假天数统计
			$systemUserAttendanceList[$key]['illnessDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and leave = 2 and $daySt and status = 1")->count();
			//事假天数统计
			$systemUserAttendanceList[$key]['thingDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and leave = 1 and $daySt and status = 1")->count();
			//年假天数统计
			$systemUserAttendanceList[$key]['yearDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and leave = 3 and $daySt and status = 1")->count();
			//婚假天数统计
			$systemUserAttendanceList[$key]['marryDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and leave = 6 and $daySt and status = 1")->count();
			//产假天数统计
			$systemUserAttendanceList[$key]['birthDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and leave = 7 and $daySt and status = 1")->count();
			//丧假天数统计
			$systemUserAttendanceList[$key]['gameoverDays'] = $this->attendanceDb->where("system_user_id = $systemUser[system_user_id] and leave = 8 and $daySt and status = 1")->count();
    	}
    	return $systemUserAttendanceList;

    }





}