<?php

class PlaylistModel extends Model {
	protected $name = 'playlist';
	protected $_validate         =         array(
			array('P_StartTime','require','开始时间必须填写！'),
			array('P_EndTime','require','结束时间必须填写！'),
			array('P_CourtIn','require','检察院必须填写！'),
			array('P_CourtRoomIn','require','听证室必须填写！'),
                        array('P_CaseCode','','案件已经存在！',1,'unique'),
			array('P_StartTime','checkTime','开始时间不能小于当前时间！',1,'callback'),
			array('P_StartTime','checkTime1','开始时间必须小于结束时间！间隔时间不能小于5分钟！',1,'callback'),
			array('P_StartTime','checkTime2','工作时间为7:00-19:00,并且直播为同一天，请查询数据！',1,'callback'),
			array('P_CourtIn','checkcourt','检察院必须填写！',1,'callback'),			
                        array('P_CourtRoomIn','checkroom','听证室必须填写！',1,'callback'),


    );

    //检查开始时间是否小于当前时间
    public function checkTime() {
        if (($_POST['P_StartTime'] == '' || $_POST['P_StartTime'] == '')) {
            return true;
        } else {
            //开始时间是否大于当前时间
            if (strtotime($_POST['P_StartTime']) <= time()) {
                return false;
            }
        }
        return true;
    }

    //检查开始时间是否小于结束时间
    public function checkTime1() {
        //开始时间大于等于结束时间
        if (($_POST['P_StartTime'] == '' || $_POST['P_StartTime'] == '')) {
            saveLog('案件录入成功1', json_encode($_POST));
            return true;
        } else {
            if (strtotime($_POST['P_StartTime']) >= strtotime($_POST['P_EndTime'])) {

                saveLog('案件录入失败1-1', json_encode($_POST));
                return false;
            }
            if ((strtotime($_POST['P_EndTime']) - strtotime($_POST['P_StartTime'])) < 300) {

                saveLog('案件录入失败1-2', json_encode($_POST));
                return false;
            }
        }
    }

    //检查开始时间是否在工作时间
    public function checkTime2() {
        //开始时间大于等于结束时间
        if (($_POST['P_StartTime'] == '' || $_POST['P_EndTime'] == '')) {
            saveLog('案件录入成功2', json_encode($_POST));
            return true;
        } else {

            $times = explode(" ", $_POST['P_StartTime']);
            $times1 = explode(":", $times[1]);
            $timee = explode(" ", $_POST['P_EndTime']);
            $timee2 = explode(":", $timee[1]);
            if ($times1[0] <= "7") {
                saveLog('案件录入失败2-1', json_encode($_POST));
                return false;
            }
            if ($timee2[0] >= "19") {
                saveLog('案件录入失败2-2', json_encode($_POST));
                return false;
            }
            if (strtotime($times[0]) != strtotime($timee[0])) {
                saveLog('案件录入失败2-3', json_encode($_POST));
                return false;
            }
        }
    }


    public function checkcourt() {
        if(empty($_REQUEST['P_CourtIn'])){
            return false;
        }  else {
            return true;
        }
    }
    
    public function checkroom() {
        if(empty($_REQUEST['P_CourtRoomIn'])){
            return false;
        }  else {
            return true;
        }
    }

}
