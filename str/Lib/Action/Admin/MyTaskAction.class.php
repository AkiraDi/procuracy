<?php

class MyTaskAction extends CommonAction {

    //我的任务首页
    public function index() {
        $Playlist = M('playlist');
        if ($_SESSION['administrator']) { //超级管理员账号可看所有已经通过审核的案件案件
            $playlist = $Playlist->where(" (P_Status=1) and P_ApplyStatus =2  and P_StartTime>=CURDATE()")
                    ->join("t_court on t_playlist.P_CourtIn=t_court.C_ID")
                    ->join("t_courtroom on t_playlist.P_CourtRoomIn=t_courtroom.CR_ID")
                    ->select();
        }else{
            $playlist = $Playlist->where(" (P_Status=1) and P_ApplyStatus =2  and P_StartTime>=CURDATE() and P_RequestMan=$_SESSION[M_ID]")
                    ->join("t_court on t_playlist.P_CourtIn=t_court.C_ID")
                    ->join("t_courtroom on t_playlist.P_CourtRoomIn=t_courtroom.CR_ID")
                    ->select();
        }
        $this->assign('playlist', $playlist);
        $this->assign('time', $time);
        $this->assign("level1", "active open");
        $this->display();
    }

    public function searchlist() {
        $Playlist = M('playlist');
        $StartTime = htmlspecialchars($_REQUEST[StartTime]);
        $EndTime = htmlspecialchars($_REQUEST[EndTime]);
        if ($_SESSION['administrator']) {
            $playlist = $Playlist->where("P_Status=1 and P_ApplyStatus =2  and P_StartTime>'$StartTime' and P_StartTime<'$EndTime'")
                    ->join("t_court on t_playlist.P_CourtIn=t_court.C_ID")
                    ->join("t_courtroom on t_playlist.P_CourtRoomIn=t_courtroom.CR_ID")
                    ->select();
        }  else {
            $playlist = $Playlist->where("P_Status=1 and P_ApplyStatus =2  and P_StartTime>'$StartTime' and P_StartTime<'$EndTime' and P_RequestMan = $_SESSION[M_ID]")
                    ->join("t_court on t_playlist.P_CourtIn=t_court.C_ID")
                    ->join("t_courtroom on t_playlist.P_CourtRoomIn=t_courtroom.CR_ID")
                    ->select();
        }

//        var_dump($playlist);exit;
        foreach ($playlist as $k => $v) {
            if (date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y'))) < $v['P_StartTime'] && $v['P_StartTime'] < date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y')))) { //当天直播
                $playlist[$k]['status'] = 1;
            } elseif ($v['P_StartTime'] < date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')))) {
                $playlist[$k]['status'] = 2;
            }
        }
        $this->assign('playlist', $playlist);
        $this->assign('time', $_REQUEST);
        $this->assign("level1", "active open");
        $this->display('index');
    }

    //查看任务单
    public function checkTask() {
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

    //取消任务
    public function editTask() {
        $P_ID = htmlspecialchars($_GET['P_ID']); //任务ID
        $Playlist = M('playlist');
        $playlist = $Playlist->where("P_ID=$P_ID")->find();
        $this->assign('playlist', $playlist);
        $this->display();
    }

    //取消任务操作
    public function editTaskHandler() {
        $P_ID = $_POST['P_ID'];
        $Playlist = M('playlist');
        $playlist = $Playlist->where("P_ID=$P_ID")->find();
        $_POST['P_Result'] = 2;//直播失败
        $_POST['P_Status'] = 2;//案件状态取消
        $endtime = strtotime($playlist['P_EndTime']);
        $Playlist->where("P_ID=$P_ID")->save($_POST); //内网改数据库
        $this->success("任务取消成功");
        
    }

    //未通过任务提交
    public function submitTaskHandler() {
        $P_ID = htmlspecialchars($_GET['P_ID']); //任务P_ID
        $data[P_RequestTime] = date('Y-m-d H:i:s', time()); //任务申请提交时间
        $data[P_Status] = 1;
        $Playlist = M('playlist');
        //出流检察院
        $playlist = $Playlist->where("P_ID=$P_ID")->find();

        //判断审核检察院
        $Court = new CourtModel();
        $submit = $Court->submitCourt($playlist[P_CourtOut], $_SESSION[M_Court]);
        $data[P_SubmitTo] = $submit['submitCourtId'];


        $res = $Playlist->where("P_ID=$P_ID")->save($data);
        if ($res) {
            //保存日志
            saveLog("未通过任务提交", $P_ID);
            $this->ajaxReturn('', '提交成功！', 1);
        } else {
            $this->ajaxReturn('', '提交失败！', 0);
        }
    }

    //删除任务
    public function delTaskHandler() {
        $P_ID = htmlspecialchars($_GET['P_ID']); //任务P_ID
        $Playlist = M('playlist');
        $res1 = $Playlist->where("P_ID='$P_ID' ")->find();
        $times = explode(" ", $res1['P_StartTime']);
        if ($times[0] == date('Y-m-d')) {
            $this->error("不能删除当天直播！");
            exit;
        }
        $cre = $this->deleteHYT('delete', $res1); //鸿仪通删除接口
        $cre = json_decode($cre, true);
        if ($cre['respCode'] == '000') {
            $res = $Playlist->where("P_ID='$P_ID' ")->delete();
            if ($res) {
                saveLog("信息删除成功+" . $res1['P_REID'], $res1); //写入日志
                $this->ajaxReturn('', '删除成功！', 1);
            } else {
                $this->ajaxReturn('', '信息删除失败！', 0);
            }
        } else {
            saveLog("信息删除失败", $P_ID . '+' . $res1['P_REID'] + $cre['respMsg']); //写入日志
            $this->ajaxReturn('', '信息删除失败！', 0);
        }
    }

    //鸿仪通取消直播
    public function cancelLiveHYT($type) {
        $Playlist = M('playlist');
        $res = $Playlist->where("t_playlist.P_ID=$_REQUEST[P_ID]")
                ->find();
        $url = C('hytDATA') . $type;
        $time = date('YmdHis') . rand(0, 9) . rand(0, 9) . rand(0, 9);
        $code = md5(C('hytDATACODE') . $time);
        $params = array(
            'code' => $code,
            'timestamp' => $time,
            'id' => $res['P_REID'],
            'reason' => $_POST['P_Fail'],
        );
        $jsonStr = json_encode($params);
        $response = http_post_json($url, $jsonStr);
        return $response;
    }

    //鸿仪通删除直播
    public function deleteHYT($type, $res) {
        $url = C('hytDATA') . $type;
        $time = date('YmdHis') . rand(0, 9) . rand(0, 9) . rand(0, 9);
        $code = md5(C('hytDATACODE') . $time);
        $params = array(
            'code' => $code,
            'timestamp' => $time,
            'id' => $res['P_REID'],
        );
//            var_dump($res);exit;
        $jsonStr = json_encode($params);
        $response = http_post_json($url, $jsonStr);
        return $response;
    }

    //查询所有子检察院字符串
    //return array
    public function getAllChildren($courtID) {
        global $str_children;
        $Court = M('court');
        $res = $Court->where("C_ManageBy=$courtID")->select();
        if ($res) {
            foreach ($res as $v) {
                $str_children .= ',' . $v['C_ID'];
                self::getAllChildren($v['C_ID']);
            }
        }
        return $str_children;
    }

}
