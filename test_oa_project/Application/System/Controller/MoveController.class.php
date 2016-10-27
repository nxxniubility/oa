<?php
namespace System\Controller;
use Common\Controller\BaseController;
use Common\Service\RedisUserService;

class MoveController extends BaseController
{

//    /**
//     * 数据库pages表数据转移
//     * 复制线上的pagestype表
//     * 复制线上pages表，然后添加terminal_id 和status( = 1)字段，
//     */
   public function servicecode()
   {
                ////////////////////////////
        //问题1：user_info表的专业是邮箱账号，学校是学历
        // $edu = D("Education")->field("education_id,educationname")->select();
        // $where['major'] = array("like",'%@%');
        // $userInfoList = D("UserInfo")->where($where)->field("user_id,major,school")->select();
        // foreach ($userInfoList as $key => $value) {
        //     // $userList[$key] = D("User")->where("user_id = $value[user_id]")->field("email,qq")->find();
        //     // $userList[$key]['emailInfo'] = $value['major'];
        //     foreach ($edu as $k1 => $v1) {
        //         if ($value['school'] == $v1['educationname']) {
        //             $save['education_id'] = $v1['education_id'];
        //             $save['major'] = 0;
        //             $updataInfo = D("UserInfo")->where("user_id = $value[user_id]")->save($save);
        //             $m = $m+1;
        //         }
        //     }
        // }
        // dump($m);
        ////////////////////////////


       /////////////////////////////
       ///问题2：解决school是学历的问题
        // $edu = D("Education")->field("education_id,educationname")->select();
        // $eduList = D("Education")->field("education_id,educationname")->select();
        // foreach ($edu as $key => $value) {
        //     unset($edu[$key]);
        //     $edu[$key] = $value['educationname'];
        // }
        // $where['school'] = array("IN",$edu);
        // $oldList = M("user_info")->where($where)->field("school,education_id,user_id")->order("user_id desc")->select();
        // foreach ($oldList as $k => $v) {
        //     if ($v['education_id']==0) {
        //         $error[] = $v;
        //         foreach ($eduList as $k1 => $v1) {
        //             if ($v['school'] == $v1['educationname']) {
        //                 $save1['education_id'] = $v1['education_id'];
        //                 $save1['school'] = 0;
        //                 $updata = D("UserInfo")->where("user_id = $v[user_id]")->save($save1);
        //                 $m = $m+1;
        //             }
        //         }
        //     }
        // }

        // $m = count($oldList);
        // foreach ($oldList as $k2 => $v2) {
        //     unset($oldList[$k2]);
        //     $oldList[$k2] = $v2['user_id'];
        // }
        // $where['user_id'] = array("IN", $oldList);
        // $save2['school'] = 0;
        // $upda = D("UserInfo")->where($where)->save($save2);
// dump($upda);
// dump($oldList);
// exit;

       ///////////////////////////////////
       ///问题3：学历值不存在于数据库
       // $save['education_id'] = 0;
       // $updata = D("UserInfo")->where("education_id = 10")->save($save);



       ///////////////////////////////////
       ///问题4：专业是学历的情况
       // $save['major'] = 0;
       // $edu
       // $where['major'] = array("IN", $edu);
       // $updata = D("UserInfo")->where($where)->save($save);

       ///////////////////////////////////
       ///问题5：薪酬存在：“格式化”等字样
       //











       }
// exit;
       // //需查看线上pagestype_id确认
       // $pcpages = M('servicecode_copy')->where("pagestype_id = 1 or pagestype_id = 2")->select();
       // foreach ($pcpages as $key => $pc) {
       //     $pc['terminal_id'] = 1;
       //     $pc['status'] = 1;
       //     M('pages')->data($pc)->add();
       // }
       // dump($old);
       // $mpages = M('servicecode_copy')->where("pagestype_id = 3 or pagestype_id = 4")->select();
       // foreach ($mpages as $key => $m) {
       //     $m['terminal_id'] = 2;
       //     $m['status'] = 1;
       //     M('pages')->data($m)->add();
       // }

       // $new =   M("pages")->where("pages_id > 0")->count();
       // dump($new);
       // $filename = "C:\Users\Administrator\Desktop\promote\www.txt";
       // $file = fopen($filename, "w"); //以写模式打开文件
       // fwrite($file, "Hello, world!\n"); //写入第一行
       // fwrite($file, "This is a test!\n"); //写入第二行
       // fclose($file); //关闭文件
//   }
///////////////////////////////////pages结束
//
//
//    /**
//     * 复制课程表，同时将线上的项目的图片文件复制到本地项目相关目录
//     */
//
//
//
//    /**
//     * 复制domain表 ,取数据用
//     * 复制totalcode表,取数据用
//     * 复制servicecode表
//     */
//
//
//
//    // /**
//    //  * 数据库promote表数据查出并实现转移
//    //  */
//    // public function promote()
//    // {
//    //     set_time_limit(0);
//    //     $countNum = M('promote_copy')->where()->count();
//
//    //     $countPages = ceil($countNum/100);
//    //     $num = 1;
//
//    //     $cidString = '';
//    //     $channelList1 = M('channel')->where("pid = 0")->select();
//    //     foreach ($channelList1 as $k1 => $channel1) {
//    //         if (!empty($cidString)) {
//    //             $cidString = $cidString. ",$channel1[channel_id]";
//    //         }else{
//    //             $cidString = $channel1['channel_id'];
//    //         }
//    //     }
//    //     $channelIdList = explode(',', $cidString);
//    //     //for ($i=0; $i < $countPages; $i++) {
//    //         $start = ($num-1)*100;
//    //         $oldPromoteList = M('promote_copy')->where()->order('promote_id asc')->limit(0,10)->select();
//    //         $arr = array();
//    //         foreach ($oldPromoteList as $key => $oldPromote) {
//    //             if (in_array($oldPromote['title'], $arr)) {
//    //                 //如果已存在推广账号名称，则取出相应的proid_id,执行添加promote_id等相关的操作
//    //                 $proid = M('proid')->where('accountname = "{$oldPromote[title]}"')->find();
//    //                 dump($oldPromote['title']);
//    //                 $promote['proid_id'] = $proid['proid_id'];
//    //                 //如果有计划单元，添加pro_lev后添加promote
//    //                 if ($oldPromote['plantitle'] && $oldPromote['elementtitle']) {
//    //                     $prolev['name'] = $oldPromote['plantitle'];
//    //                     $prolev['status'] = 1;
//    //                     $prolev['proid_id'] = $proid['proid_id'];
//    //                     $prolev['pid'] = 0;
//    //                     $result = M('pro_lev')->where($prolev)->find();
//    //                     if ($result) {
//    //                         $prolev['pid'] = $result['pro_lev_id'];
//    //                     }else{
//    //                         $pro_lev_id = M('pro_lev')->data($prolev)->add();
//    //                         $prolev['pid'] = $pro_lev_id;
//    //                     }
//    //                     $result = M('pro_lev')->where($prolev)->find();
//    //                     if ($result) {
//    //                         $promote['pro_lev_id'] = $result['pro_lev_id'];
//    //                     }else{
//    //                         $promote['pro_lev_id'] = M('pro_lev')->data($prolev)->add();
//    //                     }
//
//    //                     $promote['plan'] = $oldPromote['plantitle'];
//    //                     $promote['planunit'] = $oldPromote['elementtitle'];
//    //                     $promote['keyword'] = $oldPromote['keyword'];
//    //                     $promote['pc_pages_id'] = $oldPromote['pcpages_id'];
//    //                     $promote['m_pages_id'] = $oldPromote['mpages_id'];
//    //                     $promote['pcservice_id'] = $oldPromote['servicecode_id'];
//    //                     $promote['mservice_id'] = $oldPromote['mservicecode_id'];
//    //                     $promote['createtime'] = $oldPromote['createtime'];
//    //                     $promote['status'] = $oldPromote['display'];
//
//    //                     $result = M('promote')->data($promote)->add();
//    //                 }else{
//    //                     //无计划无单元，只添加promote
//    //                     $promote['pro_lev_id'] = 0;
//    //                     $promote['plan'] = $oldPromote['plantitle'];
//    //                     $promote['planunit'] = $oldPromote['elementtitle'];
//    //                     $promote['keyword'] = $oldPromote['keyword'];
//    //                     $promote['pc_pages_id'] = $oldPromote['pcpages_id'];
//    //                     $promote['m_pages_id'] = $oldPromote['mpages_id'];
//    //                     $promote['pcservice_id'] = $oldPromote['servicecode_id'];
//    //                     $promote['mservice_id'] = $oldPromote['mservicecode_id'];
//    //                     $promote['createtime'] = $oldPromote['createtime'];
//    //                     $promote['status'] = $oldPromote['display'];
//
//    //                     $result = M('promote')->data($promote)->add();
//    //                 }
//
//    //             }else{
//    //                 //如果是新的推广账号名称，则添加新的proid信息，并且执行添加promote_id等相关的操作
//    //                 $proid['accountname'] = $oldPromote['title'];
//    //                 //渠道
//    //                 $channel_id = $oldPromote['channel_id'];
//    //                 if (in_array($oldPromote['channel_id'], $channelIdList)) {  //判断其本身是否为1级
//    //                     $proid['channel_id'] = $oldPromote['channel_id'];
//    //                 }else{
//    //                     for ($i=0; $i < 10; $i++) {
//    //                         $channelInfo = M('channel_copy')->where("channel_id = $channel_id")->find();
//    //                         if (in_array($channelInfo['pid'], $channelIdList)) {
//    //                             $proid['channel_id'] = $channelInfo['channel_id'];
//    //                         }else{
//    //                             $channel_id = $channelInfo['pid'];
//    //                         }
//    //                     }
//    //                 }
//
//    //                 $proid['system_user_id'] = $oldPromote['user_id'];
//
//    //                 $domainInfo = M('domain_copy')->where("domain_id = $oldPromote[domain_id]")->find();
//    //                 $proid['domain'] = $domainInfo['url'];
//    //                 // $totalcodeInfo = M('totalcode_copy')->where("totalcode_id = $oldPromote[totalcode_id]")->find();
//    //                 // $proid['totalcode'] = $totalcodeInfo['totalcode'];     //warn:字段待定
//    //                 $proid['pcservice_id'] = $oldPromote['servicecode_id'];
//    //                 $proid['mservice_id'] = $oldPromote['mservicecode_id'];
//    //                 $proid['pc_pages_id'] = $oldPromote['pcpages_id'];
//    //                 $proid['m_pages_id'] = $oldPromote['mpages_id'];
//    //                 $proid['createtime'] = $oldPromote['createtime'];
//    //                 $proid['status'] = $oldPromote['display'];
//    //                 //插入proid数据
//    //                 $promote['proid_id'] = M('proid')->data($proid)->add();
//
//    //                 if ($oldPromote['plantitle'] && $oldPromote['elementtitle']) {
//    //                     $prolev['name'] = $oldPromote['plantitle'];
//    //                     $prolev['status'] = 1;
//    //                     $prolev['proid_id'] = $promote['proid_id'];
//    //                     $prolev['pid'] = 0;
//    //                     $pro_lev_id = M('pro_lev')->data($prolev)->add();
//    //                     $prolev['pid'] = $pro_lev_id;
//    //                     $promote['pro_lev_id'] = M('pro_lev')->data($prolev)->add();
//    //                     $promote['plan'] = $oldPromote['plantitle'];
//    //                     $promote['planunit'] = $oldPromote['elementtitle'];
//    //                     $promote['keyword'] = $oldPromote['keyword'];
//    //                     $promote['pc_pages_id'] = $oldPromote['pcpages_id'];
//    //                     $promote['m_pages_id'] = $oldPromote['mpages_id'];
//    //                     $promote['pcservice_id'] = $oldPromote['servicecode_id'];
//    //                     $promote['mservice_id'] = $oldPromote['mservicecode_id'];
//    //                     $promote['createtime'] = $oldPromote['createtime'];
//    //                     $promote['status'] = $oldPromote['display'];
//
//    //                     $result = M('promote')->data($promote)->add();
//    //                 }else{
//    //                     //无计划无单元，只添加promote
//    //                     $promote['pro_lev_id'] = 0;
//    //                     $promote['plan'] = $oldPromote['plantitle'];
//    //                     $promote['planunit'] = $oldPromote['elementtitle'];
//    //                     $promote['keyword'] = $oldPromote['keyword'];
//    //                     $promote['pc_pages_id'] = $oldPromote['pcpages_id'];
//    //                     $promote['m_pages_id'] = $oldPromote['mpages_id'];
//    //                     $promote['pcservice_id'] = $oldPromote['servicecode_id'];
//    //                     $promote['mservice_id'] = $oldPromote['mservicecode_id'];
//    //                     $promote['createtime'] = $oldPromote['createtime'];
//    //                     $promote['status'] = $oldPromote['display'];
//
//    //                     $result = M('promote')->data($promote)->add();
//    //                 }
//
//    //             }
//
//    //             $arr[] = $oldPromote['title'];
//    //         }
//    //         dump($arr);
//    //         $num = $num+ 1;
//    //     //}
//    // }
//
//
//    /**
//     * 导入计划
//     * @author Nixx
//     */
//    public function inputPlan()
//    {
//        set_time_limit(0);
//        $proid_id = 390;
//        $proidInfo = D('Promote')->getProInfo($proid_id);
//// $count = M('promote')->where("proid_id = 389")->delete();
//// dump($count);
//// exit;
//        if(IS_POST)
//        {
//            session('faile_input', null);
//            session('success_input', null);
//            $pc_pages_id = I("post.pcPagesType_id");
//            $m_pages_id  = I("post.mPagesType_id");
//            $setpages_id = I("post.setpages_id");
//            if (!empty($_FILES['file'])) {
//                $exts = array('xls','xlsx');
//                $rootPath = './Public/';
//                $savePath = 'promote/';
//                $uploadFile = $this->uploadFile($exts,$rootPath,$savePath);
//                $filename = $rootPath.$uploadFile['file']['savepath'].$uploadFile['file']['savename'];
//            }
//            $datas = importExecl($filename);
//            unlink($filename);
//            $letters = D('Promote')->getSetPagesInfo($setpages_id);
//            foreach ($letters as $k1 => $letter) {
//                $k1 = $k1+1;
//                $pro[$k1][] = $letter['pagehead'];
//                $pro[$k1][] = $letter['headname'];
//            }
//            /*对生成的数组进行字段对接*/
//            foreach ($pro as $key => $p) {
//                foreach ($datas as $k => $v){
//                    if ($k>1) {
//                        for ($i=0; $i < count($v); $i++) {
//                            $keys = array_keys($v);
//                            foreach ($keys as $k2 => $v1) {
//                                if ($p[0] == $v1) {
//                                    $promoteList[$k-2]["$p[1]"] = $v[$v1];
//                                }
//                            }
//                        }
//                        $promoteList[$k-2]['proid_id'] = $proid_id;
//                        $promoteList[$k-2]['pc_pages_id'] = $pc_pages_id;
//                        $promoteList[$k-2]['m_pages_id'] = $m_pages_id;
//                    }
//                }
//            }
//        //dump(count($promoteList));
//            foreach ($promoteList as $key => $promote) {
//                if (!empty($pc_pages_id)) {
//                    unset($promote['pc_pages']);
//                }
//                if (!empty($m_pages_id)) {
//                    unset($promote['m_pages']);
//                }
//                if (!$promote['keyword']) {
//                    $errorData[$key] = $promoteList[$key];
//                    $errorData[$key]['msg'] = '缺少关键字、或者其他原因';
//                    unset($promoteList[$key]);
//                }
//                elseif(!$promote['plan'] && $promote['planunit']) {
//                    $errorData[$key] = $promoteList[$key];
//                    unset($promoteList[$key]); //删除错误的数据类型
//                }
//            }
//
//            foreach ($promoteList as $key => $promote) {
//                $promote['status'] = 1;
//                $prolev['proid_id'] = $promote['proid_id'];
//                $prolev['status'] = 1;
//                //只有计划没有单元
//                if ($promote['plan'] && !$promote['planunit']) {
//                    unset($promote['planunit']);
//                    $prolev['name'] = $promote['plan'];
//                    $proLevInfo = D('Promote')->getProLevInfo($prolev);
//                    if (!$proLevInfo) {
//                        $pro_lev_id = D('Promote')->createProLev($prolev);
//                        $promote['pro_lev_id'] = $pro_lev_id;
//                    }else{
//                        $promote['pro_lev_id'] = $proLevInfo['pro_lev_id'];
//                    }
//                }//有计划有单元
//                 elseif ($promote['plan'] && $promote['planunit']) {
//                    $prolev['name'] = $promote['plan'];
//                    $proLevInfo = D('Promote')->getProLevInfo($prolev);
//                    if (!$proLevInfo) {
//                        $plan_lev_id = D('Promote')->createProLev($prolev);
//                        $prolev['name'] = $promote['planunit'];
//                        $prolev['pid'] = $plan_lev_id;
//                        $pro_lev_id = D('Promote')->createProLev($prolev);
//                        $prolev['pid'] = 0; //重置pid为0
//                        $promote['pro_lev_id'] = $pro_lev_id;
//                    }else{
//                        $prolev['name'] = $promote['planunit'];
//                        $prolev['pid'] = $proLevInfo['pro_lev_id'];
//                        $punitLevInfo = D('Promote')->getProLevInfo($prolev);
//                        if ($punitLevInfo) {
//                            $promote['pro_lev_id'] = $punitLevInfo['pro_lev_id'];
//                        }else{
//                            $pro_lev_id = D('Promote')->createProLev($prolev);
//                            $promote['pro_lev_id'] = $pro_lev_id;
//                        }
//                        $prolev['pid'] = 0; //重置pid为0
//                    }
//                }
//                if ($promote['pc_pages']) {
//                    $html = parse_url($promote['pc_pages']);
//                    $str = preg_replace('~\/dev\/([0-9]{1,})\.html~', '', $html['path']);
//                    $match=array();
//                    $m = preg_match('~promote\/([0-9]{1,})~', $str,$match);
//                    if($m){
//                        $promote_id=$match[1];
//                        $oldPromote =  M("promote_copy")->where("promote_id = $promote_id")->find();
//                        $promote['promote_id'] = $promote_id;
//                        $promote['pc_pages_id'] = $oldPromote['pcpages_id'];
//                        $promote['m_pages_id'] = $oldPromote['mpages_id'];
//                        $promote['pcservice_id'] = $oldPromote['servicecode_id'];
//                        $promote['mservice_id'] = $oldPromote['mservicecode_id'];
//                        $promote['createtime'] = $oldPromote['createtime'];
//                        unset($promote['pc_pages']);
//                        unset($promote['m_pages']);
//
//                        $proInfo = M("promote")->where("promote_id = $promote_id")->find();
//                        if ($proInfo) {
//                            $errorDataA[] = $promote;
//                        }else{
//                            M("promote")->data($promote)->add();
//                        }
//                    }else{
//                        $errorDataB[] = $promote;
//                    }
//                }else{
//                    $errorDataC[] = $promote;
//                }
//            }
//            session('faile_input', $errorDataA);
//            session('faile_input', $errorDataB);
//            session('success_input', $promoteList);
//            $accountname = $proidInfo['accountname'];
//$filename = 'C:\Users\Administrator\Desktop\wei\baidusou04.txt';
//foreach ($errorDataA as $k1 => $value) {
//        foreach($value as $k2 => $value2){
//            $data .= $k2.':'.$value2.'\r\n';
//        }
//}
//file_put_contents($filename, $data);
//
//// $filename = 'C:\Users\Administrator\Desktop\yu\less.txt';
//// foreach ($errorDataB as $k1 => $v1) {
////         foreach($v1 as $k2 => $value2){
////             $data .= $k2.':'.$value2.'\r\n';
////         }
//// }
//// file_put_contents($filename, $data);
//dump($errorDataA);
//dump($errorDataB);
//dump($errorDataC);
//        }else{
//
//            $set['system_user_id'] = 35;
//            $set['type'] = 1;
//            $setPages = D('Promote')->getSetPages($set);
//            $pcPagesTypeList = D('Pages')->getAllPages($order='createtime desc',$page=null,$where=null);
//            $pc['terminal_id'] = 1;
//            $m['terminal_id'] = 2;
//            $pro['pcPagesTypeList'] = D('Pages')->getPagesType($pc);
//            $pro['mPagesTypeList'] = D('Pages')->getPagesType($m);
//            $this->assign('pro', $pro);
//            $this->assign('setPages', $setPages);
//            $this->assign('proidInfo', $proidInfo);
//dump($proid_id);
//            $this->display();
//        }
//
//    }
//
//
////     /**
////      * 转移员工数据
////      */
//
////     public function systemUser()
////     {
////         set_time_limit(0);
////         // $userList = M('user_copy')->where("usertype = 20 or usertype = 30")->select();
////         $userList = M('user','zl_','DB_CONFIG1')->where("usertype = 20 or usertype = 30")->select();
//// //         foreach ($userList as $key => $user) {
//// //             $infos = M("userinfo_copy")->where("user_id = $user[user_id]")->find();
//// //         }
//// //         检查旧的userinfo是否有员工的信息
//// // dump($infos);
//// // exit;
////         foreach ($userList as $key => $user) {
////             $sysUser['system_user_id'] = $user['user_id'];
////             $sysUser['zone_id'] = $user['center_id'];
////             $sysUser['password'] = $user['password'];
////             $sysUser['username'] = encryptPhone($user['username'], C('PHONE_CODE_KEY'));
////             $sysUser['realname'] = $user['realname'];
////             $sysUser['sex'] = $user['sex'];
////             $sysUser['face'] = $user['face'];
////             $sysUser['email'] = $user['email'];
////             $sysUser['emailpassword'] = $user['emailpassword'];
////             $sysUser['usertype'] = $user['usertype'];
////             if ($user['usertype'] == 30) {
////                 $sysUser['usertype'] = 10;
////                 $sysUser['status'] = 0;
////             }elseif ($user['usertype'] == 20) {
////                 $sysUser['usertype'] = 50;
////                 $sysUser['status'] = 1;
////             }
////             $sysUser['createtime'] = $user['jointime'];
////             $sysUser['createip'] = $user['joinip'];
////             $sysUser['isuserinfo'] = 0;
////             $sysUserInfo = M("system_user")->where($sysUser)->find();
////             if ($sysUserInfo) {
////                 $errorData[] = $user;
////             }else{
////                 M("system_user")->data($sysUser)->add();
////                 $sysInfo['system_user_id'] = $sysUser['system_user_id'];
////                 M("system_user_info")->data($sysInfo)->add();
////                 M("system_user_engaged")->data($sysInfo)->add();
////             }
////         }
//// dump($errorData);
////     }
//
//
//    /*
//     * ==========================================================
//     * 客户数据迁移
//     * ==========================================================
//     */
//    public function session_plan()
//    {
//        print_r(session('migrationUser'));
//    }
//
//    /**
//     * 转移客户数据
//     */
//    public function showIndex()
//    {
//        $page = I('get.page',1);
//        if($page==1){
//            $where["zl_user.usertype"] = 0;
//            $count['count'] = M('user','zl_','DB_CONFIG1')
//                ->join('LEFT JOIN __USERINFO__ ON __USERINFO__.user_id=__USER__.user_id')
//                ->where($where)
//                ->count();
//            $_cahe['countPages'] = ceil($count['count']/50);
//            $_cahe['count'] = $count['count'];
//            session('migrationUser',$_cahe);
//        }else{
//            $_cahe = session('migrationUser');
//            $count['count'] = $_cahe['count'];
//        }
//        if($page<=ceil($count['count']/50)){
//
//            $this->getUserInfo($page,'50');
//            $page++;
//            $this->redirect('System/move/showIndex',array('page'=>$page));
//        }
//
//    }
//
//    /**
//     * 处理数据
//     */
//    protected function migrationUser($pages,$shownum='100')
//    {
//        set_time_limit(0);
//
//        if(!session('migrationUser')){
//            $result = $this->getUserInfo(1,1);
//            $_cahe['countPages'] = ceil($result['count']/$shownum);
//            $_cahe['count'] = $result['count'];
//        }
//        $_cahe = session('migrationUser');
//        $_cahe['page'] = $pages;
//        $_cahe['plan_num'] = $pages*$shownum;
//        session('migrationUser',$_cahe);
////        for($i=1; $i<10; $i++ ){
//            $result2 = $this->getUserInfo($pages,$shownum);
//        print_r($result2);exit();
//            foreach($result2['data'] as $k=>$v){
//                $user_data['user_id'] = $v['user_id'];
//                $user_data['zone_id'] = $v['center_id'];
//                $user_data['infoquality'] = $v['infolevel_id'];
//                $user_data['username'] = encryptPhone($v['username'], C('PHONE_CODE_KEY'));
//                $user_data['qq'] = $v['qq'];
//                $user_data['email'] = $v['email'];
//                $user_data['password'] = $v['password'];
//                $user_data['nickname'] = $v['nickname'];
//                $user_data['face'] = $v['face'];
//                $user_data['nickname'] = $v['nickname'];
//                $user_data['createtime'] = $v['jointime'];
//                $user_data['createip'] = $v['joinip'];
//                $user_data['weight'] = $v['weight'];
//                $user_data['course_id'] = $v['course_id'];
//                $user_data['searchkey'] = $v['keyword'];
//                $user_data['interviewurl'] = $v['weight'];
//                $user_data['system_user_id'] = $v['create_id'];
//                $user_data['promote_id'] = $v['promote_id']; //n
//                $user_data['lastvisit'] = $v['interviewtime'];
//                if(!empty($v['visitingtime']) && $v['visitingtime']==10){
//                    $v['status'] = 160;
//                }
//                $user_data['status'] = $v['status']; //***
//                if(!empty($v['visitingtime']) && $v['visitingtime']!=0){
//                    $user_data['nextvisit'] = $v['visitingtime'];
//                }else{
//                    $user_data['nextvisit'] = $v['nextinterviewtime'];
//                }
//                $user_data['updatetime'] = $v['allottime'];
//                $user_data['visittime'] = $v['min_visittime'];
//                $user_data['class_id'] = $v['class_id'];   //n
//                $user_data['channel_id'] = $v['channel_id'];
//                $user_data['attitude_id'] = $v['result'];  //***
//                $user_data['introducermobile'] = !empty($v['recommend_phone'])?$v['recommend_phone']:0;
//
//                $userinfo_data['user_info_id'] = $v['userinfo_id'];
//                $userinfo_data['user_id'] = $v['user_id'];
//                $userinfo_data['sex'] = $v['sex'];
//                if(!empty($v['birthday'])){
//                    $userinfo_data['birthday'] = !empty($v['birthday'])?$v['birthday']:0;
//                }
//                $userinfo_data['major'] = $v['major'];
//                $userinfo_data['education_id'] = $v['education_id'];
//                $userinfo_data['school'] = $v['school'];
//                $userinfo_data['province_id'] = $v['province_id'];
//                $userinfo_data['city_id'] = $v['city_id'];
//                $userinfo_data['area_id'] = $v['area_id'];
//                $userinfo_data['remark'] = $v['remark'];
//
//                M('user_dr')->data($user_data)->add();
//                M('user_info_dr')->data($userinfo_data)->add();
//            }
////        }
//            return $_cahe;
//    }
//
//    /**
//     * 获取旧数据
//     */
//    protected function getUserInfo($pages,$shownum='100'){
//        set_time_limit(0);
//        $limit=(($pages-1)*$shownum).','.$shownum;
//        $where["zl_user.usertype"] = 0;
//
//        $result = M('user','zl_','DB_CONFIG1')
//            ->field(array(
//                'zl_user.user_id',
//                'zl_user.center_id',
//                'zl_user.infolevel_id',
//                'zl_user.username',
//                'zl_user.qq',
//                'zl_user.email',
//                'zl_user.password',
//                'zl_user.nickname',
//                'zl_user.realname',
//                'zl_user.face',
//                'zl_user.sex',
//                'zl_user.birthday',
//                'zl_user.status',
//                'zl_user.jointime',
//                'zl_user.joinip',
//                'zl_user.weight',
//
//                'zl_userinfo.course_id',
//                'zl_userinfo.keyword',
//                'zl_userinfo.interviewurl',
////                'zl_userinfo.remark',
//                'zl_userinfo.create_id',
//                'zl_userinfo.promote_id',
//                'zl_userinfo.interviewtime',
//                'zl_userinfo.nextinterviewtime',
//                'zl_userinfo.allottime',
//                'zl_userinfo.visitingtime',
//                'zl_userinfo.visittime',
//                'zl_userinfo.result',
//                'zl_userinfo.channel_id',
//                'zl_userinfo.class_id',
//
//                'zl_visit.min_visittime',
//                'zl_channel_marker.recommend_phone',
//
//                //info--
//                'zl_userinfo.userinfo_id',
//                'zl_userinfo.education_id',
//                'zl_userinfo.major',
//                'zl_userinfo.school',
//                'zl_userinfo.province_id',
//                'zl_userinfo.city_id',
//                'zl_userinfo.area_id',
//            ))
//            ->join("LEFT JOIN (SELECT user_id,MIN(visittime)as min_visittime FROM __VISIT__ GROUP BY user_id) __VISIT__ ON __VISIT__.user_id=__USER__.user_id")
//            ->join('LEFT JOIN __USERINFO__ ON __USERINFO__.user_id=__USER__.user_id')
//            ->join('LEFT JOIN __CHANNEL_MARKER__ ON __CHANNEL_MARKER__.user_id=__USER__.user_id')
//            ->where($where)
//            ->limit($limit)
//            ->order('zl_user.user_id desc')
//            ->select();
//
//
//        foreach($result as $k=>$v){
//            $user_data['user_id'] = $v['user_id'];
//            $user_data['zone_id'] = $v['center_id'];
//            $user_data['infoquality'] = $v['infolevel_id'];
//            $user_data['username'] = encryptPhone($v['username'], C('PHONE_CODE_KEY'));
//            $user_data['qq'] = $v['qq'];
//            $user_data['email'] = $v['email'];
//            $user_data['password'] = $v['password'];
//            $user_data['nickname'] = $v['nickname'];
//            $user_data['face'] = $v['face'];
//            $user_data['nickname'] = $v['nickname'];
//            $user_data['createtime'] = $v['jointime'];
//            $user_data['createip'] = $v['joinip'];
//            $user_data['weight'] = $v['weight'];
//            $user_data['course_id'] = $v['course_id'];
//            $user_data['searchkey'] = $v['keyword'];
//            $user_data['system_user_id'] = $v['create_id'];
//            $user_data['promote_id'] = $v['promote_id']; //n
//            $user_data['lastvisit'] = $v['interviewtime'];
//            if(!empty($v['status']) && $v['status']==10){
//                $v['status'] = 160;
//            }
//            $user_data['status'] = $v['status']; //***
//            if(!empty($v['visitingtime']) && $v['visitingtime']!=0){
//                $user_data['nextvisit'] = $v['visitingtime'];
//            }else{
//                $user_data['nextvisit'] = $v['nextinterviewtime'];
//            }
//            $user_data['updatetime'] = $v['allottime'];
//            $user_data['visittime'] = $v['min_visittime'];
//            $user_data['class_id'] = $v['class_id'];   //n
//            $user_data['channel_id'] = $v['channel_id'];
//            $user_data['attitude_id'] = $v['result'];  //***
//            $user_data['introducermobile'] = !empty($v['recommend_phone'])?$v['recommend_phone']:0;
//
//            $userinfo_data['user_info_id'] = $v['userinfo_id'];
//            $userinfo_data['user_id'] = $v['user_id'];
//            $userinfo_data['sex'] = $v['sex'];
//            $userinfo_data['birthday'] = !empty($v['birthday'])?$v['birthday']:0;
//            $userinfo_data['major'] = $v['major'];
//            $userinfo_data['education_id'] = $v['education_id'];
//            $userinfo_data['school'] = $v['school'];
//            $userinfo_data['province_id'] = $v['province_id'];
//            $userinfo_data['city_id'] = $v['city_id'];
//            $userinfo_data['area_id'] = $v['area_id'];
//            $userinfo_data['remark'] = $v['remark'];
//
//            M('user_dr')->data($user_data)->add();
//            M('user_info_dr')->data($userinfo_data)->add();
//        }
//        return true;
//    }
//
//
//
//
//    /**
//     * 转移缴费数据
//     */
//    public function migrationFee()
//    {
//        $page = I('get.page',1);
//        if($page==1){
//            session('re_add_fee',null);
//            $count['count'] = M('fee_copy')
//                ->order('fee_id desc')
//                ->count();
//            $_cahe['countPages'] = ceil($count['count']/500);
//            $_cahe['count'] = $count['count'];
//            session('migrationFee',$_cahe);
//        }else{
//            $_cahe = session('migrationFee');
//            $_cahe['page'] = $page;
//            $_cahe['num'] = ($page*500);
//            $count['count'] = $_cahe['count'];
//            session('migrationFee',$_cahe);
//        }
//        if($page<=ceil($count['count']/500)){
//            $limit=(($page-1)*500).',500';
//            $result = M('fee_copy')
//                ->limit($limit)
//                ->order('fee_id desc')
//                ->select();
//
//            foreach($result as $k=>$v){
////                //审核状态
////                'audit_array' => array(
////                    //预报审核
////                    '0' => '等待审核',
////                    '1' => '审核未通过',
////                    '2' => '审核通过',
////                    '3' =>  '已退款',
////                    //缴费审核
////                    '4' =>  '等待缴费审核',
////                    '5' =>  '审核未通过',
////                    '6' =>  '已缴完款',
////                    '7' =>  '缴费退款',
////                    //教务审核
////                    '8' => '等待审核',
////                    '9' => '审核未通过',
////                ),
//                $fee_data['fee_id']=$v['fee_id'];
//                $fee_data['user_id']=$v['user_id'];
//                $fee_data['course_id']=$v['course_id'];
////                $fee_data['fee_id']=$v['faudit_id'];//缴费状态
////                $fee_data['fee_id']=$v['paymenttype'];//缴费方式  '1' => '一次性','2' => '助学贷款', '3' => '后付费', '4' => '学期内分期',
//                $fee_data['studytype']=$v['learning_id'];//学习方式
//                if($v['course_cost']==0 && !empty($v['course_id']) && $v['course_id']!=0){
//                    $course = M('course')->where(array('course_id'=>$v['course_id']))->find();
//                    $fee_data['coursecount']=$course['price'];
//                }else{
//                    $fee_data['coursecount']=$v['course_cost'];//课程总价
//                }
//                $fee_data['discount_cost']=$v['discount_cost'];//优惠金额
//                $fee_data['arrearage']=$v['course_worth'];//学费净值
//                $fee_data['loan_institutions_count']=$v['loan_cost'];//贷款缴费总金额
//                $fee_data['loan_institutions_id']=$v['loan_institutions_id'];//贷款缴费总金额
//                $fee_data['paycount']=$v['cost'];     //已缴费用
//                $fee_data['discount_id']=$v['discount_id'];//优惠原因
//                $fee_data['paymemttime']=$v['paymemttime'];//缴费时间
//
//                $info_data['user_id']=$v['user_id'];//省份ID
//                $info_data['province_id']=$v['province_id'];//省份ID
//                $info_data['city_id']=$v['city_id'];//城市ID
//                $info_data['area_id']=$v['area_id'];//区ID
//                $info_data['address']=$v['address'];//详细地址
//                $info_data['identification']=$v['idcard'];//身份证号
//                $info_data['contactname']=$v['contactname'];//紧急联系人
//                $info_data['contactnumber']=$v['contactnumber'];//紧急联系人电话
//
//                $fee_dr = M('fee_dr')->where(array('fee_id'=>$v['fee_id']))->find();
//                if(empty($fee_dr)){
//                    $fee_add_flag = M('fee_dr')->data($fee_data)->add();
//                    if(empty($fee_add_flag)){
//                        $re_ids = session('re_add_fee');
//                        $re_ids[]=$v['fee_id'];
//                        session('re_add_fee',$re_ids);
////                    M('user_copy')->rollback();
////                    break ;
//                    }
//                }
//                $user_info_dr = M('user_info_dr')->where(array('user_id'=>$v['user_id']))->find();
//                if(empty($user_info_dr)){
//                    $inf_flag = M('user_info_dr')->data($info_data)->add();
//                }else{
//                    $inf_flag = M('user_info_dr')->data(array('user_id'=>$v['user_id']))->save($info_data);
//                }
//
//            }
////            $page++;
////            $this->redirect('System/move/migrationFee',array('page'=>$page));
//        }
//    }
//
//
//
//    /**
//     * 转移缴费数据
//     */
//    public function migrationFeeLog()
//    {
//        $page = I('get.page',1);
//        if($page==1){
//            session('re_add_feelog',null);
//            $count['count'] = M('feelist_copy')
//                ->count();
//            $_cahe['countPages'] = ceil($count['count']/500);
//            $_cahe['count'] = $count['count'];
//            session('migrationFeeLog',$_cahe);
//        }else{
//            $_cahe = session('migrationFeeLog');
//            $_cahe['page'] = $page;
//            $_cahe['num'] = ($page*500);
//            $count['count'] = $_cahe['count'];
//            session('migrationFeeLog',$_cahe);
//        }
//        if($page<=ceil($count['count']/500)){
//            $limit=(($page-1)*500).',500';
//            $result = M('feelist_copy')
//                ->limit($limit)
//                ->order('feelist_id desc')
//                ->select();
//
//            foreach($result as $k=>$v){
//
//                $fee_data['fee_logs_id']=$v['feelist_id'];
//                $fee_data['user_id']=$v['user_id'];
////                $fee_data['fee_id']=$v['fee_id'];
//                $fee_data['system_user_id']=!empty($v['financial_id'])?$v['financial_id']:0;//财务审核人ID
//                if($v['arithmetic_id']==2){
//                    $fee_data['paytype']=3;
//                }else{
//                    $fee_data['paytype']=$v['feetype']; //收款类型ID 1预报 2 缴费
//                }
//                $fee_data['feemsg']=$v['feemsg'];
//
//                $fee_dr = M('fee_logs_dr')->where(array('fee_logs_id'=>$v['feelist_id']))->find();
//                if(empty($fee_dr)){
//                    $fee_add_flag = M('fee_logs_dr')->data($fee_data)->add();
//                    if(empty($fee_add_flag)){
//                        $re_ids = session('re_add_feelog');
//                        $re_ids[]=$v['feelist_id'];
//                        session('re_add_feelog',$re_ids);
////                    M('user_copy')->rollback();
////                    break ;
//                    }
//                }
//
//            }
//            $page++;
//            $this->redirect('System/move/migrationFeeLog',array('page'=>$page));
//        }
//    }
//
//
//
//    /**
//     * 回访数据
//     */
//    public function migrationCallback()
//    {
//        $page = I('get.page',1);
//        if($page==1){
//            session('re_add_callback',null);
//            $count['count'] = M('callback_copy')
//                ->count();
//            $_cahe['countPages'] = ceil($count['count']/500);
//            $_cahe['count'] = $count['count'];
//            session('migrationCallback',$_cahe);
//        }else{
//            $_cahe = session('migrationCallback');
//            $_cahe['page'] = $page;
//            $_cahe['num'] = ($page*500);
//            $count['count'] = $_cahe['count'];
//            session('migrationCallback',$_cahe);
//        }
//        session('re_add_callback',null);
//        $count['count'] = M('callback_copy')
//            ->count();
//        $_cahe['countPages'] = ceil($count['count']/500);
//        $_cahe['count'] = $count['count'];
//        session('migrationCallback',$_cahe);
//        if($page<=ceil($count['count']/500)){
//            $limit=(($page-1)*500).',500';
//            $result = M('callback_copy')
//                ->limit($limit)
//                ->order('callback_id desc')
//                ->select();
//
//            foreach($result as $k=>$v){
//
//                $add_data['callback_id']=$v['callback_id'];
//                $add_data['user_id']=$v['user_id'];
//                $add_data['system_user_id']=$v['writer_id'];//记录添加者ID
////                $add_data['user_id']=$v['clienttype'];//客户类型
//                $add_data['waytype']=$v['backtype'];//回访方式
//                $add_data['attitude_id']=$v['result'];//跟进结果
//                $add_data['callbacktime']=$v['interviewtime'];//回访时间
//
//                if(!empty($v['visitingtime']) && $v['visitingtime']!=0){
//                    $add_data['nexttime']=$v['visitingtime'];//预计上门时间
//                }else{
//                    $add_data['nexttime']=$v['nextinterviewtime'];//下次回访时间
//                }
//
//                $add_data['remark']=$v['remark'];//备注
//                if($v['display']==1){
//                    $add_data['status']=0;//跟进结果
//                }
//
////
//                $add_dr = M('user_callback')->where(array('callback_id'=>$v['callback_id']))->find();
//                if(!empty($add_dr)){
//                    $add_flag = M('user_callback')->where(array('callback_id'=>$v['callback_id']))->save($add_data);
////                    if(empty($add_flag)){
////                        $re_ids = session('re_add_callback');
////                        $re_ids[]=$v['feelist_id'];
////                        session('re_add_callback',$re_ids);
////                    M('user_copy')->rollback();
////                    break ;
////                    }
//                }
//
//            }
//            $page++;
//            $this->redirect('System/move/migrationCallback',array('page'=>$page));
//        }
//    }
//
//    /**
//     * 回访数据
//     */
//    public function userMarker()
//    {
//
//    }
//
//    /**
//     * 回访数据
//     */
//    public function systemUserZone()
//    {
//        $page = I('get.page',1);
//        if($page==1){
//
//            $count['count'] = M('system_user')
//                ->count();
//            $_cahe['countPages'] = ceil($count['count']/500);
//            $_cahe['count'] = $count['count'];
//            session('system_user',$_cahe);
//        }else{
//            $_cahe = session('system_user');
//            $_cahe['page'] = $page;
//            $_cahe['num'] = ($page*500);
//            $count['count'] = $_cahe['count'];
//            session('system_user',$_cahe);
//        }
//
//        if($page<=ceil($count['count']/500)){
//            $limit=(($page-1)*500).',500';
//            $result = M('system_user')
//                ->limit($limit)
//                ->order('system_user_id desc')
//                ->select();
//
//            foreach($result as $k=>$v){
//                $edit['zone_id'] = $v['zone_id'];
//
//                M('user')->where(array('system_user_id'=>$v['system_user_id']))->save($edit);
//            }
//
//            $page++;
//            $this->redirect('System/move/systemUserZone',array('page'=>$page));
//        }
//    }
//
//    /**
//     * 回访数据
//     */
//    public function userZone()
//    {
//        $page = I('get.page',1);
//        $count = I('get.count');
//        $where['allocationtime'] = array(array('EGT', strtotime('2016-06-13 12:00')), array('LT', time()), 'AND');
//        if($page==1){
//            $count = M('user','zl_','DB_CONFIG1')
//            ->where($where)
//            ->count();
//        }
//        if($page<=ceil($count/500)){
//            $limit=(($page-1)*500).',500';
//            $result = M('user','zl_','DB_CONFIG1')
//                ->field('system_user_id,user_id,zone_id,allocationtime')
//                ->limit($limit)
//                ->where($where)
//                ->order('allocationtime desc')
//                ->select();
//            $i=0;
//            foreach($result as $k=>$v){
//                $where2['system_user_id'] = $v['system_user_id'];
//                $system_zone = M('system_user','zl_','DB_CONFIG1')->field('system_user_id,zone_id')->where(array('system_user_id'=>$v['system_user_id']))->find();
//
//                if($v['zone_id']!=$system_zone['zone_id']){
//                    $where_new['user_id'] = $v['user_id'];
//                    $data_new['zone_id'] = $system_zone['zone_id'];
//                    // $refalg = M('user','zl_','DB_CONFIG1')->where($where_new)->save($data_new);
//
//                    $i++;
//                    echo '<pre>';
//                    echo "----------------{$i}------------------</br>";
//                    echo $v['user_id'].' --old:'.$v['zone_id'].' --new:'.$system_zone['zone_id'].'</br>';
//                    // echo 'flag:'.var_dump($refalg).'</br>';
//                    echo "-----------------------------------</br>";
//                    echo '</pre>';
//                }
//            }
//            // $page++;
//            // $this->redirect('System/move/userZone',array('page'=>$page,'count'=>$count));
//        }
//    }
//
//    /**
//     * 回访数据
//     */
//    public function userMauserinfo()
//    {
//
//        $page = I('get.page',1);
//        if($page==1){
//            $count['count'] = M('userinfo_copy')
//                ->where(array('remark'=>array('NEQ','NULL')))
//                ->count();
//            $_cahe['countPages'] = ceil($count['count']/500);
//            $_cahe['count'] = $count['count'];
//            session('userMauserinfo',$_cahe);
//        }else{
//            $_cahe = session('userMauserinfo');
//            $_cahe['page'] = $page;
//            $_cahe['num'] = ($page*500);
//            $count['count'] = $_cahe['count'];
//            session('userMauserinfo',$_cahe);
//        }
//
//        if($page<=ceil($count['count']/500)){
//            $limit=(($page-1)*500).',500';
//            $result = M('userinfo_copy')
//                ->limit($limit)
//                ->field(array('remark,user_id'))
//                ->where(array('remark'=>array('NEQ','NULL')))
//                ->order('user_id desc')
//                ->select();
//            foreach($result as $k=>$v){
//
//
//                if(!empty($v['invitation_id']) && $v['invitation_id']!=0){
//                    $add_data['updateuser_id'] = $v['invitation_id'];
//                }elseif(!empty($v['service_id']) && $v['service_id']!=0){
//                    $add_data['updateuser_id'] = $v['service_id'];
//                }else{
//                    $add_data['updateuser_id'] = $v['create_id'];
//                }
//                $add_data['system_user_id']=$v['consulting_id'];
//
//                $user_dr = M('user_dr')->where(array('user_id'=>$v['user_id']))->find();
//                if(!empty($user_dr)){
//                    M('user_dr')->where(array('user_id'=>$v['user_id']))->save($add_data);
//                }
//                $add_data['remark'] = $v['remark'];
//                $user_dr = M('user_info')->where(array('user_id'=>$v['user_id']))->find();
//                if(!empty($user_dr)){
//                    if(empty($user_dr['remark'])){
//                        M('user_info')->where(array('user_id'=>$v['user_id']))->save($add_data);
//                    }
//                }
//            }
//            $page++;
//            $this->redirect('System/move/userMauserinfo',array('page'=>$page));
//        }
//    }
//
//    /**
//     * 回访数据
//     */
//    public function userMauserinfo2()
//    {
//        $page = I('get.page',1);
//        if($page==1){
//            $count['count'] = M('user')
//               ->where(array('updatetime'=>array('lt','')))
//                ->count();
//            $_cahe['countPages'] = ceil($count['count']/500);
//            $_cahe['count'] = $count['count'];
//            session('userMauserinfo2',$_cahe);
//        }else{
//            $_cahe = session('userMauserinfo2');
//            $_cahe['page'] = $page;
//            $_cahe['num'] = ($page*500);
//            $count['count'] = $_cahe['count'];
//            session('userMauserinfo2',$_cahe);
//        }
//        if($page<=ceil($count['count']/500)){
//            $limit=(($page-1)*50).',50';
//            $result = M('user')
//                ->where(array('updatetime'=>array('lt','')))
//                ->field(array('user_id'))
//                ->limit($limit)
//                ->order('user_id desc')
//                ->select();
//
//
//            foreach($result as $k=>$v){
//                $userinfo = M('userinfo_copy')
//                    ->field(array('user_id','invitation_id','service_id','consulting_id','create_id'))
//                    ->where(array('user_id'=>$v['user_id']))
//                    ->find();
//
//                if(!empty($userinfo)){
//                    if(!empty($userinfo['invitation_id']) && $userinfo['invitation_id']!=0){
//                        $add_data['updateuser_id'] = $userinfo['invitation_id'];
//                    }elseif(!empty($userinfo['service_id']) && $userinfo['service_id']!=0){
//                        $add_data['updateuser_id'] = $userinfo['service_id'];
//                    }else{
//                        $add_data['updateuser_id'] = $userinfo['create_id'];
//                    }
//
//
//                    if(empty($userinfo['consulting_id']) || $userinfo['consulting_id']==0){
//                        $add_data['system_user_id']=$add_data['updateuser_id'];
//                    }else{
//                        $add_data['system_user_id']=$userinfo['consulting_id'];
//                    }
//                    $userflag = M('user')->where(array('user_id'=>$v['user_id']))->find();
//                    if($userflag){
//
//                        M('user')->where(array('user_id'=>$v['user_id']))->save($add_data);
//                    }
//                }
//
//            }
//            $page++;
//            $this->redirect('System/move/userMauserinfo2',array('page'=>$page));
//        }
//    }
//
//
//    /**
//     * 回访数据
//     */
//    public function userEdit()
//    {
//        $page = I('get.page',1);
//        if($page==1){
//            $count['count'] = M('user')
//                ->where(array('system_user_id|updateuser_id'=>array('eq',0)))
//                ->count();
//            $_cahe['countPages'] = ceil($count['count']/500);
//            $_cahe['count'] = $count['count'];
//            session('userEdit',$_cahe);
//        }else{
//            $_cahe = session('userEdit');
//            $_cahe['page'] = $page;
//            $_cahe['num'] = ($page*500);
//            $count['count'] = $_cahe['count'];
//            session('userEdit',$_cahe);
//        }
//
//        if($page<=ceil($count['count']/500)){
//            $limit=(($page-1)*500).',500';
//            $result = M('user')
//                ->where(array('system_user_id|updateuser_id'=>array('eq',0)))
//                ->limit($limit)
//                ->order('user_id desc')
//                ->select();
//
//            foreach($result as $k=>$v){
//                $userinfo_copy = M('userinfo_copy')->where(array('user_id'=>$v['user_id']))->find();
//                if($v['updateuser_id'==0]){
//                    if(!empty($userinfo_copy['invitation_id'])){
//                        $add_data['updateuser_id'] = $userinfo_copy['invitation_id'];
//                    }else if(!empty($userinfo_copy['service_id'])){
//                        $add_data['updateuser_id'] = $userinfo_copy['service_id'];
//                    }else{
//                        $add_data['updateuser_id'] = $userinfo_copy['create_id'];
//                    }
//                }
//                if($v['system_user_id'==0]){
//                    if(!empty($v['consulting_id'])){
//                        $add_data['system_user_id']=$v['consulting_id'];
//                    }else{
//                        $add_data['system_user_id']=$add_data['updateuser_id'];
//                    }
//                }
//
//
////                if(!empty($user_dr)){
//                    M('user')->where(array('user_id'=>$v['user_id']))->save($add_data);
////                }
//
//
//            }
//            $page++;
//            $this->redirect('System/move/userEdit',array('page'=>$page));
//        }
//    }
//
//    public function updateChannel(){
//
//        $count = I('count');
//        if(empty($count)){
//            $count = M('User')->count();
//        }
//        $p = I('p');
//        $p = empty($p) ? 1 : $p;
//        $Page       = new \Think\Page($count,1000);
//        $user = M('UserCopy1')->limit($Page->firstRow.','.$Page->listRows)->select();
//        $channels = M('Channel')->select();
//
//        foreach($user as $k => $v){
//            if(!empty($v['channel_id'])){
//                $arr = $this->parentFind($channels, $v['channel_id']);
//                echo '<pre>';
//                echo $v['user_id'].'<br/>';
//                print_r($arr);
//                echo '</pre>';
//                echo '---------------------------------------------------<br/>';
//                if(!empty($arr[1]['channel_id'])){
//                    $data['channel_id'] = $arr[1]['channel_id'];
//                }else{
//                    $data['channel_id'] = $arr[0]['channel_id'];
//                }
//            }else{
//                $data['channel_id'] = 2244;
//            }
//
//
//            M('User')->where(array('user_id'=>$v['user_id']))->save($data);
//        }
//       $this->redirect('/System/Move/updateChannel',array('p' =>$p+1,'count' => $count));
//    }
//
//    /**
//     * 指定子级ID找父级
//     * @author Sunles
//     * @return array
//     */
//    public function parentFind($array,$id,$idname="channel_id",$pidname="pid"){
//        $arr = array();
//        foreach($array as $k => $v){
//            if($id == $v[$idname]){
//                if($v[$pidname] != 0){
//                    $arr = $this->parentFind($array, $v[$pidname],$idname,$pidname);
//                }
//                $arr[] = $v;
//            }
//        }
//        return $arr;
//    }
//
//    public function updateCallBack(){
//        $count = I('count');
//        if(empty($count)){
//            $count =  M('CallbackCopy')->count();
//        }
//        $p = I('p');
//        $p = empty($p) ? 1 : $p;
//        $Page       = new \Think\Page($count,1000);
//        $CallbackCopy = M('CallbackCopy')->limit($Page->firstRow.','.$Page->listRows)->select();
//
//        foreach($CallbackCopy as $k => $v){
//            if($v['display'] == 1){
//                $data['status'] = 0;
//            }else{
//                $data['status'] = 1;
//            }
//            echo $v['user_id'].'<br/>';
//            M('UserCallback')->where(array('callback_id'=>$v['callback_id']))->save($data);
//            echo M('UserCallback')->getLastSql().'<br/>';
//        }
//        $this->redirect('/System/Move/updateCallBack',array('p' =>$p+1,'count' => $count));
//    }

   public function addRedis()
   {
        $count = I('count');
        if(empty($count)){
            $count =  M('User')->count();
        }
        $p = I('p');
        $p = empty($p) ? 1 : $p;

       if($p<=ceil($count/1000)) {
           $user = M('User')->limit((($p - 1) * 1000) . ',1000')->order('user_id desc')->select();

           $RedisUserService = new RedisUserService();
           $RedisUserService->addUser($user);
           $this->redirect('/System/Move/addRedis',array('p' =>$p+1,'count' => $count));
       }
   }


    public function rollUser(){
        exit;
//        $where['system_user_id'] = 14;
//        $where['status'] = 160;
//        $where['channel_id'] = array('IN','1,4,5,1420,1421,4856,4995,5133,1000003');
//        $list = M('user','zl_','DB_CONFIG1')->where($where)->select();
        $list = F('20161014-rolluser');
        foreach($list as $v){
            //是否有申请转入中？
            $apply = M('user_apply','zl_','DB_CONFIG1')->where(array('status'=>10,'user_id'=>$v['user_id']))->find();
            if(empty($apply)){
                $save_user['status'] = 20;
                $save_user['mark'] = 1;
                $save_user['nextvisit'] = time();
                $save_user['attitude_id'] = 0;
                $save_user['callbacknum'] = 0;
                $save_user['lastvisit'] = time();
                $save_user['allocationtime'] = time();
                $save_user['system_user_id'] = 3;
                $save_user['updateuser_id'] = 3;
                $save_user['updatetime'] = time();
                M('user','zl_','DB_CONFIG1')->where(array('user_id'=>$v['user_id']))->save($save_user);
                //添加回访记录
                $add_ab = array(
                    'user_id'=>$v['user_id'],
                    'system_user_id'=>$v['system_user_id'],
                    'callbacktime'=>time(),
                    'nexttime'=>time(),
                    'remark'=>'系统分配',
                    'callbacktype'=>30
                );
                M('user_callback','zl_','DB_CONFIG1')->add($add_ab);
                $arr[] = $v['user_id'];
            }
        }
         F('20161014-rolluser',$arr);
        print_r(count($arr));
    }

    public function Holiday(){
        set_time_limit(3000);
        for($i=70;$i<365;$i++){
            $ymd = date('Ymd', strtotime('+'.$i.' day'));
            $redata = D('Api','Service')->getApiHoliday($ymd);
            if($redata['code']==0){
                $add['day_time'] = strtotime($ymd);
                $add['day_type'] = $redata['data'];
                M('holiday')->add($add);
            }
        }
    }


    public function byUser(){
        exit;
        $list = F('20161026-byuser_channel');
        foreach($list as $v){
            $ids[] = $v['user_id'];
        }
        $save['system_user_id'] = 160203;
        $save['status'] = 20;
        $list2 = M('user','zl_','DB_CONFIG1')->field('system_user_id,status')->where(array('user_id'=>array('IN',$ids)))->save($save);

        print_r($list2);
        exit;
        $byuser_list2 = F('20161026-byuser_list2');
        foreach($byuser_list2 as $k=>$v){
            $info = M('user','zl_','DB_CONFIG1')->field('createtime,status,user_id')->where(array('user_id'=>$v['user_id']))->find();
            if($info['status']==160){
                $save['allocationtime'] = $info['createtime'];
                $save['updatetime'] = $info['createtime'];
                $save['nextvisit'] = $info['createtime'];
                $save['lastvisit'] = $info['createtime'];
                $save['status'] = 70;
                if($v['invitation_id']!=0){
                    $save['updateuser_id'] = $v['invitation_id'];
                }elseif($v['service_id']!=0){
                    $save['updateuser_id'] = $v['service_id'];
                }else{
                    $save['updateuser_id'] = $v['consulting_id'];
                }
                $save['system_user_id'] = $v['consulting_id'];
                $edit_flag = M('user','zl_','DB_CONFIG1')->where(array('user_id'=>$v['user_id']))->save($save);
                if(!empty($edit_flag)){
                    $flag_ids[] = $v['user_id'];
                }
            }
        }
        F('20161026-byuser160',$flag_ids);
    }

    public function delUser(){
        exit;
////        $where['system_user_id'] = 2;
//        $where['createuser_id'] = 53236;
//        $where['channel_id'] = 27;
////        $where['email'] = array('IN', '男,女');
////        $where['status'] = 160;
//        $where['createtime'] = array('EGT',strtotime(date('2016-10-26').' 00:00:00'));
//        $list = M('user','zl_','DB_CONFIG1')->where($where)->select();
//        F('20161026-delUser',$list);
//        print_r($list);
//        exit;
        $list = F('20161026-delUser');
        foreach($list as $k=>$v){
            $arr[] = M('user','zl_','DB_CONFIG1')->where(array('user_id'=>$v['user_id']))->delete();
            M('user_info','zl_','DB_CONFIG1')->where(array('user_id'=>$v['user_id']))->delete();
        }
        print_r(count($arr));
    }

    public function migrationFee()
    {
        $count = I('count');
        if(empty($count)){
            $count =  M('fee','zl_','DB_CONFIG1')->count();
        }
        $p = I('p');
        $p = empty($p) ? 1 : $p;
        if($p==1){
            //添加LOG
            $log['data'] = array();
            $log['count'] = 0;
            $log['data2'] = array();
            $log['count2'] = 0;
            F('getUserOnInfo',$log);
        }
        if($p<=ceil($count/1000)){
            $log = F('getUserOnInfo');
            $user = M('fee','zl_','DB_CONFIG1')->limit((($p-1)*1000).',1000')->order('fee_id desc')->select();
            $dataArr = array();
            foreach ($user as $k => $v) {
                $add_data['user_id'] = $v['user_id'];
                //获取归属人ID
                $getSystem_user = M('user','zl_','DB_CONFIG1')->field('system_user_id')->where(array('user_id'=>$v['user_id']))->find();
                $add_data['system_user_id'] = $getSystem_user['system_user_id'];
                //获取财务ID 与预报金额
                $getAuditoruser = M('fee_logs','zl_','DB_CONFIG1')->field('system_user_id,pay,receivetype,auditor_status')->where(array('user_id'=>$v['user_id'],'paytype'=>1))->order('fee_logs_id desc')->find();
//                $add_data['auditoruser_id'] = $getAuditoruser['system_user_id'];
                $add_data['auditoruser_id'] = 0;
                //状态转换
                if($v['pay_status']==0){
                    $add_data['status'] = $getAuditoruser['auditor_status'];
                }elseif($v['pay_status']==1){
                    $add_data['status'] = 40;
                }elseif($v['pay_status']==2){
                    $add_data['status'] = 50;
                }
                $add_data['course_id'] = $v['course_id'];
                $add_data['studytype'] = $v['studytype'];
                $add_data['discount_id'] = $v['discount_id'];
                $add_data['coursecost'] = $v['coursecount'];
                $add_data['paycount'] = $v['paycount'];
                $add_data['discountcost'] = $v['discount_cost'];
                $add_data['subscription'] = $getAuditoruser['pay'];
                //将3网银 4支付宝 5微信  统一转3：转账
                if($getAuditoruser['receivetype']==4 || $getAuditoruser['receivetype']==5){
                    $getAuditoruser['receivetype']=3;
                }
                $add_data['payway'] = $getAuditoruser['receivetype'];
//                $add_data['cost'] = ((int)$v['paycount'])-((int)$v['arrearage']);
                if($v['loan_institutions_id']==1){
                    $v['loan_institutions_id']=10;
                }elseif($v['loan_institutions_id']==2){
                    $v['loan_institutions_id']=20;
                }elseif($v['loan_institutions_id']==10){
                    $v['loan_institutions_id']=30;
                }
                $add_data['loan_institutions_id'] = $v['loan_institutions_id'];
                $add_data['loan_institutions_cost'] = $v['loan_institutions_count'];
                $add_data['sparecost'] = $v['arrearage'];
                $add_data['createtime'] = $v['paymemttime'];
                $dataArr[] = $add_data;
            }
            $createOrderId = M("order")->addAll($dataArr);

            F('getUserOnInfo',$log);
            $this->redirect('/System/Move/migrationFee',array('p' =>$p+1,'count' => $count));
        }
    }

	public function moveLogs()
    {
    	$where['auditor_status'] = array('IN',"10,30"); //取审核中 以及 审核成功的数据
    	$oldLogs =  M('fee_logs')->where($where)->select();
// dump($oldLogs);
// exit;
		foreach ($oldLogs as $key => $oldLog) {
			$order = M('order')->where("user_id = $oldLog[user_id]")->field('order_id')->find();

			//如果存在则更新order,然后创建order_log
			if ($order) {
				if ($oldLog['paytype'] == 3) { //退费？
					$saveOrder['cost'] = ((int)$order['cost'])-((int)$oldLog['pay']);
					if ($saveOrder['cost'] == 0) {
						$saveOrder['status'] = 70;//退款
					}else{
						$saveOrder['status'] = 60;//部分退款
					}
					if ($saveOrder['sparecost']) { //欠费，有则+，无则过
						$saveOrder['sparecost'] = ((int)$order['sparecost'])+((int)$oldLog['pay']);
					}
					// if (((int)$order['subscription']) == 0) {
					// 	$saveOrder['subscription'] = $oldLog['subscription'];
					// }
				}else{  //收费
					$saveOrder['cost'] = ((int)$order['cost'])+((int)$oldLog['pay']);//实际缴费
					if ($saveOrder['sparecost']) { //欠费，有则-
						$saveOrder['sparecost'] = ((int)$order['sparecost'])-((int)$oldLog['pay']);
						if ($saveOrder['sparecost'] == 0) {
							$saveOrder['status'] =50;
						}
					}
					if (((int)$order['subscription']) == 0) {//预报为空则补上
						$saveOrder['subscription'] = $oldLog['subscription'];
					}
				}
				$updateOrder = M('order')->where("order_id = $order[order_id]")->save($saveOrder);

				//创建order_log记录
				$addOrderLog['user_id'] = $oldLog['user_id'];
				if ($oldLog['paytype'] == 3) {
					$addOrderLog['paytype'] = 2;
				}else{
					$addOrderLog['paytype'] = 1;
				}
				if ($oldLog['receivetype'] = 3 || $oldLog['receivetype'] = 4 || $oldLog['receivetype']= 5) {
					$addOrderLog['payway'] = 3;
				}
				$addOrderLog['cost'] = $oldLog['pay'];
				$addOrderLog['order_id'] = $order['order_id'];
				$addOrderLog['createtime'] = $oldLog['receivetime'];
				$order_log_id = M('order_log')->data($addOrderLog)->add();
			}else{
				//不存在则创建order
				$createOrder['user_id'] = $oldLog['user_id'];
				$createOrder['system_user_id'] = $oldLog['system_user_id'];
				$createOrder['status'] = 10;
				$createOrder['subscription'] = $oldLog['pay'];
				$createOrder['cost'] = $oldLog['pay'];
				$order_id = M('order')->data($createOrder)->add();

				$addOrderLog['user_id'] = $oldLog['user_id'];
				if ($oldLog['paytype'] == 3) {
					$addOrderLog['paytype'] = 2;
				}else{
					$addOrderLog['paytype'] = 1;
				}
				if ($oldLog['receivetype'] = 3 || $oldLog['receivetype'] = 4 || $oldLog['receivetype']= 5) {
					$addOrderLog['payway'] = 3;
				}
				$addOrderLog['cost'] = $oldLog['pay'];
				$addOrderLog['order_id'] = $order_id;
				$addOrderLog['createtime'] = $oldLog['receivetime'];
				$order_log_id = M('order_log')->data($addOrderLog)->add();
			}
		}
    }

   public function updateApply2()
   {
      $list = M('user_apply','zl_','DB_CONFIG1')->where(array('applytime'=>array('GT',1471190400),'affiliation_system_user_id'=>4,'affiliation_channel_id'=>18,'status'=>30))->select();

      foreach($list as $k=>$v){
         $callback = M('user_callback','zl_','DB_CONFIG1')->field('system_user_id')->where(array('user_id'=>$v['user_id'],'callbacktime'=>array('LT',$v['auditortime'])))->order('callbacktime desc')->find();
         if(!empty($callback)){
            $realname = M('system_user','zl_','DB_CONFIG1')->field('realname')->where(array('system_user_id'=>$callback['system_user_id']))->find();
            $data['affiliation_system_user_id'] = $callback['system_user_id'];
            M('user_apply','zl_','DB_CONFIG1')->field('affiliation_system_user_id')->where(array('user_apply_id'=>$v['user_apply_id']))->save($data);
            echo $realname['realname'].'-'.$v['user_id'].'-'.$v['user_apply_id'].'<br/>';
         }
      }
   }


//    public function getUserOnInfo(){
//        $count = I('count');
//        if(empty($count)){
//            $count =  M('user_info','zl_','DB_CONFIG1')->count();
//        }
//        $p = I('p');
//        $p = empty($p) ? 1 : $p;
//        if($p==1){
//            $log['data'] = array();
//            $log['count'] = 0;
//            $log['data2'] = array();
//            $log['count2'] = 0;
//            F('getUserOnInfo',$log);
//        }
//        if($p<=ceil($count/1000)){
//            $log = F('getUserOnInfo');
//            $user = M('user_info','zl_','DB_CONFIG1')->field('user_id')->limit((($p-1)*1000).',1000')->order('user_id desc')->select();
//            foreach ($user as $k => $v){
//                $isUser = M('user','zl_','DB_CONFIG1')->field('user_id')->where(array('user_id'=>$v['user_id']))->find();
//                if(empty($isUser)){
////                    M('user_info','zl_','DB_CONFIG1')->add(array('user_id'=>$v['user_id']));
//                    $log['data'][] = $v['user_id'];
//                    $log['count'] = $log['count']+1;
//                }
//            }
//            F('getUserOnInfo',$log);
//            $this->redirect('/System/Move/getUserOnInfo',array('p' =>$p+1,'count' => $count));
//        }
//    }

//    public function getUserChannel(){
//        $user =  M('user','zl_','DB_CONFIG1')->field('user_id')->where(array('channel_id'=>array('exp','is null')))->select();
////        print_r($user);exit;
//        $log['count'] = count($user);
//        $log['data'] = $user;
//        $log['dataChannel'] = array();
//        foreach ($user as $k => $v){
//            $reInfo = M('userinfo','zl_','DB_CONFIG1')->field('channel_id,user_id')->where(array('user_id'=>$v['user_id']))->find();
//            if(!empty($reInfo) && !empty($reInfo['channel_id'])){
//                $channel_id = $this->getChannelVal($reInfo['channel_id']);
//                if(!empty($channel_id)){
////                    M('user','zl_','DB_CONFIG1')->field('channel_id')->where(array('user_id'=>$v['user_id']))->save(array('channel_id'=>$channel_id));
//                    $log['dataChannel'][] = array('user_id'=>$v['user_id'],'channel_id'=>$channel_id);
//                    unset($log['data'][$v['user_id']]);
//                }
//            }
//        }
//        F('UserChannelNull',$log);
//        print_r($log['dataChannel']);
//    }
//    protected function getChannelVal($channel){
//        $arr = array('0','1','2','3','1567','1583','2244');
//        if(!in_array($channel,$arr)){
//            $channelInfo = M('channel_copy','zl_','DB_CONFIG1')->field('channel_id,pid')->where(array('channel_id'=>$channel))->find();
//            if(!empty($channelInfo)){
//                if(!in_array($channelInfo['pid'],$arr)){
//                    return $this->getChannelVal($channelInfo['pid']);
//                }else{
//                    return $channel;
//                }
//            }else{
//                return null;
//            }
//        }else{
//            return $channel;
//        }
//    }

//2016-8-16 修复回访记录
//    public function getUserCallback(){
//        $user_callback = M('user_callback', 'zl_', 'DB_CONFIG1')->where(array('callbacktype'=>0,'status'=>1,'remark'=>array(array('neq','系统超时回收'),array('neq','系统分配'),'AND'),'callbacktime'=>array(array('GT','1471255200'),array('LT','1471311600'))))->order('callback_id desc')->select();
////        print_r($user_callback);
//        foreach($user_callback as $k=>$v){
//            $user = M('user', 'zl_', 'DB_CONFIG1')->field('status,user_id,callbacknum')->where(array('user_id'=>$v['user_id']))->find();
//            if($user['status']==20){
//                $save['status'] = 30;
//                M('user', 'zl_', 'DB_CONFIG1')->field('status')->where(array('user_id'=>$user['user_id']))->save($save);
//                echo $user['user_id'].'<br/>';
//            }
//        }
//    }
    public function getUserCallback2(){
//        $arr = array('246554','246828','246826',246824,246557,246555,246551,246407,246556,246553,246552,246550,246455);
//
//        foreach($arr as $v) {
//            $info = M('user', 'zl_', 'DB_CONFIG1')->field('system_user_id,user_id')->where(array('user_id' => $v))->find();
//            if (!empty($info)) {
//                M('user', 'zl_', 'DB_CONFIG1')->field('updateuser_id')->where(array('user_id' => $v))->save(array('updateuser_id' => $info['system_user_id']));
//            }
//        }
//        $user_callback = M('user_callback', 'zl_', 'DB_CONFIG1')->where(array('status'=>0,'callbacktime'=>array(array('GT','1471255200'),array('LT',time()))))->order('callback_id desc')->select();
//        foreach($user_callback as $k=>$v){
//            $user = M('user', 'zl_', 'DB_CONFIG1')->field('status,user_id,callbacknum')->where(array('user_id'=>$v['user_id'],'status'=>30))->find();
//            if(!empty($user)){
//                $count = M('user_callback', 'zl_', 'DB_CONFIG1')->where(array('status'=>1,'user_id'=>$user['user_id']))->count();
//                if($count==0){
//                    echo $count.'-'.$user['user_id'].'<br/>';
//                }
////                M('user', 'zl_', 'DB_CONFIG1')->field('status')->where(array('user_id'=>$user['user_id']))->save($save);
////                echo $user['user_id'].'<br/>';
//            }
//        }
//        $user_callback = M('user', 'zl_', 'DB_CONFIG1')->field('callbacknum,user_id,status,updatetime')->where(array('callbacknum'=>0,'status'=>30,'updatetime'=>array('EGT','1471264556')))->order('updatetime desc')->select();
//
//        foreach($user_callback as $k=>$v){
//
//            $save['status'] = 20;
//            M('user', 'zl_', 'DB_CONFIG1')->field('status')->where(array('user_id'=>$v['user_id']))->save($save);
//            echo $v['user_id'].'<br/>';
//        }
//        print_r($user_callback);
    }
//

    public function getUserZone(){
//        $count = I('count');
//        if(empty($count)){
//            $count =  M('user','zl_','DB_CONFIG1')->where(array('phonevest'=>array('eq','')))->count();
//        }
//        $p = I('p');
//        $p = empty($p) ? 1 : $p;
//        if($p==1){
            $ids = $this->getRoleIds(28);
            $where["system_user_id"] = array('IN', $ids);
            $log['cont'] = 0;
            $log['data'] = '';
            $log['userid'] = array();
            F('getUserZone',$log);
//        }


//        $checkform = new \Org\Form\Checkform();
//        if($p<=ceil($count/500)) {
            $log = F('getUserZone');

            $system_user = M('system_user', 'zl_', 'DB_CONFIG1')->field('zone_id,system_user_id,realname')->where($where)->select();
//        print_r($system_user);exit;
            foreach($system_user as $k=>$v){
                $user = M('user', 'zl_', 'DB_CONFIG1')->field('zone_id,user_id,system_user_id,updatetime,username')->where(array('system_user_id' => $v['system_user_id'],'status'=>array('IN','20,30'),'zone_id' => array('NEQ',$v['zone_id'])))->select();
                if(!empty($user)){
                    foreach($user as $k2=>$v2){
                        $user[$k2]['updatetime'] = date('Y-m-d',$v2['updatetime']);
                        $user[$k2]['system_user_name'] =$v['realname'];
                        $user[$k2]['username'] =decryptPhone($v2['username'],C('PHONE_CODE_KEY'));
                        $log['data'][] = $user[$k2];
                    }
                    $log['cont'] = count($user)+$log['cont'];
                }

            }
            $log_data = array();
            foreach($log['data'] as $k3=>$v3){
                $log_data[ $v3['updatetime'] ][] = $v3;
            }
            F('getUserZone',$log_data);
//            print_r($log_data);exit;

            foreach($log_data as $k=>$v){
                foreach($v as $v2){
                    echo $k."	".$v2['username']."	".$v2['system_user_name']."	".$v2['zone_id']."	".$v2['user_id'].'<br/>';
                }
            }

//            $this->redirect('/System/Move/getUserZone',array('p' =>$p+1,'count' => $count));
//        }
    }

    public function getCallback()
    {
//       $temp = M('user_callback','zl_','DB_CONFIG1')->field('callbacktime')->where(array('callbacktime'=>array('EGT','1476007200'),'remark'=>'系统分配','callbacktype'=>30))->save(array('callbacktime'=>array('exp','callbacktime+2')));
//
//        print_r($temp);
//        exit;
        set_time_limit(3000);
        $temp_users = F('temp_user_systemuser');
//        $roles = array(160282,160225,114375,160255,99739,160240,30,160242);
//        foreach($temp_users as $v){
//            $temp = M('user_callback','zl_','DB_CONFIG1')->field('system_user_id,user_id')->where(array('user_id'=>$v,'callbacktime'=>array('EGT','1475942400'),'remark'=>'系统超时回收'))->find();
//            $temp_user_systemuser[] = array('user_id'=>$v,'system_user_id'=>$temp['system_user_id']);
//        }
        $temp_flag = array();
        foreach($temp_users as $v){
            //是否有申请转入中？
            $apply = M('user_apply','zl_','DB_CONFIG1')->where(array('status'=>10,'user_id'=>$v['user_id']))->find();
            if(empty($apply)){
                //是否为带跟进 带联系 回库状态
                $is_user = M('user','zl_','DB_CONFIG1')->field('callbacknum,user_id,status')->where(array('user_id'=>$v['user_id'],'status'=>array('IN','20,30,160')))->find();
                if(!empty($is_user)){
                    //添加回访记录
                    $add_ab = array(
                        'user_id'=>$v['user_id'],
                        'system_user_id'=>$v['system_user_id'],
                        'callbacktime'=>time(),
                        'nexttime'=>time(),
                        'remark'=>'系统回收',
                        'callbacktype'=>31
                    );
                    M('user_callback','zl_','DB_CONFIG1')->add($add_ab);
                    $add_all = array(
                        'user_id'=>$v['user_id'],
                        'system_user_id'=>$v['system_user_id'],
                        'callbacktime'=>time(),
                        'nexttime'=>time(),
                        'remark'=>'系统分配',
                        'callbacktype'=>30
                    );
                    M('user_callback','zl_','DB_CONFIG1')->add($add_all);
                    //恢复数据
                    if($is_user['status']==160){
                        if($is_user['callbacknum']>0){
                            $save_user['status'] = 30;
                        }else{
                            $save_user['status'] = 20;
                        }
                    }
                    $save_user['system_user_id'] = $v['system_user_id'];
                    $save_user['updateuser_id'] = $v['system_user_id'];
                    M('user','zl_','DB_CONFIG1')->where(array('user_id'=>$v['user_id']))->save($save_user);
                    $temp_flag[] = $v['user_id'];
                }
            }
        }
        F('user_flag',$temp_flag);
        exit;


        $temp_users = array();
        foreach($reList as $v){
            $info = M('user','zl_','DB_CONFIG1')->field('user_id,system_user_id')->where(array('user_id'=>$v['user_id'],'status'=>160,'callbacknum'=>array('EGT',1)))->find();
            if(!empty($info)){
                $apply = M('user_apply','zl_','DB_CONFIG1')->where(array('status'=>10,'user_id'=>$v['user_id']))->find();
                if(empty($apply)){
                    M('user_apply','zl_','DB_CONFIG1')->where(array('user_id'=>$v['user_id']))->save(array('status'=>'30'));
                    $temp_users[] = $v['user_id'];
                }
            }
        }
        F('temp_users',$temp_users);
    }

    public function getUserMobileCity(){
        $count = I('count');
        if(empty($count)){
            $count =  M('user','zl_','DB_CONFIG1')->where(array('phonevest'=>array('exp','is null')))->count();
        }
        $p = I('p');
        $p = empty($p) ? 1 : $p;
        if($p==1){
            $log['cont'] = 0;
            $log['data'] = '';
            $log['userid'] = array();
            F('getUserRepeat',$log);
        }
        $checkform = new \Org\Form\Checkform();
        if($p<=ceil($count/500)){
            $user =  M('user','zl_','DB_CONFIG1')->field('username,user_id')->where(array('phonevest'=>array('exp','is null')))->order('user_id desc')->limit((($p-1)*500).',500')->select();

            $log = F('getUserRepeat');
            if(!empty($user)){
                foreach ($user as $k => $v){
                    $username = decryptPhone($v['username'], C('PHONE_CODE_KEY'));
                    if($checkform->checkMobile($username)){
                        $reApi = phoneVest($username);
                        if(!empty($reApi)) {
                            $data['phonevest'] = $reApi['city'];
                        }else{
                            $data['phonevest'] = '';
                        }
                        M('user','zl_','DB_CONFIG1')->field('phonevest')->where(array('user_id'=>$v['user_id']))->save($data);
                    }

                }
            }
            F('getUserRepeat',$log);
            $this->redirect('/System/Move/getUserMobileCity',array('p' =>$p+1,'count' => $count));
        }
    }

////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
    public function addOrder()
    {

	     $orderlogs = M("order")->where("payway = 0")->save(array('payway'=>2));
         dump($orderlogs);
         exit;

        //获取员工信息
        $system_user = M('system_user','zl_','DB_CONFIG1')->field('system_user_id')->where(array('realname'=>$arr['D']))->find();
        if(!empty($system_user)){
            $system_user_id = $system_user['system_user_id'];
        }else{
            $system_user_id = 0;
        }
        //获取用户信息
        $user = M('user','zl_','DB_CONFIG1')->field('user_id,status,realname')->where(array('realname'=>array('like','%'.$arr['C']),'system_user_id'=>$system_user_id))->find();
        if(!empty($user)){
            $user_id = $user['user_id'];
            if($user['status']!=70){
                M('user','zl_','DB_CONFIG1')->where(array('user_id'=>$user_id))->save(array('status'=>70));
            }
            if($user['realname']!=$arr['C']){
                M('user','zl_','DB_CONFIG1')->where(array('user_id'=>$user_id))->save(array('realname'=>$arr['C']));
            }
        }else{
            $userList = M('user','zl_','DB_CONFIG1')->field('user_id')->where(array('realname'=>$arr['C'],'status'=>70))->find();
            if(count($userList)==1){
                $user_id = $userList['user_id'];
            }else{
                return  array('code'=>1,'data'=>$arr,'msg'=>'找不到该客户');
            }
        }
        $auditoruser_id = (int)$arr['A'];
        if($auditoruser_id==55399){
            $zone_id = 3;
        }elseif($auditoruser_id==104447){
            $zone_id = 4;
        }else{
            $zone_id = 2;
        }
        //课程ID
        if( $arr['B']=='游戏UI' ){
            $arr_course_id = 8;
        }elseif( $arr['B']=='安卓开发' ){
            $arr_course_id = 4;
        }elseif( $arr['B']=='WEB前端' || $arr['B']=='web开发' || $arr['B']=='Web前端' ){
            $arr_course_id = 5;
        }elseif( $arr['B']=='UI设计' ){
            $arr_course_id = 3;
        }elseif( $arr['B']=='软件测试' || $arr['B']=='测试' || $arr['B']=='软件开发' ){
            $arr_course_id = 2;
        }elseif( $arr['B']=='JAVA开发' ){
            $arr_course_id = 1;
        }
        //学费
        if($arr_course_id==2){
            $arr_coursecost = 18800;
        }elseif($arr_course_id==1){
            $arr_coursecost = 20800;
        }else{
            $arr_coursecost = 19800;
        }

        //缴费方式
        if( $arr['E']=='POS' ){
            $arr_payway = 2;
        }elseif( $arr['E']=='现金' ){
            $arr_payway = 1;
        }else{
        	$arr_payway = 0;
        }
        $arr_sparecost = (int)$arr_coursecost-(int)$arr['H'];
    	//添加订单
        $add_order = array(
            'zone_id'=>$zone_id,
            'user_id'=>$user_id,
            'system_user_id'=>$system_user_id,
            'auditoruser_id'=>$auditoruser_id,
            'status'=>40,
            'course_id'=>$arr_course_id,
            'subscription'=>$arr['H'],
            'coursecost'=>$arr_coursecost,
            'paycount'=>$arr_coursecost,
            'discountcost'=>0,
            'payway'=>$arr_payway,
            'cost'=>$arr['H'],
            'sparecost'=>$arr_sparecost,
            'createtime'=>strtotime($arr['F']),
            'finishtime'=>0,
        );
        $orderInfo = M("orders")->where($add_order)->find();
        if ($orderInfo) {
        	return array('code'=>1,'data'=>$arr,'msg'=>'已创建订单');
        }else{
        	$orderId = M('orders')->add($add_order);
	        if ($orderId) {
	        	$orderLogs['order_id'] = $orderId;
		        $orderLogs['zone_id'] = $zone_id;
		        $orderLogs['auditoruser_id'] = $auditoruser_id;
		        $orderLogs['paytype'] = 1;
		        $orderLogs['payway'] = $arr_payway;
		        $orderLogs['cost'] = $arr['H'];
		        $orderLogs['createtime'] = strtotime($arr['F']);
		        $orderlogId = M('order_logss')->add($orderLogs);
		        if ($orderlogId == false) {
		        	return array('code'=>1,'data'=>$arr,'msg'=>'创建订单记录失败');
		        }
	        	return array('code'=>0,'data'=>$arr,'msg'=>'创建订单成功');
	        }else{
	        	return array('code'=>1,'data'=>$arr,'msg'=>'创建订单失败');
	        }
        }
    }






////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

    public function getUserRepeat(){
        $count = I('count');
        if(empty($count)){
            $count =  M('user','zl_','DB_CONFIG1')->count();
        }
        $p = I('p');
        $p = empty($p) ? 1 : $p;
        if($p==1){
            $log['cont'] = 0;
            $log['data'] = '';
            $log['userid'] = array();
            F('getUserRepeat',$log);
        }
        $checkform = new \Org\Form\Checkform();
        if($p<=ceil($count/500)){
            $user =  M('user','zl_','DB_CONFIG1')->field('username,user_id')->order('user_id desc')->limit((($p-1)*500).',500')->select();
            $log = F('getUserRepeat');
            if(!empty($user)){
                foreach ($user as $k => $v){
                    $userNum = M('user','zl_','DB_CONFIG1')->field('username,user_id')->where(array('username'=>$v['username']))->select();
                    if(!empty($userNum) && !empty($v['username']) && count($userNum)>1) {

                        foreach($userNum as $k2=>$v2){
                            if(!in_array($v2['user_id'],$log['userid'])){
                                $log['cont'] = $log['cont']+1;
                                $log['data'][] = $userNum;
                                $log['userid'][] = $v2['user_id'];
                            }
                        }

                    }
                }
            }
            F('getUserRepeat',$log);
            $this->redirect('/System/Move/getUserRepeat',array('p' =>$p+1,'count' => $count));
        }
    }

    public function printSession()
    {
        print_r(F('getUserRepeat'));

//        $allFiles = F('getUserRepeat');
//
//        $delData = '';
//        foreach($allFiles['data'] as $k=>$v){
//            $user = M('user_info','zl_','DB_CONFIG1')->field('user_info_id,user_id')->where(array('user_id'=>$v['user_id']))->select();
//            if(count($user)>=2){
//                foreach($user as $k2=>$v2){
//                    if($k2==0){
//                        M('user_info','zl_','DB_CONFIG1')->where(array('user_info_id'=>$v2['user_info_id']))->delete();
//                        $delData[] = array('user_info_id'=>$v2['user_info_id'],'user_id'=>$v2['user_id']);
//                    }
//                }
//            }
//        }
//        print_r($delData);
//        print_r($allFiles);
//        $dataTrim = session('getUserTrimLog')['dataTrim'];
//        foreach($dataTrim as $k=>$v){
//            $username = encryptPhone($v['username'], C('PHONE_CODE_KEY'));
//            $user = M('user')->field('status,user_id')->where(array('username'=>$username))->find();
//            $username2 = encryptPhone(trim($v['username']), C('PHONE_CODE_KEY'));
//            $user2 = M('user')->field('status,user_id')->where(array('username'=>$username2))->find();
//            echo '-----------------------</br>';
//            echo $user['status'].':'.$user['user_id'].':'.$username.'</br>';
//            echo $user2['status'].':'.$user2['user_id'].':'.$username2.'</br>';
//            echo '-----------------------</br>';
//        }
//        print_r( session('getUserRepeat'));
//        exit;
    }

    public function updateSystemUser(){
        $count = I('count');
        if(empty($count)){
            $count =  M('UserinfoCopy')->count();
        }
        $p = I('p');
        $p = empty($p) ? 1 : $p;
        $Page       = new \Think\Page($count,1000);
        $userinfo = M('UserinfoCopy')->limit($Page->firstRow.','.$Page->listRows)->order('user_id desc')->select();

        foreach ($userinfo as $k => $v){
            $updateuser_id = empty($v['invitation_id']) ? $v['service_id'] : $v['invitation_id'];
            $systemuser_id = empty($v['consulting_id']) ? $updateuser_id : $v['consulting_id'];
            $updateuser_id = empty($updateuser_id) ? $systemuser_id : $updateuser_id;

            $data['system_user_id'] = $systemuser_id;
            $data['updateuser_id'] = $updateuser_id;
            M('User')->where(array('user_id'=>$v['user_id']))->save($data);
            echo 'id:'.$v['user_id'].' yaoyue:'.$v['invitation_id'].' kefu:'.$v['service_id'].' zixun:'.$v['consulting_id'].'<br/>';
            echo 'chuangjianren:'.$updateuser_id.' suoshuren:'.$systemuser_id.'<br/><br/><br/>';
        }
        $this->redirect('/System/Move/updateSystemUser',array('p' =>$p+1,'count' => $count));
    }

    public function checkUserFee(){
        $count = I('count');
        if(empty($count)){
            $count =  M('FeeCopy','zl_','DB_CONFIG1')->where(array('scost'=>array('neq',0),'audit_id'=>array('neq',3)))->count();
        }
        $p = I('p');
        $p = empty($p) ? 1 : $p;
        $Page       = new \Think\Page($count,1000);

        $feelist = M('FeeCopy','zl_','DB_CONFIG1')->limit($Page->firstRow.','.$Page->listRows)->where(array('scost'=>array('neq',0),'audit_id'=>array('neq',3)))->select();

        $newFee = M('fee_logs');

        $i=1;

        foreach($feelist as $k=>$v){
            if($v['audit_id']==1){
                $data['auditor_status'] = 20;
            }else if($v['audit_id']==2){
                $data['auditor_status'] = 30;
            }else if($v['audit_id']==0){
                $data['auditor_status'] = 10;
            }

            $data['pay'] = $v['scost'];
            $data['receivetime'] = $v['stime'];
            $data['receivetype'] = $v['stype_id'];
            $data['paytype'] = 1;

            $nuser = $newFee->where(array('user_id'=>$v['user_id'],'fee_id'=>$v['fee_id']))->find();
            echo '<pre>';
            print_r($nuser);
            echo '</pre>';

        }


        $this->redirect('/System/Move/checkUserFee',array('p' =>$p+1,'count' => $count));
    }

    public function joinUserData(){
        //set_time_limit(0);
        echo M('User')->count().'<br/>';
        $count = I('count');
        if(empty($count)){
            $count =  M('UserCopy')->count();
        }
        $p = I('p');
        $p = empty($p) ? 1 : $p;
        $Page       = new \Think\Page($count,1000);
        $join = 'zl_userinfo_copy on zl_user_copy.user_id=zl_userinfo_copy.user_id';
        $user = M('UserCopy')->join($join)->limit($Page->firstRow.','.$Page->listRows)->where(array('usertype'=>0))->order('zl_user_copy.user_id desc')->select();
        echo M('UserCopy')->getLastSql().'<br/>';
        $newUser = M('User');
        $system_userdb = M('SystemUser');
        $zl_channel_marker_copy = M(ChannelMarkerCopy);
        $zl_visit_copy = M('VisitCopy');
        $channels = M('Channel')->select();
        $zl_user_callback_copy = M('UserCallbackCopy');
        $i=1;

        foreach($user as $k => $v){
            $nuser = M('User')->where(array('user_id'=>$v['user_id']))->find();
            if(empty($nuser)){

                //创建人与所属人
                $updateuser_id = empty($v['invitation_id']) ? $v['service_id'] : $v['invitation_id'];
                $systemuser_id = empty($v['consulting_id']) ? $updateuser_id : $v['consulting_id'];
                $updateuser_id = empty($updateuser_id) ? $systemuser_id : $updateuser_id;
                //所属人信息
                $system_user = $system_userdb->where(array('system_user_id'=>$systemuser_id))->find();
                $marker = $zl_channel_marker_copy->where(array('user_id'=>$v['user_id']))->find();
                $visittime = $zl_visit_copy->where(array('user_id'=>$v['user_id']))->order('visittime asc')->find();//找到访时间
                $arr = $this->parentFind($channels, $v['channel_id']);
                $callbacknum = $zl_user_callback_copy->where(array('user_id'=>$v['user_id']))->count();
                $waytype = $zl_user_callback_copy->where(array('user_id'=>$v['user_id']))->order('user_id desc')->find();
                $data['user_id'] = $v['user_id'];
                $data['zone_id'] = $system_user['zone_id'];
                $data['infoquality'] = $v['infolevel_id'];
                $data['username'] = encryptPhone($v['username'],C('PHONE_CODE_KEY'));
                $data['qq'] = $v['qq'];
                $data['email'] = $v['email'];
                $data['password'] = $v['password'];
                $data['nickname'] = $v['nickname'];
                $data['realname'] = $v['realname'];
                $data['face'] = $v['face'];
                $data['status'] = $v['status']==10 ? 160 :$v['status'];
                $data['usertype'] = $v['usertype'];
                $data['weight'] = $v['weight'];
                $data['searchkey'] = $v['keyword'];
                $data['interviewurl'] = $v['interviewurl'];
                $data['class_id'] = $v['class_id'];
                $data['course_id'] = empty($v['course_id']) ? 0 : $v['course_id'];
                $data['learningtype'] = 1;
                $data['mark'] = 1;
                $data['introducermobile'] = empty($marker['recommend_phone']) ? 0 :$marker['recommend_phone'];
                $data['promote_id'] = $v['promote_id'];
                $data['lastvisit'] = $v['interviewtime'];
                $data['nextvisit'] = $v['nextinterviewtime'];
                $data['updatetime'] = $v['allottime'];
                $data['visittime'] = empty($visittime['visittime']) ? 0 : $visittime['visittime'];
                $data['channel_id'] = empty($arr[1]['channel_id']) ? $arr[0]['channel_id'] : $arr[1]['channel_id'];
                $data['attitude_id'] = $v['result'];
                $data['callbacknum'] = $callbacknum;
                $data['system_user_id'] = $systemuser_id;
                $data['logintime'] = $v['logintime'];
                $data['loginip'] = $v['loginip'];
                $data['createtime'] = $v['jointime'];
                $data['createip'] = $v['joinip'];
                $data['updateuser_id'] = $updateuser_id;
                $data['waytype'] = empty($waytype['backtype']) ? 0 : $waytype['backtype'];
                $result = $newUser->add($data);
                if($result !== false){
                    echo $i.'.已加入-'.$v['user_id'].'《《《《《《《《《《《《《《《《《<br/>';
                }else{
                    echo $i.'.加入失败-'.$v['user_id'].'》》》》》》》》》》》》》》》》<br/>';
                }
                $this->printDIY($data);


            }else{
                echo $i.'.已有-'.$v['user_id'].'<br/>';

            }
            $i++;

        }
            $this->redirect('/System/Move/joinUserData',array('p' =>$p+1,'count' => $count));

    }

    public function joinUserinfoData(){
        header("Content-type: text/html; charset=utf-8");
        set_time_limit(0);
        $newUserTotal = M('UserInfo')->count();
        echo $newUserTotal.'<br/>';
        $count = I('count');
        $userold = M('Userold');
        if(empty($count)){
            $count =  $userold->field('user_id')->count();
        }
        $p = I('p');
        $p = empty($p) ? 1 : $p;
        $pageSize = 1000;
        $Page  = new \Think\Page($count,$pageSize);
        $pagetotal = ceil($count/$pageSize);

        $join = 'LEFT JOIN zl_userinfo_copy ON zl_userinfo_copy.user_id = zl_userold.user_id';
        $user = $userold->field('userinfo_id,zl_userold.user_id,sex,birthday,education_id,major,remark,school,province_id,city_id,area_id')->join($join)->where(array('usertype'=>0))->limit($Page->firstRow.','.$Page->listRows)->order('zl_userold.user_id desc')->select();
        $newUserInfo = M('UserInfo');

        foreach($user as $key => $value){
            $newUser= M('UserInfo')->field('user_id')->where(array('user_id'=>$value['user_id']))->find();
            if(empty($newUser)){

                $data['user_info_id'] = $value['userinfo_id'];
                $data['user_id'] = $value['user_id'];
                $data['sex'] = $value['sex'];
                $data['birthday'] = $value['birthday'];
                //$data['identification'] =
                //$data['homeaddress'] =
                //$data['address'] =
                //$data['urgentname'] =
                //$data['urgentmobile'] =
                //$data['postcode'] =
                $data['education_id'] = $value['education_id'];
                $data['major'] = $value['major'];
                $data['remark'] = $value['remark'];
                $data['school'] = $value['school'];
                //$data['workyear'] =
                //$data['lastposition'] =
                //$data['lastcompany'] =
                //$data['lastsalary'] =
                //$data['wantposition'] =
                //$data['wantsalary'] =
                //$data['workstatus'] =
                //$data['englishstatus'] =
                //$data['englishlevel'] =
                //$data['computerlevel'] =
                $data['province_id'] = $value['province_id'];
                $data['city_id'] = $value['city_id'];
                $data['area_id'] = $value['area_id'];
                $result = M('UserInfo')->add($data);
                if($result !== false){
                    echo $value['user_id'].'-数据已插入';
                    $this->printDIY($data);
                }else{
                    echo $value['user_id'].'-数据插入失败<br/>';
                }

            }else{
                echo $value['user_id'].'-数据已存在无需修复<br/>';
            }
        }

        $this->redirect('/System/Move/joinUserinfoData',array('p' =>$p+1,'count' => $count));

    }

    function updated(){
       $user = M('User')->where(array('updatetime'=>array(array('EGT',1465693200),array('ELT',1465700400)),'channel_id'=>array('IN',array(3,492,491,29,28,26,27))))->select();

       foreach($user as $k => $v){
           $ids[] = $v['user_id'];
       }

       $zl_user_copy = M('UserCopy')->where(array('user_id'=>array('IN',$ids)))->select();

       foreach($zl_user_copy as $k => $v){
           $new[$v['user_id']] = $v;
       }

       /* foreach($user as $k => $v){
           $data['status'] = 160;
           $data['updatetime'] = $new[$v['user_id']]['jointime'];
           M('User')->where(array('user_id'=>$v['user_id']))->save($data);

       } */

    }

    public function updateLastTime(){
        set_time_limit(0);
        $count = I('count');
        if(empty($count)){
            $count =  M('User')->where(array('lastvisit'=>0))->count();
        }
        $p = I('p');
        $p = empty($p) ? 1 : $p;
        $Page       = new \Think\Page($count,1000);
        $totalPages = ceil($count/1000);
        $user = M('User')->where(array('lastvisit'=>0))->limit(1000)->select();
        //echo M('User')->getLastSql().'<br/>';
        echo $count.'<br/>';
        $i = 1;
        if(!empty($user)){
            foreach($user as $k => $v){

                //echo 'user_id'.$v['user_id'].'最后回访时间'.$v['lastvisit'].'--分配时间'.$v['allocationtime'].'--出库时间'.$v['updatetime'].'--创建时间'.$v['createtime'].'<br/>';
                if(empty($v['lastvisit']) && !empty($v['allocationtime'])){
                    $v['lastvisit'] = $v['allocationtime'];
                }
                if(empty($v['lastvisit']) && !empty($v['updatetime'])){
                    $v['lastvisit'] = $v['allocationtime'] = $v['updatetime'];
                }
                if(empty($v['lastvisit']) && !empty($v['createtime'])){
                    $v['lastvisit'] = $v['allocationtime'] = $v['updatetime'] = $v['createtime'];
                }

                $data = array();
                $data['lastvisit'] = $v['lastvisit'];
                $data['allocationtime'] = $v['allocationtime'];
                $data['updatetime'] = $v['updatetime'];
                $data['createtime'] = $v['createtime'];


                $result = M('User')->where(array('user_id'=>$v['user_id']))->save($data);
                echo $i.'. ';
                echo M('User')->getLastSql().'<br/>';
                $i++;
                //$this->printDIY($data);
                //echo '返回结果'.$result.'<br/>';
            }
        }

        if($totalPages > $p){
            $this->redirect('/System/Move/updateLastTime',array('p' =>$p+1,'count' => $count));
        }
    }

    public function callback(){
        exit;
        $user = M('User')->where(array('system_user_id'=>49))->select();
        foreach($user as $k => $v){
            $data['status'] = 1;
            M('UserCallback')->where(array('user_id'=>$v['user_id']))->save($data);
        }

        $this->printDIY($user);
    }

    public function updateRemark(){
        set_time_limit(0);
        $count = I('count');
        if(empty($count)){
            $count =  M('UserinfoCopy')->field('user_id')->count();
        }
        $p = I('p');
        $p = empty($p) ? 1 : $p;
        $pageSize = 1000;
        $Page       = new \Think\Page($count,$pageSize);
        $totalPages = ceil($count/$pageSize);
        $userinfo = M('UserinfoCopy')->field('user_id,remark')->limit($Page->firstRow.','.$Page->listRows)->select();
        $percent = (($pageSize*$p)/$count)*100;
        echo $percent.'%<br/>';
        $i=1;
        foreach($userinfo as $k => $v){
            $userinfo2 = M('UserInfo')->field('remark')->where(array('user_id'=>$v['user_id']))->find();
            echo $i.'.'.$v['user_id'];
            if(empty($userinfo2['remark']) && !empty($v['remark'])){
                $data['remark'] =$v['remark'];
                $result = M('UserInfo')->where(array('user_id'=>$v['user_id']))->save($data);
                if($result !== false){
                    echo ':已导入--'.$v['remark'].'--结果：'.$result.'<br/>';
                }else{
                    echo ':导入失败--'.$v['remark'].'<br/>';
                }
            }else{
                echo ':不需要导入<br/>';
            }
            $i++;
        }

        $this->redirect('/System/Move/updateRemark',array('p' =>$p+1,'count' => $count));
    }

    function updateApply(){
        $where['system_user_id'] = array('IN',array(25,26,27,99740,128513));
        $where['status'] = 30;
        $userApply = D('UserApply')->where($where)->select();
        foreach($userApply as $key => $value){
            $user = D('User')->where(array('user_id'=>$value['user_id']))->find();
            $this->printDIY($user);
            $data['createtime'] = $value['applytime'];
            $data['channel_id'] = $value['channel_id'];
            $data['createuser_id'] = $value['system_user_id'];
            $user = D('User')->where(array('user_id'=>$value['user_id']))->save($data);
            $this->printDIY($user);
        }
    }


    function updateCreateuser(){
        exit();
        set_time_limit(0);
        $count = I('count');
        if(empty($count)){
            $count =  M('UserOld2016')->where(array('usertype'=>0))->count();
        }
        $p = I('p');
        $p = empty($p) ? 1 : $p;
        $Page       = new \Think\Page($count,1000);
        $totalPages = ceil($count/1000);
        $join = "LEFT JOIN zl_userinfo_old2016 ON zl_user_old2016.user_id=zl_userinfo_old2016.user_id";
        $olduser = M('UserOld2016')->join($join)->where(array('usertype'=>0))->limit($Page->firstRow.','.$Page->listRows)->select();
        $i =1;
        foreach($olduser as $key => $value){
            $data['createuser_id'] = $value['create_id'];
            $result = M('User')->where(array('user_id'=>$value['user_id']))->save($data);
            //echo 'UPDATE zl_user SET createuser_id='.$value['create_id'].' WHERE user_id='.$value['user_id'].';<br/>';
            echo $i.'. ';
            if($result === false){
                echo $value['user_id'].'更新失败<br/>';
            }else if($result===0){
                echo $value['user_id'].'更新无改变<br/>';
            }else if($result){
                echo $value['user_id'].'更新成功<br/>';
            }
            echo '-------------------------------------<br/>';
            $i++;
        }
        if($totalPages>$p){
            $this->redirect('/System/Move/updateCreateuser',array('p' =>$p+1,'count' => $count));
        }
    }

    function getLaowang(){
        $user = M('User')->where(array('createuser_id'=>49))->select();
        $this->printDIY($user);
    }


    public function userdel(){
        $where1['createtime'] = array('GT',1468836000);
        $where2['createtime'] = array('LT',1468839600);
        $where['_complex']['_logic'] = 'and';
        $where['_complex'][] = $where1;
        $where['_complex'][] = $where2;
        $where['channel_id'] = 492;
        $where['createuser_id'] = 34;
        $user = M('User')->where($where)->select();

        foreach($user as $k => $v){
            $ids[] = $v['user_id'];
        }
        //$result = M('User')->where(array('user_id'=>array('IN',$ids)))->delete();
        //$result = M('UserCallback')->where(array('user_id'=>array('IN',$ids)))->delete();
        $this->printDIY($result);
    }

    function printDIY($data){
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }


    public function zoo(){
        $where['remark'] = array('like','批量回库原因%');
        $userCallback = M('UserCallback')->where($where)->select();

        foreach($userCallback as $k => $v){
            $ids[] = $v['user_id'];
        }

        $w['user_id'] = array('IN',$ids);
        $w['system_user_id'] = 38;
        $user = M('User')->where($w)->select();
        echo M('User')->getLastSql();
        $this->printDIY($user);

    }
}
