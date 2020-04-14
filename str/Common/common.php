<?php

//计算入流检察院，出流检察院的审核检察院
//入流检察院id 出流检察院id 当前（申请或审核）检察院id
function submitCourt($courtInId,$courtOutId,$submitCourtId){
	//查出入流检察院所有父检察院
	$courtInParents=getAllParentCourts($courtInId);
	//查询出流检察院所有父检察院
	$courtOutParents=getAllParentCourts($courtOutId);
	
	//如果申请或审核检察院在2个集合内，则该检察院就事最终审核
	if(in_array($submitCourtId,$courtInParents)&&in_array($submitCourtId,$courtOutParents)){
		return $submitCourtId;
	//如果不在，取他的父检察院
	}else{
		$Court=M('court');
		$res=$Court->where("C_ID=$submitCourtId")->find();
		return $res[C_ManageBy];
	}
}

//查询所有检察院的父检察院
//返回父检察院array
function getAllParentCourts($courtId){
	$x=1;
	$Court=M('court');
	$courts=array();
	$courts[]=$courtId;
	while($x>0){
		$res=$Court->where("C_ID=$courtId")->find();
		$courts[]=$res[C_ManageBy];
		$courtId=$res[C_ManageBy];
		$x=$res[C_ID];
	}
	return $courts;
}

//保存日志
//
function saveLog($type,$P_ID,$remark){
		$data[L_Type]=$type;
		$data[L_P_ID]=$P_ID;//任务ID
		$data[L_DateTime]=date("Y-m-d H:i:s",time());//时间
		$data[L_Man]=$_SESSION[M_ID];//操作人
		//根据PID查询表单内容
		$Playlist=M('playlist');
		$playlist=$Playlist->where("P_ID=$P_ID")->find();
		if($playlist){
			$data[L_Detail]=json_encode($playlist);
			$Log=M('log');
			$Log->add($data);
		}else{
			$data[L_Detail]=$remark;
			$Log=M('log');
			$Log->add($data);
		}
	}


/**
  +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
  +----------------------------------------------------------
 * @static
 * @access public
  +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
  +----------------------------------------------------------
 * @return string
  +----------------------------------------------------------
 */
function musubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if (function_exists("mb_substr"))
        return mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        return iconv_substr($str, $start, $length, $charset);
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    if ($suffix)
        return $slice . "…";
    return $slice;
}

//根据秒 格式化时间
function formatTime($seconds){
	$hour=round($seconds/3600,0);
	$min=round(($seconds-$hour*3600)/60,0);
	$sec=$seconds-$hour*3600-$min*60;
	if($hour>0){
		return $hour."小时".$min."分".$sec."秒";
	}else{
		return $min."分".$sec."秒";
	}
}

//页面得到不同语言字段值
function getField($id, $field, $lang, $length) {
    $arrfield = getFieldStr($lang);
    $objCate = M("Detail");
    $arrCate = $objCate->find($id);
    $title = $arrCate[$arrfield[$field]];
    $title = musubstr($title, 0, $length);
    return $title;
}

function toDate($time, $format = 'Y-m-d H:i:s') {
    if (empty($time)||($time=='-1')) {
        return '';
    }
    $format = str_replace('#', ':', $format);
    return date($format, $time);
}

/**
 * 短信发送
 */
function Post($curlPost,$url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
}

//文件夹判断是否为空
function my_judge_empty_dir($module)  
{  
	//截取'Common'的位置
	$tpl_pos=strrpos(__FILE__,"Common");
	//得到模块对应模版的路径地址
	$dir=substr(__FILE__,0,$tpl_pos)."Tpl\default\\".$module."\\".MODULE_NAME;
    $handle = opendir($dir);  
    while (($file = readdir($handle)) !== false)  
    {  
        if ($file != "." && $file != "..")  
        {  
            closedir($handle);  
            return false;  
        }  
    }  
    closedir($handle);
    return true;  
}   

//生成短信验证码随机数
function random_verify($number){
	$randStr = str_shuffle('1234567890');
	$rand = substr($randStr,0,$number);
	return $rand;
}

//发送短信验证码
function sendVerify(){
	$User=M('User');
	//查询上一次发送验证码的时间，计算和现在请求发送短信验证码的时间差
	$lastSendTime=$User->where("MACAddress='".$_SESSION['mac']."'")->field('SendVerifyTime,SendPhone')->find();
	$_SESSION['interval']=time()-intval($lastSendTime['SendVerifyTime']);
	if(($lastSendTime['SendVerifyTime']!='')&&($_SESSION['interval']<60)&&($_SESSION['tempPhone']==$lastSendTime['SendPhone'])){
		return false;
	}else{
		//产生随机4位的验证码
		$_SESSION["verifyNumber"]=random_verify(4);
		//保存验证短信发送时间戳,60秒内不允许重复
		$User->where("MACAddress='".$_SESSION['mac']."'")->setField(array('SendVerifyTime','SendPhone'),array(time(),$_SESSION['tempPhone']));
		$_SESSION['interval']=0;
		//手机发送短信
	}
}

//保存电话号码
function savePhone($phone,$changeNum){
	$User=M('User');
	//第一个电话号码字段为空时
	if(!$_SESSION['phone1']){
		$User->where("MACAddress='".$_SESSION['mac']."'")->setField('PhoneNo1',$phone);
	}
	//第二个电话号码字段为空时
	else if(!$_SESSION['phone2']){
		$User->where("MACAddress='".$_SESSION['mac']."'")->setField('PhoneNo2',$phone);
	}
	//第三个电话号码字段为空时
	else if(!$_SESSION['phone3']){
		$User->where("MACAddress='".$_SESSION['mac']."'")->setField('PhoneNo3',$phone);
	}
	//如果保存电话的三个字段都满了，替换修改的那个号码
	else{
		$User->where("MACAddress='".$_SESSION['mac']."'")->setField('PhoneNo'.$changeNum,$phone);
	}
}

//插入一段字符串
function str_insert($str, $i, $substr)
{
	for($j=0; $j<$i; $j++){
		$startstr .= $str[$j];
	}
	for ($j=$i; $j<strlen($str); $j++){
		$laststr .= $str[$j];
	}
	$str = ($startstr . $substr . $laststr);
	return $str;
}


//二维码生成
function QRcode(){
	import('@.ORG.QRcode');
	import('@.ORG.Image');
	$data="http://www.163.com";
	$outfile='./Public/images/QRcode/text.png';
	$water='./Public/images/sitv_water.png';
	// L水平 7%的字码可被修正【官方推荐】
	// M水平 15%的字码可被修正
	// Q水平 25%的字码可被修正
	// H水平 30%的字码可被修正
	$level='M';
	$size=9;
	$margin=1;
	QRcode::png($data, $outfile, $level, $size,$margin);
	 
	$Image = new Image();
	//给图片添加logo水印
	$Image->water($outfile,$water);
}

/**对excel里的日期进行格式转化*/
function GetData($val){
	$jd = GregorianToJD(1, 1, 1970);
	$gregorian = JDToGregorian($jd+intval($val)-25569);
	return $gregorian;/**显示格式为 “月/日/年” */
}

/**
 +----------------------------------------------------------
 * Import Excel | 2013.08.23
 * Author:HongPing <hongping626@qq.com>
 +----------------------------------------------------------
 * @param  $file   upload file $_FILES  文件相对路径
 *                 $column 读取xml表格的列数
 +----------------------------------------------------------
 * @return array   array("error","message")
 +----------------------------------------------------------
 */
function importExcel($file,$Column=17){
	if(!file_exists($file)){
		return array("error"=>0,'message'=>'file not found!');
	}
	require_once './Common/Classes/PHPExcel.php';
	$objReader = new PHPExcel_Reader_Excel2007();
	try{
		$PHPReader = $objReader->load($file);
	}catch(Exception $e){
	}
	if(!isset($PHPReader)) return array("error"=>0,'message'=>'read error!');
	$allWorksheets = $PHPReader->getAllSheets();
	$i = 0;
	foreach($allWorksheets as $objWorksheet){
		$sheetname=$objWorksheet->getTitle();
		$allRow = $objWorksheet->getHighestRow();//how many rows
		$highestColumn = $objWorksheet->getHighestColumn();//how many columns
		$allColumn = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$allColumn=$Column;
		$array[$i]["Title"] = $sheetname;
		$array[$i]["Cols"] = $allColumn;
		$array[$i]["Rows"] = $allRow;
		$arr = array();
		$isMergeCell = array();
		foreach ($objWorksheet->getMergeCells() as $cells) {//merge cells
			foreach (PHPExcel_Cell::extractAllCellReferencesInRange($cells) as $cellReference) {
				$isMergeCell[$cellReference] = true;
			}
		}
		for($currentRow = 1 ;$currentRow<=$allRow;$currentRow++){
			$row = array();
			for($currentColumn=0;$currentColumn<$allColumn;$currentColumn++){
				;
				$cell =$objWorksheet->getCellByColumnAndRow($currentColumn, $currentRow);
				$afCol = PHPExcel_Cell::stringFromColumnIndex($currentColumn+1);
				$bfCol = PHPExcel_Cell::stringFromColumnIndex($currentColumn-1);
				$col = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
				$address = $col.$currentRow;
				$value = $objWorksheet->getCell($address)->getValue();
				if(substr($value,0,1)=='='){
					return array("error"=>0,'message'=>'can not use the formula!');
					exit;
				}
			 	if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
					$cellstyleformat=$cell->getStyle( $cell->getCoordinate() )->getNumberFormat();
					$formatcode=$cellstyleformat->getFormatCode();
					if (preg_match('/^([$[A-Z]*-[0-9A-F]*])*[dy]/i', $formatcode)) {
						$value=gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($value));
					}else{
						$value=PHPExcel_Style_NumberFormat::toFormattedString($value,$formatcode);
					}
				} 
				if($isMergeCell[$col.$currentRow]&&$isMergeCell[$afCol.$currentRow]&&!empty($value)){
					$temp = $value;
				}elseif($isMergeCell[$col.$currentRow]&&$isMergeCell[$col.($currentRow-1)]&&empty($value)){
					$value=$arr[$currentRow-1][$currentColumn];
				}elseif($isMergeCell[$col.$currentRow]&&$isMergeCell[$bfCol.$currentRow]&&empty($value)){
					$value=$temp;
				}
				$row[$currentColumn] = $value;
			}
			$arr[$currentRow] = $row;
		}
		$array[$i]["Content"] = $arr;
		$i++;
	}
	spl_autoload_register(array('Think','autoload'));//must, resolve ThinkPHP and PHPExcel conflicts
	unset($objWorksheet);
	unset($PHPReader);
	unset($PHPExcel);
	//unlink($file);
	return array("error"=>1,"data"=>$array);
}

//
function getXmlToArray($file){
	$res=importExcel($file);
	//删除表格中的空行
	$arr_excel=array();
	foreach($res['data'][0]['Content'] as $key=>$val){
		$res=array_filter($val);//去除数组中的空字符元素
		if($res){
			$arr_excel[$key]=$val;
		}
	}
	return $arr_excel;
}

//随机字符串
function GetRandStr($length){
	$str='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$len=strlen($str)-1;
	$randstr='';
	for($i=0;$i<$length;$i++){
		$num=mt_rand(0,$len);
		$randstr .= $str[$num];
	}
	return $randstr;
}

//curl
function curl($url, $data = NULL, $httpType = 'POST', $header = '') {
    list($usec, $sec) = explode(" ", microtime());
    $start = ((float) $usec + (float) $sec);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    if($header){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    if ($httpType == 'POST') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        $postString = '';
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $postString .= $key . '=' . $value . '&';
            }
            $postString = substr($postString, 0, -1);
        } else {
            $postString = $data;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
    } elseif ($httpType == 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $getString = '';
        if ($data) {
            foreach ($data as $key => $value) {
                $value = urlencode($value);
                $getString .= $key . '=' . $value . '&';
            }
            $getString = substr($getString, 0, -1);
            $getString = '?' . $getString;
        }
        curl_setopt($ch, CURLOPT_URL, $url . $getString);
//            print_r($url . $getString);exit;
    }
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    list($usec, $sec) = explode(" ", microtime());
    $end = ((float) $usec + (float) $sec);

    //保存日志
    saveLog("接口请求", 'request---' . $url . $postString . $getString . "\n" . 'response---' . $response);

    if ($http_code == 200) {
        return $response;
    } else {
        return false;
    }
}

//用户令牌
function getAccessToken(){
    $url = 'http://139.0.31.80:8081/fd-data-share/oauth/token?';
    $params = array(
        'client_id' => '32ce08856f324f06a2ae6b9a056c2b0d',
        'client_secret' => 'QjBYTrDzdGkyA8qrkcgSNRq5korZA3rH',
        'grant_type' => 'client_credentials',
    );

    $response = curl($url, $params, 'POST');
    $result = json_decode($response, true);

    return $result;
}


/**
 * [http 调用接口函数]
 * @Date   2016-07-11
 * @Author GeorgeHao
 * @param  string       $url     [接口地址]
 * @param  array        $params  [数组]
 * @param  string       $method  [GET\POST\DELETE\PUT]
 * @param  array        $header  [HTTP头信息]
 * @param  integer      $timeout [超时时间]
 * @return [type]                [接口返回数据]
 */
function http($url, $params, $method = 'GET', $header = array(), $timeout = 5)
{
    // POST 提交方式的传入 $set_params 必须是字符串形式
    $opts = array(
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => $header
    );

    /* 根据请求类型设置特定参数 */
    switch (strtoupper($method)) {
        case 'GET':
            $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            break;
        case 'POST':
            $params = http_build_query($params);
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        case 'DELETE':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_HTTPHEADER] = array("X-HTTP-Method-Override: DELETE");
            $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        case 'PUT':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 0;
            $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            throw new Exception('不支持的请求方式！');
    }
  
    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    $data = curl_exec($ch);
    $error = curl_error($ch);
    
    //保存日志
//    saveLog("接口请求", 'request---' . $url . $postString . $getString . "\n" . 'response---' . $data);
    return $data;
}

/**
 * PHP发送Json对象数据
 *
 * @param $url 请求url
 * @param $jsonStr 发送的json字符串
 * @return array
 */
function http_post_json($url, $data_string){
    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_POST, 1);  
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(  
        'Content-Type: application/json; charset=utf-8',  
        'Content-Length: ' . strlen($data_string))  
    );  
    ob_start();  
    curl_exec($ch);  
    $return_content = ob_get_contents();  
    ob_end_clean();  
    $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    //保存日志
    saveLog("POST接口请求", 'request---' . $url . $data_string . "\n" . '','response---' . $return_content);
    return $return_content;
}

/**
* 字符串半角和全角间相互转换
* @param string $str 待转换的字符串
* @param int  $type TODBC:转换为半角；TOSBC，转换为全角
* @return string 返回转换后的字符串
*/
function convertStrType($str, $type) {

    $dbc = array(
        '０', '１', '２', '３', '４',
        '５', '６', '７', '８', '９',
        'Ａ', 'Ｂ', 'Ｃ', 'Ｄ', 'Ｅ',
        'Ｆ', 'Ｇ', 'Ｈ', 'Ｉ', 'Ｊ',
        'Ｋ', 'Ｌ', 'Ｍ', 'Ｎ', 'Ｏ',
        'Ｐ', 'Ｑ', 'Ｒ', 'Ｓ', 'Ｔ',
        'Ｕ', 'Ｖ', 'Ｗ', 'Ｘ', 'Ｙ',
        'Ｚ', 'ａ', 'ｂ', 'ｃ', 'ｄ',
        'ｅ', 'ｆ', 'ｇ', 'ｈ', 'ｉ',
        'ｊ', 'ｋ', 'ｌ', 'ｍ', 'ｎ',
        'ｏ', 'ｐ', 'ｑ', 'ｒ', 'ｓ',
        'ｔ', 'ｕ', 'ｖ', 'ｗ', 'ｘ',
        'ｙ', 'ｚ', '－', '　', '：',
        '．', '，', '／', '％', '＃',
        '！', '＠', '＆', '（', '）',
        '＜', '＞', '＂', '＇', '？',
        '［', '］', '｛', '｝', '＼',
        '｜', '＋', '＝', '＿', '＾',
        '￥', '￣', '｀'
    );

    $sbc = array(//半角
        '0', '1', '2', '3', '4',
        '5', '6', '7', '8', '9',
        'A', 'B', 'C', 'D', 'E',
        'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y',
        'Z', 'a', 'b', 'c', 'd',
        'e', 'f', 'g', 'h', 'i',
        'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x',
        'y', 'z', '-', ' ', ':',
        '.', ',', '/', '%', ' #',
        '!', '@', '&', '(', ')',
        '<', '>', '"', '\'', '?',
        '[', ']', '{', '}', '\\',
        '|', '+', '=', '_', '^',
        '$', '~', '`'
    );
    if ($type == 'TODBC') {
        return str_replace($sbc, $dbc, $str); //半角到全角
    } elseif ($type == 'TOSBC') {
        return str_replace($dbc, $sbc, $str); //全角到半角
    } else {
        return $str;
    }
}

//自动录制 
    function AutoLive($P_res){
        $a=0;
        foreach ($P_res as $key => $info) {
         $dataPidList[$info['P_OutPID']][] = $info;
        }
        foreach ($dataPidList as $k=>$value) {
            for($i=0;$i<count($value);$i++){
                $pListAll[$a]['pid'] = $k;
                $pListAll[$a]['recordList'][$i]['recordName'] = $value[$i]['P_CaseName'];
                $pListAll[$a]['recordList'][$i]['reId'] = $value[$i]['P_REID'];
                $pListAll[$a]['recordList'][$i]['recordStartTime'] = $value[$i]['P_StartTime'];
                //$pListAll[$a]['recordList'][$i]['recordEndTime'] = $value[$i]['P_EndTime'];
            		$pListAll[$a]['recordList'][$i]['recordEndTime'] = date('Y-m-d H:i:s', time()+300); //当前结束时间+5分钟
            }
            $a++;
        }
        
        $endList['creater'] = 'courtAdmin';
        $endList['sourceCode'] = 'fy';
        $endList['pidList'] = $pListAll;
        $json_List = json_encode($endList);
        $url = C('TYCLERK').'creat';
        $response = http_post_json($url, $json_List);
        saveLog("手动结束录制",$response);

   }
   
    function dowFile($array) {

        $filename = 'tysx_fy.zip';
        $datalist = $array;
        if (!file_exists($filename)) {
            $zip = new ZipArchive();
            if ($zip->open($filename, ZipArchive::CREATE) == TRUE) {
                foreach ($datalist as $val) {
                    if (file_exists($val)) {
                        $zip->addFile($val, basename($val));
                    }
                }
                $zip->close();
            }
        }
        if (!file_exists($filename)) {
            exit("无法找到文件");
        }
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=' . basename($filename)); //文件名
        header("Content-Type: application/zip"); //zip格式的
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: ' . filesize($filename)); //告诉浏览器，文件大小
        ob_end_clean();
        flush();
        readfile($filename);
        unlink($filename);//删除服务器上文件
    }

?>
