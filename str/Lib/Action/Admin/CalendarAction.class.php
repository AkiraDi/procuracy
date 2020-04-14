<?php

class CalendarAction extends CommonAction {

    //任务周历首页
    public function index() {
        $this->assign("admin", $_SESSION['administrator']);
        $this->assign("level4", "active open");
        $this->display();
    }

    //获取周历内任务
    public function getTasks() {
        $startTime = date("Y-m-d 00:00:00", htmlspecialchars($_GET[start]));
        $endTime = date("Y-m-d 00:00:00", htmlspecialchars($_GET[end]) - 28800);
        //查询下属的检察院ID
        $Court = new CourtModel();
        $courts_array = $Court->getAllChildren($_SESSION['M_Court']);
        $courts_array[] = $_SESSION['M_Court'];
        //数组转字符串
        foreach ($courts_array as $key => $v) {
            if ($key == 0) {
                $courts.=$v;
            } else {
                $courts.="," . $v;
            }
        }
        //按照起始和结束时间查询任务
        $Playlist = M('playlist');
        /* $playlist=$Playlist->field("P_ID as id,P_CaseName as title,P_StartTime as start,P_EndTime as end,C_Name as court,P_Status as className")
          ->where("P_StartTime>'$startTime' and P_EndTime<'$endTime' and (P_Status=1 or P_Status=2) and P_RequestCourt=$_SESSION[M_Court]")
          ->join("t_court on t_playlist.P_CourtIn=t_court.C_ID")
          ->select(); */
        $sql = "SELECT  P_ID as id,P_CaseName as title,P_StartTime as start,P_EndTime as end,
		(SELECT C_Name FROM t_court WHERE C_ID=t_playlist.P_CourtIn) as court,P_Status as className FROM t_playlist
		WHERE str_to_date(P_StartTime,'%Y-%m-%d %H:%i:%s') > str_to_date('$startTime','%Y-%m-%d %H:%i:%s') "
                . "and str_to_date(P_EndTime,'%Y-%m-%d %H:%i:%s') < str_to_date('$endTime','%Y-%m-%d %H:%i:%s') "
                . "and (P_Status=1 or P_Status=2) and P_ApplyStatus = 2 and P_RequestCourt in ($courts)";
        $playlist = $Playlist->query($sql);
        foreach ($playlist as $k => $vo) {
            if ($vo['className'] == 1) {
                $playlist[$k]['className'] = 'label-success';
            } else if ($vo['className'] == 2) {
                $playlist[$k]['className'] = 'label-important';
            } else {
                $playlist[$k]['className'] = 'label-warning';
            }
            $playlist[$k]['title'].="(" . $vo['court'] . ")";
        }
        //	dump($playlist);
        echo json_encode($playlist);
    }

    //查看任务
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

    //导出到excel表格
    public function exportToExcel() {
        require_once './Common/Classes/PHPExcel.php';
        //取出时间段内，用户检察院以及子检察院所有通过审核的申请单
        $Court = new CourtModel();
        $courts_array = $Court->getAllChildren($_SESSION[M_Court]);
        $courts_array[] = $_SESSION[M_Court];
        $P_StartTime = htmlspecialchars($_GET[P_StartTime]);
        $P_EndTime = htmlspecialchars($_GET[P_EndTime]);
        //数组转字符串
        foreach ($courts_array as $key => $v) {
            if ($key == 0) {
                $courts.=$v;
            } else {
                $courts.="," . $v;
            }
        }
        $Playlist = M('playlist');
        $sql = "SELECT * ,(SELECT C_Name FROM t_court WHERE C_ID=t.P_RequestCourt ) as P_RequestCourt, 
		(SELECT M_Name FROM t_user WHERE M_ID=t.P_RequestMan) as P_RequestMan,
                (SELECT M_Phone FROM t_user WHERE M_ID=t.P_RequestMan) as M_Phone,
		(SELECT C_Name FROM t_court WHERE C_ID=t.P_RequestCourt) as P_RequestCourt,
		(SELECT C_Name FROM t_court WHERE C_ID=t.P_CourtIn) as P_CourtIn,
		(SELECT C_Name FROM t_court WHERE C_ID=t.P_CourtOut) as P_CourtOut,
		(SELECT CR_Name FROM t_courtroom WHERE CR_ID=t.P_CourtRoomIn) as CR_Name,
		(SELECT L_Comment FROM t_live WHERE L_PID=t.P_OutPID) as L_Comment,
		(SELECT L_ID FROM t_live WHERE L_PID=t.P_OutPID) as L_ID
		FROM t_playlist t WHERE str_to_date(P_StartTime,'%Y-%m-%d %H:%i:%s') >= str_to_date('$P_StartTime','%Y-%m-%d %H:%i:%s') "
                . "and str_to_date(P_EndTime,'%Y-%m-%d %H:%i:%s') <= str_to_date('$P_EndTime','%Y-%m-%d %H:%i:%s') "
                . "and P_RequestCourt in ($courts) and (P_Status=2 or P_Status=1 or P_Status=3 )";
        $data = $Playlist->query($sql);
//                print_r($sql);exit;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator('庭审直播资源管理服务平台')
                ->setLastModifiedBy('庭审直播资源管理服务平台')
                ->setTitle('Office 2007 XLSX Document')
                ->setSubject('Office 2007 XLSX Document')
                ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
                ->setKeywords('office 2007 openxml php')
                ->setCategory('Result file');
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '检察院名称')
                ->setCellValue('B1', '听证室名称')
                ->setCellValue('C1', '开庭时间')
                ->setCellValue('D1', '结束时间')
                ->setCellValue('E1', '案号')
                ->setCellValue('F1', '案号代码')
                ->setCellValue('G1', '案由')
                ->setCellValue('H1', '承办部门名称')
                ->setCellValue('I1', '直播标题')
                ->setCellValue('J1', '案情简介')
                ->setCellValue('K1', '庭次')
                ->setCellValue('L1', '案件类别')
                ->setCellValue('M1', '审判组织成员')
                ->setCellValue('N1', '诉讼参与方')
                ->setCellValue('O1', '承办人')
                ->setCellValue('P1', '直播通道')
                ->setCellValue('Q1', '直播通道编号')
                ->setCellValue('R1', 'PID')
                ->setCellValue('S1', '直播结果0（失败）1（成功）')
                ->setCellValue('T1', '失败原因')
                ->setCellValue('U1', '备注')
                ->setCellValue('V1', 'id（请勿修改）')
                ->setCellValue('W1', '申请人')
                ->setCellValue('X1', '申请时间')
                ->setCellValue('Y1', '申请检察院')
                ->setCellValue('Z1', '申请人电话');

        $i = 2;
        //20180508 开始时间减少10分钟 结束时间增加5分钟 通道占用时间 
//                $file_path = "./Lib/Action/Admin/11111.txt";
//                if(file_exists($file_path)){
//                    $str = file_get_contents($file_path);
//                    $arr = json_decode($str,TRUE);
//                    $a = $arr['stime'];
//                    $b = $arr['etime'];
//        //        
//                }

        foreach ($data as $k => $v) {
//                    if($v['P_STime']){
//                        $a = $v['P_STime'];
//                        $b = $v['P_ETime'];
//                    }
//                    $steartime = $v['P_StartTime'];
//                    $endtime = $v['P_EndTime'];
//                    $s = date('Y-m-d H:i:s', strtotime("$steartime +$a min"));
//                    $e = date('Y-m-d H:i:s', strtotime("$endtime -$b min"));
//                    $v['P_StartTime'] = $s;
//                    $v['P_EndTime'] = $e;
            if (!empty($v['P_RealEndTime'])) {
                $v['P_EndTime'] = $v['P_RealEndTime'];
            }
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $v['P_CourtIn'])
                    ->setCellValue('B' . $i, $v[P_CourtIn] . $v[CR_Name])
                    ->setCellValue('C' . $i, $v['P_StartTime'])
                    ->setCellValue('D' . $i, $v['P_EndTime'])
                    ->setCellValue('E' . $i, $v['P_CaseName'])
                    ->setCellValue('F' . $i, " " . $v['P_CaseCode'])
                    ->setCellValue('G' . $i, $v['P_CaseDetail'])
                    ->setCellValue('H' . $i, $v['P_Department'])
                    ->setCellValue('I' . $i, $v['P_LiveName'])
                    ->setCellValue('J' . $i, $v['P_CaseDesc'])
                    ->setCellValue('K' . $i, " " . $v['P_CourtNo'])
                    ->setCellValue('L' . $i, $v['P_Catagory'])
                    ->setCellValue('M' . $i, $v['P_JudgeGroup'])
                    ->setCellValue('N' . $i, $v['P_PartyGroup'])
                    ->setCellValue('O' . $i, $v['P_Contractor'])
                    ->setCellValue('P' . $i, " " . substr($v['L_Comment'], 6))
                    ->setCellValue('Q' . $i, $v['L_ID'])
                    ->setCellValue('R' . $i, $v['P_OutPID'])
                    ->setCellValue('S' . $i, $v['P_Result'])
                    ->setCellValue('T' . $i, $v['P_Fail'])
                    ->setCellValue('U' . $i, $v['P_Ext'])
                    ->setCellValue('V' . $i, " " . $v['P_ID'])
                    ->setCellValue('W' . $i, $v['P_RequestMan'])
                    ->setCellValue('X' . $i, $v['P_RequestTime'])
                    ->setCellValue('Y' . $i, $v['P_RequestCourt'])
                    ->setCellValue('Z' . $i, " " . $v['M_Phone']);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Through the application form');
        $objPHPExcel->setActiveSheetIndex(0);
        $filename = urlencode('Through the application form') . '_' . $P_StartTime . "for" . $P_EndTime;
        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header('Content-Type:text/plain;charset=utf-8');
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type: application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    //excel导入
    function importExcel() {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile(); // 实例化上传类
        $upload->savePath = "./excel/"; // 设置附件上传目录
        $filename = GetRandStr(8); //文件名
        $upload->saveRule = $filename;
        $upload->uploadReplace = true;

        if (!$upload->upload()) {
            echo "文件上传失败!";
        } else {

            require_once './Common/Classes/PHPExcel/IOFactory.php';
            $reader = PHPExcel_IOFactory::createReader('Excel5'); // 读取 excel 文档
            $PHPExcel = $reader->load("./excel/" . $filename . ".xls"); // 文档名称
            $sheet = $PHPExcel->getSheet(0); // 读取第一个工作表(编号从 0 开始)

            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumm = $sheet->getHighestColumn(); // 取得总列数
            $arr = array(1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z', 27 => 'AA', 28 => 'AB', 29 => 'AC', 30 => 'AD', 31 => 'AE', 32 => 'AF'); //echo $highestRow.$highestColumn;	       
            $Playlist = M('playlist');
            $count = 0;

            for ($row = 2; $row <= $highestRow; $row++) {
                $isLive = (string) $sheet->getCellByColumnAndRow(5, $row)->getValue(); // 状态
//                    var_dump($isLive);
                $data = array();
                if ($isLive == '成功') {
                    $data['P_Result'] = 1;
                    $data['P_Fail'] = (string) $sheet->getCellByColumnAndRow(6, $row)->getValue(); // 标签
                    $data['P_Ext'] = (string) $sheet->getCellByColumnAndRow(7, $row)->getValue(); // 详情
                } else {
                    $data['P_Result'] = 0;
                    $data['P_Fail'] = (string) $sheet->getCellByColumnAndRow(6, $row)->getValue(); // 标签
                    $data['P_Ext'] = (string) $sheet->getCellByColumnAndRow(7, $row)->getValue(); // 详情
                }
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumm . $row, NULL, TRUE, FALSE);
                $outid = str_replace(' ', '', $rowData[0][22]); // PID 编号
                $Cid = str_replace(' ', '', $rowData[0][10]); // 案件代码
                // $res=$Playlist->where("P_CaseCode=$Cid and P_OutPID=$outid")->save($data);
                $sql = 'update `t_playlist` set `P_Result` = "' . $data['P_Result'] . '" , `P_Fail`="' . $data['P_Fail'] . '" ,`P_Ext`="' . $data['P_Ext'] . '" where P_CaseCode="' . $Cid . '"and `P_OutPID` = "' . $outid . '"';

                $res = $Playlist->execute($sql);
                if ($res >= 0) {
                    $count++;
                } else {
                    echo('第' . $row . '行，数据有误<br/>');
                }
            }
            header('Content-Type:text/plain;charset=utf-8');
            echo("总共导入条数为" . ($highestRow - 1) . "条，实际修改条数为" . $count . "条");
            exit;

//	        echo "总共导入条数为".($highestRow-1)."条，实际修改条数为".$count."条";
        }
    }

}
