<?php

class LiveAction extends CommonAction {
    /*     * **********************************************************************书记员*********************************************************************************** */

    //书记员首页
    public function monitor() {
        $Playlist = M('playlist');
        $time = date("Y-m-d H:i:s");
        if ($_SESSION['administrator']) {
            $sql = "SELECT * FROM `t_playlist` "
                    . "WHERE t_playlist.P_Status=1 and t_playlist.P_ApplyStatus=2 and to_days(t_playlist.`P_StartTime`) = to_days(now())";
            $res = $Playlist->query($sql);
        } else {
            $sql = "SELECT * FROM `t_playlist` "
                    . "WHERE t_playlist.P_Status=1 and t_playlist.P_ApplyStatus=2 and to_days(t_playlist.`P_StartTime`) = to_days(now()) "
                    . "and P_RequestMan=$_SESSION[M_ID]";
            $res = $Playlist->query($sql);
        }
        if (!empty($res)) {
            foreach ($res as $k => $v) {
                if ($v['P_LiveStatus'] == 1) {
                    $res[$k]['now'] = '未开始';
                } elseif ($v['P_LiveStatus'] == 2) {
                    $res[$k]['now'] = '直播中';
                } elseif ($v['P_LiveStatus'] == 3) {
                    $res[$k]['now'] = '休庭中';
                } elseif ($v['P_LiveStatus'] == 4) {
                    $res[$k]['now'] = '已结束';
                }
            }
        } else {
            $this->assign("error", '您所在的庭院没有案件！');
        }
        $this->assign("list", $res);
        $this->assign("level8", "active open");
        $this->assign("level82", "active open");
        $this->display();
    }

    //书记员操作
    public function M_operate() {
        $Playlist = M('playlist');
        if ($_SESSION['administrator']) {
            $sql = "SELECT * FROM `t_playlist` "
                    . "WHERE t_playlist.P_Status=1 and t_playlist.P_ApplyStatus=2 and to_days(t_playlist.`P_StartTime`) = to_days(now()) and t_playlist.P_ID=" . $_GET['pid'];
        } else {
            $sql = "SELECT * FROM `t_playlist` "
                    . "WHERE t_playlist.P_Status=1 and t_playlist.P_ApplyStatus=2 and to_days(t_playlist.`P_StartTime`) = to_days(now()) and t_playlist.P_ID=" . $_GET['pid']
                    . " and P_RequestMan=$_SESSION[M_ID]";
        }
        $res = $Playlist->query($sql);
        $this->ifLive($res);
        if (empty($res)) {
            $this->error('此案件您没有操作权限！');
        }
        $ymsdata = json_decode($this->ymsRE($res[0]), true);
        if ($ymsdata['code'] == 0) {
            switch ($_GET['type']) {
                case "1":
                    $data['status'] = 5;
                    $zbdata = json_decode($this->zbRE($res[0],$data), true);
                    break;
                case "3":
                    $data['status'] = 7;
                    $zbdata = json_decode($this->zbRE($res[0],$data), true);
                    break;
            }
            if($zbdata['code'] == 0){
                $this->upSQL($res[0]);
            }  else {
                $this->error('zb操作失败');
            }
        } else {
            $this->error('操作失败');
        }
    }

    /*     * ********************************************共用方法********************************************************* */

    //yms操作
    public function ymsRE($res) {
        $time = time() . '000';
        $sign = md5(C('YMSCODE') . C('YMSAPP') . $time);
        $ysmURL = C('YMSURL') . 'liveControl/broadcast';
        if ($_GET['type'] == 11) {
            $type = 1;
        }  else {
            $type = $_GET['type'];
        }
        $params = array(
            'pid' => $res['P_OutPID'],
            'type' => $type,
            'time' => $time,
            'sign' => $sign,
        );
        $jsonStr = json_encode($params);
        $response = http_post_json($ysmURL, $jsonStr);
        saveLog("直播yms操作".$_GET[type], $ysmURL, $jsonStr . 'res=' . $response);
        return $response;
    }

    //zb操作
    public function zbRE($res,$data) {
        $url = C('DELAYURL') . 'livechl/channel/' . $res['P_ChId'];
        $params = array(
            'status' => $data['status'],
        );
        $jsonStr = json_encode($params);
        $response = http_post_json($url, $jsonStr);
        saveLog("直播操作", $url, $jsonStr . 'res=' . $response);
        return $response;
    }

    //操作yms录制
    public function ymsTR($res) {
        $time = time() . '000';
        $sign = md5(C('YMSCODE') . C('YMSAPP') . $time);
        $ysmURL = C('YMSURL') . 'liveControl/record';
        $params = array(
            'pid' => $res['P_OutPID'],
            'name' => $res['P_CaseName'],
            'startTime' => $res['P_RealStartTime'],
            'endTime' => $res['P_RealEndTime'],
            'creater' => $res['P_Contractor'],
            'time' => $time,
            'sign' => $sign,
        );
        $jsonStr = json_encode($params);
        $response = http_post_json($ysmURL, $jsonStr);
        saveLog("直播yms录制操作".$_GET[type], $ysmURL, $jsonStr . 'res=' . $response);
        return json_decode($response, true);
    }

    //修改数据库
    public function upSQL($res) {
        $Playlist = M('playlist');
        $Revice = M('review');
        switch ($_GET[type]) {
            case 1://开始
                $info['P_RealStartTime'] = date('Y-m-d H:i:s', time());
                $info['P_LiveStatus'] = 2;
                $Playlist->where("P_ID= $_GET[pid]")->save($info);
                break;
            case 2://暂停
                $info['P_LiveStatus'] = 3;
                $Playlist->where("P_ID= $_GET[pid]")->save($info);
                break;
            case 3://结束
                $info['P_RealEndTime'] = date('Y-m-d H:i:s', time());
                $info['P_LiveStatus'] = 4;
                $res['P_RealEndTime'] = $info['P_RealEndTime'];
                $Playlist->where("P_ID= $_GET[pid]")->save($info);
                $redata = $this->ymsTR($res);
                if ($redata['code'] != 0) {
                    saveLog("录制操作失败", $_GET[pid]);
                    $this->error("录制操作失败");
                }  else {
                    $data['R_LiveID'] = $res['P_ID'];
                    $data['R_ID'] = $redata['data']['recordId'];
                    $data['R_CreateTime'] = date("Y-m-d H:i:s",time());
                    $Revice->add($data);
                    saveLog("录制操作成功".$res['P_ID'], json_encode($data));
                }
                break;
            case 11://恢复
                $info['P_LiveStatus'] = 2;
                $Playlist->where("P_ID= $_GET[pid]")->save($info);
                break;
        }
        saveLog("操作直播成功".$_GET[type], $_GET[pid]);
        $this->success("操作成功");
    }

    public function ifLive($param) {
        switch ($_GET[type]) {
            case 1://开始
                if ($res[0]['P_LiveStatus'] == 2) {
                    $this->error("直播已开启，请勿重复操作");
                }
                break;
            case 2://暂停
                if ($res[0]['P_LiveStatus'] == 3) {
                    $this->error("直播已暂停，请勿重复操作");
                }
                break;
            case 3://结束
                if ($res[0]['P_LiveStatus'] == 4) {
                    $this->error("直播已结束，请勿重复操作");
                }
                break;
            case 11://恢复
                if ($res[0]['P_LiveStatus'] == 2) {
                    $this->error("直播已开启，请勿重复操作");
                }
                break;
        }
    }

}
