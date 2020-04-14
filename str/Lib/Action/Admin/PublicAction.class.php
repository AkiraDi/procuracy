<?php

class PublicAction extends Action {

    //登录页面
    public function login() {
        $this->assign("M_Name", $_COOKIE['M_Name']);
        $this->assign("M_Password", $_COOKIE['M_Password']);
        $this->display('login');
    }

    //注销
    public function logout() {
        session_unset();
        session_destroy();
        $this->redirect('Public/login');
    }

    //检测用户登录
    public function checkLogin() {
        //表单数据不能为空
//    var_dump(htmlspecialchars($_POST['M_Name']));exit;
        if (htmlspecialchars($_POST['M_Name']) && htmlspecialchars($_POST['M_Password'])) {
            $pwd = htmlspecialchars($_POST['M_Password']);
            $username = htmlspecialchars($_POST['M_Name']);
            //创建数据库对象
            $user = M('user');
            $roleUser = M("role_user");
            //根据用户名查询
            $map['M_Name'] = $username;
            $map['M_IfUse'] = array('gt', 0);
            //加载RBAC类
            import('ORG.Util.RBAC');
            //通过authenticate读取用户信息
            $result = RBAC::authenticate($map);
            $userInfo = $roleUser->where("t_role_user.user_id = $result[M_ID]")
                    ->find();
//				dump($userInfo);die;
            //用户是否禁用
            if ($result['M_IfUse'] == 0) {
                $this->assign("flag", 0);
                $this->error("你已经被禁用");
            }
            //设置cookie
            if (htmlspecialchars($_POST['Remember'] == 1)) {
                $time = time() + 86400 * 30; // 设置24小时的有效期
                setcookie("M_Name", htmlspecialchars($_POST[M_Name], $time)); // 设置一个名字为var_name的cookie，并制定了有效期
                setcookie("M_Password", htmlspecialchars($_POST[M_Password], $time)); // 再将过期时间设置进cookie以便你能够知道var_name的过期时间
            }

            //是否为管理员账户
            if ($result['M_Name'] == C('RBAC_ADMIN')) {
                $_SESSION[C('ADMIN_AUTH_KEY')] = true;
            }
            if ($result) {
                if ($result['M_Password'] == md5($pwd)) {
                    $_SESSION[C('USER_AUTH_KEY')] = $result['M_ID'];
                    $_SESSION["name"] = $result['M_Name'];
                    $_SESSION["M_Court"] = $result['M_Court'];
                    $_SESSION["M_ID"] = $result['M_ID'];
                    $_SESSION['role'] = $userInfo['role_id'];
                    //使用saveAccessList缓存访问权限
                    RBAC::saveAccessList();
                    //$this->assign("level1","active open");
                    $this->redirect('MyTask/index');
                } else {
                    $this->error("用户密码错误");
                }
            } else {
                $this->error("用户名不存在或已经被禁用");
            }
        } else {
            $this->error("用户名不存在或已经被禁用");
        }
    }

    //发送私信
    public function send_msg() {
        $this->display('send_msg');
    }

    //fullcalendar
    public function getEvents() {
        $data = array(
            0 => array(
                'id' => '001',
                'title' => '任务一',
                'start' => '2015-10-09',
                'color' => 'gray'
            ),
            1 => array(
                'id' => '002',
                'title' => '任务二',
                'start' => '2015-10-10',
            ),
            2 => array(
                'id' => '003',
                'title' => '任务三',
                'start' => '2015-10-10',
                'color' => 'gray'
            )
        );
        echo json_encode($data);
        //$this->ajaxReturn($data);
    }

    //提供数据到dateTable
    public function getTableData() {
        $Model = M('role');
        $count = $Model->count();
        $iDisplayStart = htmlspecialchars($_GET[iDisplayStart]);
        $iDisplayLength = htmlspecialchars($_GET[iDisplayLength]);
        $res = $Model->order('id')->limit($iDisplayStart . ',' . $iDisplayLength)->select();
        $data = array();
        foreach ($res as $k => $v) {
            $data[$k]['name'] = $v['name'];
            $data[$k]['status'] = $v['status'];
            $data[$k]['id'] = $v['id'];
        }
        $output['sEcho'] = htmlspecialchars($_GET['sEcho']);
        $output['iTotalDisplayRecords'] = $count;
        $output['iTotalRecords'] = $count;
        $output['iDisplayStart'] = htmlspecialchars($_GET[iDisplayStart]);
        $output['iDisplayLength'] = htmlspecialchars($_GET[iDisplayLength]);
        $output['data'] = $data;
        echo json_encode($output);
    }

    //入流查询选择
    public function portIn() {
        $this->display("portIn");
    }

    //节目单时间查询
    public function searchPlaylist() {
        if (htmlspecialchars($_POST[isSearch])) {
            $_SESSION[searchStartTime] = htmlspecialchars($_POST[startTime]);
            $_SESSION[searchEndTime] = htmlspecialchars($_POST[endTime]);
        }else if(empty($_POST[searchStartTime])||empty($_POST[searchEndTime])){
            $_SESSION[searchStartTime] = date("Y-m-d").' 00:00:00';
            $_SESSION[searchEndTime] = date("Y-m-d").' 23:59:59';
        }
        
        if ($_SESSION['administrator']) {
            $condition = "p.P_StartTime>='$_SESSION[searchStartTime]' and p.P_EndTime<='$_SESSION[searchEndTime]'";
        } else {
            $condition = "p.P_StartTime>='$_SESSION[searchStartTime]' and p.P_EndTime<='$_SESSION[searchEndTime]' "
                    . " and p.P_RequestMan = $_SESSION[M_ID]";
        }
        $model = new Model();
        $sql = "SELECT * FROM t_playlist as p LEFT JOIN t_court on p.P_CourtIn=t_court.C_ID LEFT JOIN t_courtroom on p.P_CourtRoomIn=t_courtroom.CR_ID  WHERE $condition";
        $data = $model->query($sql);
        foreach ($data as $k => $v){
            switch ($v['P_Status']){
                case '1';
                    $data[$k]['SName'] = '案件创建成功';
                    break;
                case '2';
                    $data[$k]['SName'] = '案件取消';
                    break;
                case '3';
                    $data[$k]['SName'] = '案件删除';
                    break;
            } 
            switch ($v['P_ApplyStatus']){
                case '1';
                    $data[$k]['ASName'] = '待审核';
                    break;
                case '2';
                    $data[$k]['ASName'] = '审核通过';
                    break;
                case '3';
                    $data[$k]['ASName'] = '审核不通过';
                    break;
            }
        }
        $this->assign('playlist', $data);
        $this->assign('endTime', $_SESSION[searchEndTime]);
        $this->assign('startTime', $_SESSION[searchStartTime]);
        $this->display('searchPlaylist');
    }

    //申请统计页   
    public function collectPlaylist() {
        $_SESSION['collectStart'] = htmlspecialchars($_POST[startDay]);
        $_SESSION['collectEnd'] = htmlspecialchars($_POST[endDay]);
        $_SESSION['collectReason'] = htmlspecialchars($_POST[reason]);
        $_SESSION['courtNum'] = htmlspecialchars($_POST[court]);
        $courtID = $_SESSION["M_Court"]; //所属检察院ID
        $court_p[0] = htmlspecialchars($_POST['court']);

        if (htmlspecialchars($_POST[startDay]) == null || htmlspecialchars($_POST[endDay]) == null || htmlspecialchars($_POST[court]) == null) {
            $CourtModel = new CourtModel();
            $court_array = $CourtModel->getAllChildren($courtID); //所有的子检察院		   
            array_push($court_array, $courtID); //合并子检察院和自检察院	
            if ($court_array == null)
                $court_array[0] = $courtID;
            $courtStr = implode(',', $court_array);
            $courtName = M('court')->field('C_ID,C_Name')->where(array('C_ID' => array('in', $courtStr)))->select();
//            var_dump($courtName);exit;
            $this->assign("courtName", $courtName);
            $this->display('collectPlaylist');
            exit;
        }else {
            $CourtModel = new CourtModel();
            $court_array = $CourtModel->getAllChildren(htmlspecialchars($_POST[court])); //所有的子检察院
            $court_array = array_merge($court_p, $court_array); //合并子检察院和自检察院
            if ($court_array == null)
                $court_array[0] = htmlspecialchars($_POST[court]);
        }
        //查询各检察院申请数
        $Playlist = M('playlist');
        $Court = M("court");
        $res = array();
//        var_dump($res);exit;
        foreach ($court_array as $k => $v) {
//            var_dump($v);
            $court = $Court->where("C_ID='$v'")->find();
            $startDay = htmlspecialchars($_POST[startDay]);
            $endDay = htmlspecialchars($_POST[endDay]);
            if ($_SESSION['collectReason']) {
//                var_dump(111);
                $res[$k]['total'] = $res[$k]['success'] = 0;
                $collectReason = $_SESSION['collectReason'];
                $res[$k]['failed'] = $Playlist->where("P_StartTime>='$startDay' AND P_EndTime<='$endDay' AND P_RequestCourt='$v' AND P_IfSuccess=0 AND P_FailedReason LIKE '%$collectReason%' ")->count();
            } else {

                $res[$k]['total'] = $Playlist->where("P_StartTime>='$startDay' AND P_EndTime<='$endDay' AND P_CourtIn='$v' and P_REID is  not  null")->count();
                $res[$k]['success'] = $Playlist->where("P_StartTime>='$startDay' AND P_EndTime<='$endDay' AND P_CourtIn='$v' AND P_Result=1")->count();
                $res[$k]['failed'] = $Playlist->where("P_StartTime>='$startDay' AND P_EndTime<='$endDay' AND P_CourtIn='$v' AND P_Result=0")->count();
            }
            $res[$k]['startTime'] = $startDay;
            $res[$k]['endTime'] = $endDay;
            $res[$k]['courtName'] = $court[C_Name];
            $res[$k]['courtID'] = $v;
        }
        //查询检察院等级
        /* 		$Court=M("court");
          $res=$Court->where("C_ID=$courtID")->find();
          //当检察院等级为中级检察院时，查询申请数量
          if($res[CL_Name]!=4){
          $Playlist=M("playlist");
          $sql="SELECT *,(SELECT L_Comment FROM t_live WHERE p.P_OutPID=t_live.L_PID) as L_Comment,
          (SELECT C_Name FROM t_court WHERE p.P_CourtIn=t_court.C_ID) as P_CourtIn,
          (SELECT C_Name FROM t_court WHERE p.P_CourtOut=t_court.C_ID) as P_CourtOut
          FROM t_playlist as p WHERE p.P_StartTime>='$_POST[startDay]' and p.P_EndTime<='$_POST[endDay]' and p.P_RequestCourt in($courts)";
          $playlist=$Playlist->query($sql);
          $this->assign("playlist",$playlist);
          $this->assign("startDay",$_POST[startDay]);
          $this->assign("endDay",$_POST[endDay]);
          $this->assign("total",count($playlist));
          } */
        $CourtModel2 = new CourtModel();
        $court_array = $CourtModel2->getAllChildren($courtID); //所有的子检察院		   
        array_push($court_array, $courtID); //合并子检察院和自检察院	
        if ($court_array == null)
            $court_array[0] = $courtID;
        $courtStr = implode(',', $court_array);
        $courtName = M('court')->field('C_ID,C_Name')->where(array('C_ID' => array('in', $courtStr)))->select();

//        var_dump($res);exit;
        $this->assign("courtName", $courtName);
        $this->assign("courtNum", htmlspecialchars($_POST[court]));
        $this->assign("startDay", $_SESSION['collectStart']);
        $this->assign("endDay", $_SESSION['collectEnd']);
        $this->assign("selectedReason", $_SESSION['collectReason']);
        $this->assign("res", $res);
        $this->display('collectPlaylist');
    }

    /**
      +--------------------------------
     * 统计详情页
      +--------------------------------
     * @date: 2017年1月5日 下午3:41:37
     * @author: Str
     * @param: variable
     * @return:
     */
    public function checkCollection() {
        $Playlist = M('playlist');
        $collectReason = $_SESSION['collectReason'];
        $startTime = htmlspecialchars($_GET[startTime]);
        $endTime = htmlspecialchars($_GET[endTime]);
        $courtID = htmlspecialchars($_GET[courtID]);
        $name = htmlspecialchars($_GET[name]);
        switch (htmlspecialchars($_GET[type])) {
            case 'total':
                $sql = "SELECT *,(P_OutPID/10)-9 as channel,(SELECT C_Name FROM t_court WHERE C_ID=p.P_CourtIn) as courtIn,
            (SELECT CR_Name FROM t_courtroom WHERE CR_ID=p.P_CourtRoomIn) as courtRoom,
            (SELECT C_Name FROM t_court WHERE C_ID=p.P_RequestCourt) as requestCourt,
            (SELECT M_Name FROM t_user WHERE M_ID=p.P_RequestMan) as requestMan
            FROM t_playlist as p WHERE p.P_StartTime>='$startTime' AND p.P_EndTime<='$endTime' AND P_CourtIn='$courtID' ";
                $this->assign("title", "【" . $name . "】直播总数明细表");
                break;
            case 'success':
                $sql = "SELECT *,(P_OutPID/10)-9 as channel,(SELECT C_Name FROM t_court WHERE C_ID=p.P_CourtIn) as courtIn,
            (SELECT CR_Name FROM t_courtroom WHERE CR_ID=p.P_CourtRoomIn) as courtRoom,
            (SELECT C_Name FROM t_court WHERE C_ID=p.P_RequestCourt) as requestCourt,
            (SELECT M_Name FROM t_user WHERE M_ID=p.P_RequestMan) as requestMan
            FROM t_playlist as p WHERE p.P_StartTime>='$startTime' AND p.P_EndTime<='$endTime' AND P_Result=1 AND P_CourtIn='$courtID'";
                $this->assign("title", "【" . $name . "】直播成功明细表");
                break;
            case 'failed':
                $sql = "SELECT *,(P_OutPID/10)-9 as channel,(SELECT C_Name FROM t_court WHERE C_ID=p.P_CourtIn) as courtIn,
            (SELECT CR_Name FROM t_courtroom WHERE CR_ID=p.P_CourtRoomIn) as courtRoom,
            (SELECT C_Name FROM t_court WHERE C_ID=p.P_RequestCourt) as requestCourt,
            (SELECT M_Name FROM t_user WHERE M_ID=p.P_RequestMan) as requestMan
            FROM t_playlist as p WHERE p.P_StartTime>='$startTime' AND p.P_EndTime<='$endTime' AND P_Result=0 AND P_CourtIn='$courtID'";
                if ($collectReason) {
                    $sql .="AND P_Fail LIKE '%$collectReason%' ";
                }
                $this->assign("title", "【" . $name . "】直播失败明细表");
        }
//        var_dump($sql);exit;
        $playlist = $Playlist->query($sql);
        foreach ($playlist as $k => $v) {
            if ($v['P_Result'] == 1) {
                $playlist[$k]['effect'] = '成功';
            } elseif ($v['P_Result'] === '0') {
                $playlist[$k]['effect'] = '失败';
            } else {
                $playlist[$k]['effect'] = '未记录';
            }
        }
        $this->assign('playlist', $playlist);
        $this->display('checkCollection');
    }

    //根据解码器号码
    //判断解码服务器地址
    //return string
    public function getStreamServer($Decoder) {
        if ($Decoder == 1) {
            return C("STREAM_SERVER_1");
        } else if ($Decoder == 2) {
            return C("STREAM_SERVER_2");
        } else if ($Decoder == 3) {
            return C("STREAM_SERVER_3");
        } else if ($Decoder == 4) {
            return C("STREAM_SERVER_4");
        } else if ($Decoder == 5) {
            return C("STREAM_SERVER_5");
        }
    }

    function object_to_array($obj) {
        $obj = (array) $obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array) object_to_array($v);
            }
        }

        return $obj;
    }

    //http://139.1.1.87/index.php/Public/start?ftdm=1277
    public function start() {
        $Playlist = M('playlist');
        $ftdm = htmlspecialchars($_GET['ftdm']);
        $nowTime = date("Y-m-d H:i:s", time());
        $res = $this->getNowList();
        $res[0]['hytType'] = 1;
        if (empty($res)) {
            saveLog('浮层start无直播', $ftdm . '+++' . $nowTime);
            echo '您所在的听证室没有直播';
        } elseif ($res[0]['P_IsAuto'] == 2) {
            $hytres = $this->HYThandler($res);
            $hytarr = $this->object_to_array($hytres);
            if ($hytarr[result] == 0) {
                $data['P_LiveStatus'] = 2; //开始直播
                $sql = $Playlist->where("P_ID=" . $res[0]['P_ID'])->save($data);
                saveLog("浮层start成功", $ftdm . '+++' . $nowTime);
                echo '开庭外网操作成功！';
            } else {
                saveLog("浮层start失败", $hytres->errorDescription);
                echo '开庭外网操作失败！' . $hytres->errorDescription;
            }
        } elseif ($res[0][$res[0]['P_IsAuto'] == 1]) {
            saveLog('浮层start开庭方式错误', $ftdm . '+++' . $nowTime);
            echo '此次直播为自动开庭 请切换开庭方式';
        }
    }

    //http://139.1.1.87/index.php/Public/stopLive?ftdm=1277
    public function stopLive() {
        $Playlist = M('playlist');
        $ftdm = htmlspecialchars($_GET['ftdm']);
        $nowTime = date("Y-m-d H:i:s", time());
        $res = $this->getNowList();
        $res[0]['hytType'] = 2;
        $res[0]['tyyType'] = 'stopLive';
        if (empty($res)) {
            saveLog('浮层stopLive无直播', $ftdm . '+++' . $nowTime);
            echo '您所在的听证室没有直播';
        } else {
            $tyyres = $this->updataTYY($res);
            if ($tyyres['code'] == '0') {
                $hytres = $this->HYThandler($res); //指令接口调用
                $hytarr = $this->object_to_array($hytres);
                if ($hytarr->result == 0) {
                    $data['P_LiveStatus'] = 3; //开始直播
                    $sql = $Playlist->where("P_ID=" . $res[0]['P_ID'])->save($data);
                    saveLog("浮层stopLive", $ftdm . '+++' . $nowTime);
                    echo '休庭操作成功！';
                } else {
                    saveLog("浮层stopLive失败", $hytres->errorDescription);
                    echo '休庭外网操作失败！' . $hytres->errorDescription;
                }
            } else {
                saveLog("浮层stopLive失败", $tyyres['msg']);
                echo '休庭云平台操作失败！' . $tyyres['msg'];
            }
        }
    }

    //http://139.1.1.87/index.php/Public/resumeLive?ftdm=1277
    public function resumeLive() {
        $Playlist = M('playlist');
        $ftdm = htmlspecialchars($_GET['ftdm']);
        $nowTime = date("Y-m-d H:i:s", time());
        $res = $this->getNowList();
        $res[0]['hytType'] = 3;
        $res[0]['tyyType'] = 'resumeLive';
        if (empty($res)) {
            saveLog('浮层resumeLive无直播', $ftdm . '+++' . $nowTime);
            echo '您所在的听证室没有直播';
        } else {
            $tyyres = $this->updataTYY($res);
            if ($tyyres['code'] == '0') {
                $hytres = $this->HYThandler($res); //指令接口调用
                $hytarr = $this->object_to_array($hytres);
                if ($hytres->result == 0) {
                    $data['P_LiveStatus'] = 2; //开始直播
                    $sql = $Playlist->where("P_ID=" . $res[0]['P_ID'])->save($data);
                    saveLog("浮层resumeLive", $ftdm . '+++' . $nowTime);
                    echo '再开庭操作成功！';
                } else {
                    saveLog("浮层resumeLive失败", $hytres->errorDescription);
                    echo '再开庭外网操作失败！' . $hytres->errorDescription;
                }
            } else {
                saveLog("浮层resumeLive失败", $tyyres['msg']);
                echo '再开庭云平台操作失败！' . $tyyres['msg'];
            }
        }
    }

    //139.1.1.87/index.php/Public/endLive?ftdm=1277&nowTime=1516170492
    public function endLive() {
        $Playlist = M('playlist');
        $ftdm = htmlspecialchars($_GET['ftdm']);
        $nowTime = date("Y-m-d H:i:s", time());
        $res = $this->getNowList();
        $this->AutoLive($res);
        if (empty($res)) {
            saveLog('浮层endLive无直播', $ftdm . '+++' . $nowTime);
            echo '您所在的听证室没有直播';
        } else {
            $res[0]['hytType'] = 4;
            $res[0]['tyyType'] = 'cutLive';
            $zkysres = $this->initChannel($res[0]['P_OutPID']);

            if ($zkysres == 'ok') {
                $tyyres = $this->updataTYY($res);
                if ($tyyres['code'] == '0') {
                    $hytres = $this->HYThandler($res); //指令接口调用
                    if ($hytres->result == 0) {
                        $data['P_LiveStatus'] = 4; //开始直播
                        $sql = $Playlist->where("P_ID=" . $res[0]['P_ID'])->save($data);
                        saveLog("浮层endLive", $ftdm . '+++' . $nowTime);
                        echo '结束操作成功！';
                    } else {
                        saveLog("浮层endLive失败", $hytres->errorDescription);
                        echo '结束外网操作失败！' . $hytres->errorDescription;
                    }
                } else {
                    saveLog("浮层endLive失败", $tyyres['msg']);
                    echo '结束云平台操作失败！' . $tyyres['msg'];
                }
            } else {
                echo '结束通道操作失败！';
            }
        }
    }

    //http://139.1.1.87/index.php/Public/auto?ftdm=1277
    public function auto() {
        $ftdm = htmlspecialchars($_GET['ftdm']);
        $nowTime = date("Y-m-d H:i:s", time());
        $res = $this->getNowList();
        $P_res = $res[0];
        $minus = strtotime("$P_res[P_StartTime] - $P_res[P_ASTime] min") - time();
        $Playlist = M('playlist');

        if (!empty($P_res)) {
            if ($minus > 20) {
                if (htmlspecialchars($_GET['type']) == 1) {
                    $data['P_IsAuto'] = 2;
                } elseif (htmlspecialchars($_GET['type']) == 2) {
                    $data['P_IsAuto'] = 1;
                }
                $res = $Playlist->where("P_ID=$P_res[P_ID]")->save($data);
                saveLog("切换手自动开闭厅" . htmlspecialchars($_GET['type']), $_REQUEST[pid]);
                if (htmlspecialchars($_GET['type']) == 2) {
                    echo"自动开庭设置操作成功！";
                } else {
                    $h_res = $this->AutoEndTime($P_res);
                }
            } else {
                if (htmlspecialchars($_GET['type']) == 2) {
                    echo "该场直播为手动开庭请点击开庭按钮！";
                } elseif (htmlspecialchars($_GET['type']) == 1) {
                    echo "该场直播已经自动切流 无法手动操作！";
                }
                exit;
            }
        } else {
            echo '所在听证室当前没有直播！';
        }
    }

    //http://139.1.1.87/index.php/Public/delay?ftdm=1277&nowTime=1516170492&delayTime=30
    public function delay() {
        $ftdm = htmlspecialchars($_GET['ftdm']);
        $nowTime = date("Y-m-d H:i:s", time());
        $Playlist = M('playlist');
        $playlist = $this->getNowList();
        $nullCourt = $this->getNullCourt($playlist);
        if (empty($nullCourt)) {
            echo("所有通道已满无法延迟闭庭！");
            exit;
        } else {
            $outId = $playlist['P_OutPID'];
            $is_use = $Playlist->where("P_OutPID=$outId and P_StartTime> now() and DATEDIFF(P_StartTime,NOW())=0")
                    ->select();
            if (empty($is_use)) {
                $this->delayTime($playlist);
            } else {
                $data_pid = array();
                foreach ($is_use as $key => $value) {
                    $nullCourt = $this->getNullCourt($playlist);
                    $data_pid['P_OutPID'] = $nullCourt['L_PID'];
                    $data_pid['P_CourtOut'] = $nullCourt['L_CourtName'];
                    $value['P_OutPID'] = substr($nullCourt['L_Comment'], 6);
                    $res = $Playlist->where("P_ID=$value[P_ID]")->save($data_pid);
                    $hyt_res = $this->updateHYT('update', $value); //改通道
                    $cre = json_decode($hyt_res, true);
                    if ($cre['respCode'] == '000') {
                        $h_res = $this->delayTime($playlist);
                    } else {
                        echo("延迟直播失败！");
                        exit;
                    }
                }
            }
        }
    }

    //http://139.1.1.87/index.php/Public/getInfo?ftdm=1277
    public function getInfo() {
        $ftdm = htmlspecialchars($_GET['ftdm']);
        $Playlist = M('playlist');
        $sql = "SELECT * FROM `t_playlist` "
                . "LEFT JOIN t_live on t_playlist.P_OutPID=t_live.L_PID "
                . "LEFT JOIN t_courtroom on t_playlist.P_CourtRoomIn=t_courtroom.CR_ID "
                . "WHERE t_playlist.P_Status=2 and P_CourtRoomIn='$ftdm' and P_EndTime > now()"
                . " order by P_StartTime ASC";
        $sqldata = $Playlist->query($sql);
        $info = '案件名称：' . $sqldata[0]['P_CaseName'] . '；开始结束时间：' . $sqldata[0]['P_StartTime'] . '->' . $sqldata[0]['P_EndTime'] . '；直播地址：' . $sqldata[0]['CR_URL'];

        if (empty($sqldata[0])) {
            echo '该时段此听证室无直播！';
        } else {
            print_r($info);
        }
    }

    //http://139.1.1.87/index.php/Public/OpenLive?ftdm=1277
    public function OpenLive() {
        $ftdm = htmlspecialchars($_GET['ftdm']);
        $Playlist = M('playlist');
        $nowTime = date("Y-m-d H:i:s", time());
        $res = $this->getNowList();
        $P_res = $res[0];
        $_REQUEST['type'] = 'resumeLive1';
        $_REQUEST['pid'] = $P_res[0]['P_OutPID'];
        $zkysres = $this->updateZKYS($P_res);
        $tyyres = $this->updataTYY($P_res);
        if ($zkysres == 'ok' && $tyyres['code'] == '0') {
            //鸿仪通接口调用
            $time = date('YmdHis') . rand(0, 9) . rand(0, 9) . rand(0, 9);
            $code = md5(C('hytPassCODE') . $time);
            $c_code = $P_res[0]['P_REID'];
            $xmldata = '<?xml version="1.0" encoding="UTF-8"?>
                        <action>
                            <params>
                                <code>' . $code . '</code>
                                <timestamp>' . $time . '</timestamp>
                                <liveId>' . $c_code . '</liveId>
                                <action>1</action>
                            </params>
                        </action>';
            $contentLength = strlen($xmldata);
            //        //初始一个curl会话
            $curl = curl_init();
            $header[] = "Content-Type: text/xml";
            $header[] = "Content-length: " . $contentLength;
            //
            //        //设置url
            curl_setopt($curl, CURLOPT_URL, C('hytLIVE'));
            //        //设置发送方式：
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            //        //设置发送数据
            curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);
            //抓取URL并把它传递给浏览器
            $response = curl_exec($curl);
            saveLog('开启通道hyt', $response);
            $xmlResult = simplexml_load_string($response);
            if ($xmlResult['respCode'] == '000') {
                $this->success("通道开启成功！");
            } else {
                $this->error("通道开启失败！外网平台失败！");
            }
        } elseif ($zkysres == 'ok' && $tyyres['code'] != '0') {
            $this->error("通道开启失败！云平台操作失败！");
        } elseif ($zkysres != 'ok' && $tyyres['code'] == '0') {
            $this->error("通道开启失败！通道操作失败！");
        } elseif ($zkysres != 'ok' && $tyyres['code'] != '0') {
            $this->error("通道开启失败！");
        }
    }

    //延迟时间操作细化
    public function delayTime($playlist) {
        $Playlist = M('playlist');
        $playlist = $playlist[0];
        $data['P_EndTime'] = date('Y-m-d H:i:s', strtotime("$playlist[P_EndTime] + $_REQUEST[delayTime] min"));
        $data['P_REID'] = $playlist['P_REID'];
        $res = $Playlist->where("P_ID=$playlist[P_ID]")->save($data);
        if ($res) {
            $h_res = $this->updateHYT('update', $data); //改时间
            $cre = json_decode($h_res, true);
            if ($cre['respCode'] == '000') {
                saveLog("浮层延迟任务", $playlist['P_ID'] . '+++' . $playlist['P_CaseName'] . '+++' . $_REQUEST[delayTime] . '分钟');
                echo("延迟任务成功" . htmlspecialchars($_GET['delayTime']) . '分钟');
                exit;
            } else {
                saveLog("浮层外网延迟任务成功失败", $playlist['P_ID'] . '+++' . $playlist['P_CaseName'] . '+++' . $_REQUEST[delayTime] . '分钟'); //写入日志
                echo('外网延迟任务成功失败！');
                exit;
            }
        }
    }

    //根据ftdm获得当前直播
    public function getNowList() {
        $ftdm = htmlspecialchars($_GET['ftdm']);
        $nowTime = date("Y-m-d H:i:s", time());
        $Playlist = M('playlist');
        $sql = "SELECT * FROM `t_playlist` "
                . "LEFT JOIN t_live on t_playlist.P_OutPID=t_live.L_PID "
                . "WHERE t_playlist.P_Status=2 and DATE_ADD(t_playlist.`P_EndTime`,INTERVAL +t_playlist.`P_METime` MINUTE)>= '$nowTime' "
                . "and '$nowTime' >=DATE_ADD(t_playlist.`P_StartTime`,INTERVAL -t_playlist.`P_MSTime` MINUTE) "
                . "and P_CourtRoomIn=$ftdm";
//        var_dump($sql);exit;
        return ($Playlist->query($sql));
    }

    //操作云平台
    public function updataTYY($res) {
        $pid = $res[0]['P_OutPID'];
        $name = $res[0]['tyyType'];
        $url = C('CLERK');
        $url.= $name;
        $params = array(
            'sourceCode' => 'ahfy',
            'pid' => $pid,
        );
        $response = curl($url, $params, 'GET');
        $resuce = json_decode($response, TRUE);
        return $resuce;
    }

    //鸿仪通指令接口
    public function HYThandler($type) {
        $time = date('YmdHis') . rand(0, 9) . rand(0, 9) . rand(0, 9);
        $code = md5(C('hytCODE') . $time);
        $c_code = $type[0]['P_REID'];
        $comment = substr($type[0]['L_Comment'], 6);
        $action = $type[0]['hytType']; //换操作代码

        $xmldata = '<?xml version="1.0" encoding="UTF-8"?>
                    <action>
                        <params>
                            <code>' . $code . '</code>
                            <timestamp>' . $time . '</timestamp>
                            <liveId>' . $c_code . '</liveId>
                            <codesecret>' . $comment . '</codesecret>
                            <action>' . $action . '</action>
                        </params>
                    </action>';

        $contentLength = strlen($xmldata);
//        //初始一个curl会话
        $curl = curl_init();
        $header[] = "Content-Type: text/xml";
        $header[] = "Content-length: " . $contentLength;
//
//        //设置url
        curl_setopt($curl, CURLOPT_URL, C('hytCLERK'));
//        //设置发送方式：
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//
//        //设置发送数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);
        //抓取URL并把它传递给浏览器
        $response = curl_exec($curl);
        $xmlResult = simplexml_load_string($response);
        saveLog("书记员hyt接口操作1浮层", 'request---' . $xmldata);
        saveLog("书记员hyt接口操作2浮层", 'response---' . $response);
        return $xmlResult;
    }

    //鸿仪通更新直播
    public function updateHYT($type, $res) {
        $url = C('hytDATA') . $type;
        $time = date('YmdHis') . rand(0, 9) . rand(0, 9) . rand(0, 9);
        $code = md5(C('hytDATACODE') . $time);
        $params = array(
            'code' => $code,
            'timestamp' => $time,
            'id' => $res['P_REID'],
            'startTime' => date('Y/m/d H:i:s', strtotime($res['P_StartTime'])),
            'endTime' => date('Y/m/d H:i:s', strtotime($res['P_EndTime'])),
            'liveChannelId' => $res[P_OutPID],
        );
        if (empty($res['P_StartTime'])) {
            $params['startTime'] = NULL;
        }
        if (empty($res['P_EndTime'])) {
            $params['endTime'] = NULL;
        }
//                var_dump($params);exit;
        $jsonStr = json_encode($params);
        $response = http_post_json($url, $jsonStr);
        return $response;
    }

    //解码器更新通道输入源 中科云视
    public function updateZKYS($param) {
        $param = $param[0];
        $Courtroom = M("courtroom");
        $courtroom = $Courtroom->where("CR_ID='$param[P_CourtRoomIn]' ")->find();

        $url = $courtroom['CR_URL'];
        $port = $courtroom['CR_Port'];
        $url_array = explode('/', $url); //拆分入流地址到数组

        if ($_REQUEST['type'] == 'cutLive1') {
            $url_array[0] = 'rtsp:';
        }
        $xmldata = '<?xml version="1.0" encoding="utf-8"?>
            <message module="MULTIPLEXER" version="1.0">
            <header action="REQUEST" command="UPDATE_INPUT" />
            <body>
            <channel>
            <id>' . $param[L_Channel] . '</id>
            <input>
             <url>' . $url_array[0] . "//" . $url_array[2] . ":" . $port . "/" . $url_array[3] . '</url>
            <pid>' . $param[L_PID] . '</pid>
            </input>
            </channel>
            </body>
            </message>';
//var_dump($xmldata);exit;
        $contentLength = strlen($xmldata);
        //初始一个curl会话
        $curl = curl_init();
        $header[] = "Content-Type: text/xml";
        $header[] = "Content-length: " . $contentLength;
        //设置url
        curl_setopt($curl, CURLOPT_URL, self::getStreamServer($param[L_Decoder]));
        //设置发送方式：
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //设置发送数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);
        //抓取URL并把它传递给浏览器
        $response = curl_exec($curl);
        $xmlResult = simplexml_load_string($response);
        savelog('zkys通道控制', $response . '</br>' . $xmldata . self::getStreamServer($param[P_Decoder]));
        //关闭cURL资源，并且释放系统资源
        curl_close($curl);
        if ($xmlResult->body->channel->status == 'ok') {
            return "ok";
        } else {
            return "failed";
        }
    }

    //手动开闭厅自动延长结束时间
    public function AutoEndTime($p_res) {
        $Playlist = M('playlist');
        $is_use = $Playlist->where("P_OutPID=$p_res[P_OutPID] and P_StartTime> now() and DATEDIFF(P_StartTime,NOW())=0 and P_ID<>$p_res[P_ID] and P_Status=2")->find();
        if (!empty($is_use)) {
            $data['P_EndTime'] = date('Y-m-d H:i:s', strtotime("$is_use[P_StartTime] - $is_use[P_STime] min - 5 min")); //下一场此通道直播开始前5分钟
            $data['P_REID'] = $p_res['P_REID'];
            $h_res = $this->updateHYT('update', $data); //更新鸿仪通信息
            if ($h_res->result == 0) {
                $Playlist->where("P_REID=$data[P_REID]")->save($data);
                echo"手动开庭成功操作成功！并将结束时间延长！";
            } else {
                echo"手动开庭成功操作成功！请点击开庭按钮！";
            }
        } else {
            echo"手动开庭成功操作成功！请点击开庭按钮！";
        }
    }

    //获取空出流通道
    public function getNullCourt($param) {
        $Court = new CourtModel();
        $live = $Court->getOutputCourt();
        shuffle($live); //随机排序出流数组
        $r_etime = date('Y-m-d H:i:s', strtotime("$param[P_EndTime] + $_REQUEST[delay] min"));
        $Playlist = M('playlist');
        for ($i = 0; $i < count($live); $i++) {
            $livePID = $live[$i]['L_PID'];
            $is_use = $Playlist
                    ->where("((P_StartTime<='$param[P_EndTime]' and P_EndTime>='$param[P_EndTime]') "
                            . "or (P_StartTime>='$param[P_EndTime]' and P_StartTime<='$r_etime')) "
                            . "and DATEDIFF(P_StartTime,NOW())=0 "
                            . "and (P_OutPID=$livePID)")
                    ->find();
            if (empty($is_use)) {
                return $live[$i];
                break;
            }
        }
    }

    //自动录制 
    public function AutoLive($P_res) {
        if (empty($P_res)) {
            $P_res[0]['P_CaseName'] = htmlspecialchars($_GET['recordName']);
            $P_res[0]['P_REID'] = htmlspecialchars($_GET['reId']);
            $P_res[0]['P_StartTime'] = htmlspecialchars($_GET['recordStartTime']);
            $P_res[0]['P_EndTime'] = htmlspecialchars($_GET['recordEndTime']);
            $P_res[0]['P_OutPID'] = htmlspecialchars($_GET['pid']);
            saveLog("脚本录制", $P_res);
        }
        $a = 0;
        foreach ($P_res as $key => $info) {
            $dataPidList[$info['P_OutPID']][] = $info;
        }
        foreach ($dataPidList as $k => $value) {
            for ($i = 0; $i < count($value); $i++) {
                $pListAll[$a]['pid'] = $k;
                $pListAll[$a]['recordList'][$i]['recordName'] = $value[$i]['P_CaseName'];
                $pListAll[$a]['recordList'][$i]['reId'] = $value[$i]['P_REID'];
                $pListAll[$a]['recordList'][$i]['recordStartTime'] = $value[$i]['P_StartTime'];
                $pListAll[$a]['recordList'][$i]['recordEndTime'] = $value[$i]['P_EndTime'];
            }
            $a++;
        }

        $endList['creater'] = 'courtAdmin';
        $endList['sourceCode'] = 'fy';
        $endList['pidList'] = $pListAll;
        $json_List = json_encode($endList);
        $url = C('TYCLERK') . 'creat';
        $response = http_post_json($url, $json_List);
        saveLog("节目自动录制", $response);
        return $response;
    }

    public function AutoLog() {
        $pid = htmlspecialchars($_GET['P_ID']);
        $info = htmlspecialchars($_GET['info']);
        saveLog("$info", '自动脚本++++' . $pid);
    }

    public function AutoLive1() {
        $P_StartTime = htmlspecialchars($_GET['stime']);
        $P_EndTime1 = htmlspecialchars($_GET['etime1']);
        $P_EndTime2 = htmlspecialchars($_GET['etime2']);
        $Playlist = M('playlist');
        $res = $Playlist
                ->where("P_StartTime>'$P_StartTime' "
                        . "and P_EndTime > '$P_EndTime1' "
                        . "and P_EndTime < '$P_EndTime2' ")
                ->select();
        $dataPidList = array();
        $pListAll = array();
        $a = 0;
        foreach ($res as $key => $info) {
            $dataPidList[$info['P_OutPID']][] = $info;
        }
        foreach ($dataPidList as $k => $value) {
            for ($i = 0; $i < count($value); $i++) {
                $pListAll[$a]['pid'] = $k;
                $pListAll[$a]['recordList'][$i]['recordName'] = $value[$i]['P_CaseName'];
                $pListAll[$a]['recordList'][$i]['recordStartTime'] = $value[$i]['P_StartTime'];
                $pListAll[$a]['recordList'][$i]['recordEndTime'] = $value[$i]['P_EndTime'];
            }
            $a++;
        }
        $endList['creater'] = 'courtAdmin';
        $endList['sourceCode'] = 'fy';
        $endList['pidList'] = $pListAll;
        $json_List = json_encode($endList);
        var_dump($json_List);
        exit;
        saveLog("超时录制", $json_List);
        $url = C('TYCLERK') . 'creat';
//       $response = http_post_json($url, $json_List);
        saveLog("节目自动录制", $response);
        echo $response;
    }

}

?>