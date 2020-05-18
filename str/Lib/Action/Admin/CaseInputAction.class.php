<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CaseInputAction extends CommonAction {

    //案件录入首页
    public function index() {
        $this->assign('playlist', '首页');
        $this->assign("level7", "active open");
        $this->display();
    }

    //新增任务
    public function handAddCase() {
        $Court = new CourtModel();
        //取出入流检察院
        $courts = $Court->getInputCourt();
        //取出出流检察院
        //$live=$Court->getOutputCourt();
        $this->assign('courts', $courts);
        //$this->assign('live',$live);
        $this->display("handAddCase");
    }

    //新增任务操作
    public function handAddCaseHandler() {
        $Playlist = D('playlist');
        $data = array();
        if (!$Playlist->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            exit($this->error($Playlist->getError()));
        } else {
            $case_name = $_POST[P_CaseName];
            $courtNo = $_POST['P_CourtNo'];
            $case_res = $Playlist->where("t_playlist.P_CaseName like '%" . $case_name . "%' and P_CourtNo = $courtNo")->find();
            if (!empty($case_res)) {
                $this->error("已有相同案件，请勿重复申请相同案件！");
            } else {
                $_REQUEST['P_CaseName'] = preg_replace('# #', '', $_REQUEST['P_CaseName']);
                $_REQUEST['P_RequestCourt'] = $_SESSION['M_Court']; //申请人所属检察院
                $_REQUEST['P_RequestMan'] = $_SESSION['M_ID']; //申请人
                $_REQUEST['P_RequestTime'] = date('Y-m-d H:i:s', time()); //申请时间
                $_REQUEST['P_CaseCode'] = '99' . time();
                $_REQUEST['P_ID'] = date('YmdHis', time()) . rand(10, 100); //P_ID,年月日时分秒+两位随机数
                $_REQUEST['P_DelayStartTime'] = date("Y-m-d H:i:s", strtotime("$_REQUEST[P_StartTime] + $_REQUEST[P_DelayMin] min"));
                $_REQUEST['P_DelayEndTime'] = date("Y-m-d H:i:s", strtotime("$_REQUEST[P_EndTime] + $_REQUEST[P_DelayMin] min"));
                $_REQUEST['P_SubmitTo'] = $_POST['P_CourtIn']; //审核检察院

                $procurator = $this->getString($this->code($_REQUEST['procurator']));
                $hostprocurator = $this->getString($this->code($_REQUEST['hostprocurator']));
                $procuratoraid = $this->getString($this->code($_REQUEST['procuratoraid']));
                $clerk = $this->getString($this->code($_REQUEST['clerk']));
                $party = $this->getString($this->code($_REQUEST['party']));
                $hear = $this->getString($this->code($_REQUEST['hear']));
                $sit = $this->getString($this->code($_REQUEST['sit']));
                $police = $this->getString($this->code($_REQUEST['police']));
                $_REQUEST['P_JudgeGroup'] = '检察官:' . $procurator . ';主办检察官:' . $hostprocurator
                        . ';检察官助理:' . $procuratoraid . ';书记员:' . $clerk . ';案件当事人:' . $party
                        . ';听证员:' . $hear . ';旁听人员:' . $sit . ';司法警察:' . $police . ';';
//                var_dump($_REQUEST);exit;
                $res = $Playlist->add($_REQUEST);
                if ($res) {
//                    $outdata  = $this->_before_liveResHandler();
                    //保存日志
                    saveLog("手动申请案件任务", $_REQUEST['P_ID']);
                    $this->success("申请案件成功");
                } else {
                    $this->error("操作失败");
                }
            }
        }
    }

    //任务编辑页面
    public function editCase() {
        $P_ID = htmlspecialchars($_GET['P_ID']); //任务ID
        $Playlist = M('playlist');
        $playlist = $Playlist->where("P_ID=$P_ID")->find();
//        var_dump($playlist);
        //取出入流检察院对应的所有听证室
        $Courtroom = M('courtroom');
        $courtrooms = $Courtroom->where("CR_Belongs=$playlist[P_CourtIn]")->select();
        $Court = new CourtModel();
        //取出入流检察院
        $courts = $Court->getInputCourt();
        //取出出流检察院
        //$live=$Court->getOutputCourt();
        $this->assign('courts', $courts);
        //$this->assign('live',$live);
        //取出Live表中的L_ID
        $Live = M('live');
        $res = $Live->where("L_PID='$playlist[P_OutPID]'")->find();
        $this->assign('LID', $res['L_ID']);
        $this->assign('courts', $courts);
        $this->assign('courtrooms', $courtrooms);
        $this->assign('playlist', $playlist);
        $this->display();
    }

    //任务编辑操作
    public function _before_editCaseHandler() {
        
    }

    public function editCaseHandler() {
        $Playlist = D('playlist');
        //$_POST['P_SubmitTo']=submitCourt($_POST['P_CourtOut'],$_SESSION['M_Court']);//审核检察院
        if (!$Playlist->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            exit($this->error($Playlist->getError()));
        } else {
            // 验证通过 可以进行其他数据操作
            $_POST['P_CaseName'] = preg_replace('# #', '', $_REQUEST['P_CaseName']);
            $_POST['P_DelayStartTime'] = date("Y-m-d H:i:s", strtotime("$_REQUEST[P_StartTime] + $_REQUEST[P_DelayMin] min"));
            $_POST['P_DelayEndTime'] = date("Y-m-d H:i:s", strtotime("$_REQUEST[P_EndTime] + $_REQUEST[P_DelayMin] min"));
//            var_dump($_POST);exit;
            $res = $Playlist->save($_POST);
        }
        if ($res >= 0) {
            //保存日志
            saveLog("编辑任务", $_POST['P_ID']);
            $this->success("操作成功");
        } else {
            $this->error("操作失败");
        }
    }

    public function liveRes() {
        $P_ID = htmlspecialchars($_GET['P_ID']); //任务ID
        $Playlist = M('playlist');
        $playlist = $Playlist->where("P_ID=$P_ID")->find();
        $M_Court = '';

        $Court = new CourtModel();
        //取出入流检察院
        $courts = $Court->getInputCourt($M_Court);
        //取出出流检察院
        //$live=$Court->getOutputCourt();
        $this->assign('courts', $courts);
        $this->assign('playlist', $playlist);
        //$this->assign('live',$live);
        $this->display("liveRes");
    }

    public function liveResHandler() {
        if (empty($_POST['P_OutPID'])) {
            $this->error("端口已满！");
        }
        $Playlist = M('playlist');
        $_POST['P_SubmitTo'] = $_POST['P_CourtIn']; //审核检察院
        if (!$Playlist->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            exit($this->error($Playlist->getError()));
        } else {
            // 验证通过 可以进行其他数据操作
            $res = $Playlist->where("P_ID=$_REQUEST[P_ID]")->save($_POST);
        }
        if ($res >= 0) {
            //保存日志
            saveLog("提交任务", $_REQUEST['P_ID']);
            $this->submitCaseHandler($_REQUEST['P_ID']);
//            $this->success("操作成功");
        } else {
            $this->error("操作失败");
        }
    }

    public function _before_liveResHandler($arr) {
        $Court = new CourtModel();
        $live = $Court->getOutputCourt();
        $Playlist = M('playlist');
        $where = 'and  P_StartTime < "' . $arr[P_StartTime] . '" and P_EndTime > "' . $arr[P_DelayEndTime] . '" ';
        $pidsql = 'select P_OutPID from t_playlist where P_Status = 1 and P_ApplyStatus = 2 ' . $where;
        $findPID = $Playlist->query($pidsql);
        $str = '';
        if (!empty($findPID)) {
            foreach ($findPID as $value) {
                $str .= $value['P_OutPID'] . ',';
            }
            $findLiveSql = 'select * from t_live where L_ID not in (' . substr($str, 0, strlen($str) - 1) . ')';
        } else {
            $findLiveSql = 'select * from t_live';
        }
        $findPIDList = $Playlist->query($findLiveSql);
        if (!empty($findPIDList)) {
            $_POST['P_CourtOut'] = $findPIDList[0][L_CourtName];
            $_POST['P_OutPID'] = $findPIDList[0][L_ID];
            $_POST['P_PullUrl'] = $findPIDList[0][L_PULLURL];
            $_POST['P_PushUrl'] = $findPIDList[0][L_PUSHURL];
            $_POST['P_ChId'] = $findPIDList[0][L_Channel];
        }
        $this->liveResHandler();
    }

    //已申请案件对接延迟接口
    //ajax
    private function submitCaseHandler($P_ID) {
        $Playlist = M('playlist');
        $data[P_Status] = 1; //案件状态 创建成功
        $data[P_LiveStatus] = 1; //直播状态 未开始
        $data[P_ApplyStatus] = 1; // 审核状态 待审核
        $data[P_RequestTime] = date('Y-m-d H:i:s', time()); //任务申请提交时间

        $res = $Playlist->where("P_ID=$P_ID")->save($data);
        if ($res >= 0) {
            $this->success("操作成功");
        } else {
            saveLog("提交任务数据库更新失败+++" . $P_ID, '更新数据库', $data); //写入日志
            $this->error("创建失败");
        }
    }

    //获得检察院名称
    public function getCourtName($cid) {
        $Court = M('court');
        $CourtName = $Court->where("C_ID = $cid")->find();
        return $CourtName['C_Name'];
    }

    //获取听证室名称
    public function getRoomName($rid) {
        $Court = M('courtroom');
        $RoomName = $Court->where("CR_ID = $rid")->find();
        return $RoomName['CR_Name'];
    }

    public function getString($str) {
        $aa = str_replace('"', '', $str);
        $bb = substr($aa, 1);
        $cc = substr($bb, 0, -1);
        return $cc;
    }

    public function code($param) {
        $str = json_encode($param);
        $str = preg_replace_callback("#\\\u([0-9a-f]{4})#i", function($matchs) {
            return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
        }, $str);
        return $str;
    }

}
