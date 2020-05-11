<?php

class VerifyTaskAction extends CommonAction {

    //审核直播资源首页
    public function index() {
        S(array(
            'type'=>'Redis',
            'host'=>'10.1.1.197',
            'port'=>'6379',
            'prefix'=>'P_',
            'expire'=>30)
        );
        S('run',0);
        //选择属于审核人员检察院的申请单，playlist中P_SubmitTo
        $M_Court = $_SESSION['M_Court']; //取出user表中审核人员所属的检察院
        $Playlist = M('playlist');
        if ($_SESSION['administrator']) { //超级管理员账号可看所有未审核账号
            $playlist = $Playlist->where("t_playlist.P_ApplyStatus=1 and t_playlist.P_Status=1")
                            ->join("t_court on t_playlist.P_CourtIn=t_court.C_ID")
                            ->order("P_StartTime desc")->limit(500)->select();
        } else {
            $playlist = $Playlist->where("t_playlist.P_ApplyStatus=1 and t_playlist.P_SubmitTo=$M_Court and t_playlist.P_Status=1")
                            ->join("t_court on t_playlist.P_CourtIn=t_court.C_ID")
                            ->order("P_StartTime desc")->limit(500)->select();
        }

//        var_dump($playlist);exit;
        $this->assign('playlist', $playlist);
        $this->assign("level3", "active open");
        $this->display();
    }

    //审核查看任务
    public function checkTask() {
        $P_ID = htmlspecialchars($_GET['P_ID']); //任务ID
        $Playlist = M('playlist');
        $playlist = $Playlist->where("P_ID=$P_ID")
                ->join("t_court on t_playlist.P_CourtIn=t_court.C_ID")
                ->join("t_courtroom on t_playlist.P_CourtRoomIn=t_courtroom.CR_ID")
                ->find();
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
        $res = $Live->where("L_Decoder='$playlist[P_OutPID]'")->find();
        $this->assign('LID', $res['L_ID']);
        $this->assign('courts', $courts);
        $this->assign('courtrooms', $courtrooms);
        $this->assign('playlist', $playlist);
        $this->display();
    }

    //审核不通过原因层
    public function reason() {
        $P_ID = htmlspecialchars($_GET['P_ID']);
        $this->assign('P_ID', $P_ID);
        $this->display("reason");
    }

    //审核操作
    //编辑=0，待审核=1，审核通过=2，审核不通过=3
    public function verifyTaskHandler() {
        $run = S('run');
        if($run===1){
            $this->error("请稍后操作");
        }else{
            S(array(
                'type'=>'Redis',
                'host'=>'10.1.1.197', //现网
                'port'=>'6379',
                'prefix'=>'P_',
                'expire'=>30)
            );
            S('run',1);
            $Playlist = M('playlist');
            //如果审核不通过
            if ($_POST['noPass']) {
                $data['P_ID'] = $_POST['P_ID']; //任务ID
                $playlist = $Playlist->where("P_ID=$data[P_ID]")->find();
                $data['P_ApplyStatus'] = 3;
                $data['P_ApplyReason'] = $_POST['P_ApplyReason'];
                if (!$_POST['P_ApplyReason']) {
                    $this->runChack();
                    $this->error("请填写不通过原因！");
                }
                $this->verifyData($data);
            } else {
                $_POST['P_ID'] = $_POST['P_ID']; //任务ID
                $playlist = $Playlist->where("P_ID=$_POST[P_ID]")->find();
                $_POST['P_ApplyStatus'] = 2;
                $this->_before_liveResHandler($playlist);
            }
        }
    }

    public function verifyData($data) {
        $Playlist = M('playlist');
        if ($_SESSION['administrator']) {
            $res = $Playlist->save($data);
        } elseif ($playlist['P_SubmitTo'] == $_SESSION['M_Court']) {
            $res = $Playlist->save($data);
        } else {
            $this->runChack();
            $this->error("此案件您无法审核");
        }
        if ($res) {
            $this->runChack();
            //保存日志
            saveLog("审核通过", $_POST['P_ID']);
            $this->success("操作成功");
        } else {
            $this->runChack();
            $this->error("操作失败");
        }
    }

    public function liveResHandler() {
        if (empty($_POST['P_OutPID'])) {
            $this->error("端口已满！");
        }
        $Playlist = M('playlist');
        if (!$Playlist->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            exit($this->error($Playlist->getError()));
        } else {
            // 验证通过 可以进行其他数据操作
            $res = $Playlist->where("P_ID=$_REQUEST[P_ID]")->save($_POST);
        }
        if ($res >= 0) {
            $data['P_DelayStartTime']=date("YmdHis", strtotime($_REQUEST['P_StartTime']));
            $data['P_DelayEndTime']=date("YmdHis", strtotime($_REQUEST['P_DelayEndTime']));
            $data['P_ChId']=$_POST['P_ChId'];
            $data['P_DelayMin'] = $_REQUEST['P_DelayMin']*60;
            $senddealy = json_decode($this->sendDealy($data),true);
            if($senddealy['code'] == 0){
                $this->runChack();
                saveLog("审核通过", $_POST['P_ID']);
                $this->success("操作成功");
            }  else {
                $this->runChack();
                saveLog("审核未提交前端", $_POST['P_ID']);
                $this->success("延时操作未成功");
            }
        } else {
            $this->runChack();
            $this->error("操作失败");
        }
    }

    public function _before_liveResHandler($arr) {
        $this->runChack();
        $Court = new CourtModel();
        $live = $Court->getOutputCourt();
        $Playlist = M('playlist');
//        $where = 'and  P_StartTime < "' . $_REQUEST[P_StartTime] . '" and P_EndTime > "' . $_REQUEST[P_DelayEndTime] . '" ';
        $where = 'and  (P_StartTime between "'.date("Y-m-d 00:00:00",  strtotime($arr[P_StartTime])) .'" and  "'.$arr[P_StartTime];
        $where .='" or  P_EndTime between "'.$arr[P_DelayEndTime] .'" and "'.date("Y-m-d 23:59:59",  strtotime($arr[P_StartTime])).'")';
        $pidsql = 'select P_OutPID from t_playlist where P_Status = 1 and P_ApplyStatus = 2 ' . $where;
        $Playlist->query("LOCK TABLES t_playlist WRITE");
        $findPID = $Playlist->query($pidsql);
        $Playlist->query("UNLOCK TABLES");
        saveLog('可使用通道', json_encode($findPID),$pidsql);
        $str = '';
        if (!empty($findPID)) {
            foreach ($findPID as $value) {
                $str .= '"'.$value['P_OutPID'] . '",';
            }
            $findLiveSql = 'select * from t_live where L_Decoder not in (' . substr($str, 0, strlen($str) - 1) . ')';
        } else {
            $findLiveSql = 'select * from t_live';
        }
        $Playlist->query("LOCK TABLES t_live WRITE");
        $findPIDList = $Playlist->query($findLiveSql);
        $Playlist->query("UNLOCK TABLES");
        if (!empty($findPIDList)) {
            $_POST['P_CourtOut'] = $findPIDList[rand(0,9)][L_CourtName];
            $_POST['P_OutPID'] = $findPIDList[rand(0,9)][L_Decoder];
            $_POST['P_PullUrl'] = $findPIDList[rand(0,9)][L_PULLURL];
            $_POST['P_PushUrl'] = $findPIDList[rand(0,9)][L_PUSHURL];
            $_POST['P_ChId'] = $findPIDList[rand(0,9)][L_Channel];
        }
        $this->liveResHandler();
    }

    public function sendDealy($data) {
        $url = C('DELAYURL').'livechl/channel/'.$data['P_ChId'];
        $params = array(
            'delay_start_time' => $data['P_DelayStartTime'],
            'delay_end_time' => $data['P_DelayEndTime'],
            'delay_duration' => $data['P_DelayMin'],
        );
        $jsonStr = json_encode($params);
        $response = http_post_json($url, $jsonStr);
        return $response;
    }
    
    public function runChack() {
        S(array(
            'type'=>'Redis',
            'host'=>'10.1.1.197', //现网
            'port'=>'6379',
            'prefix'=>'P_',
            'expire'=>30)
        );
        S('run',2);
    }

}
