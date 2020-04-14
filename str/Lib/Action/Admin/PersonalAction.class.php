<?php
class PersonalAction extends CommonAction{
	//修改密码页
	public function changePassword(){
		$this->display('changePassword');
	}
	
	//修改密码操作
	public function changePasswordHandler(){
		$M_ID=$_SESSION['uid'];//用户ID
		if($_POST['newPwd1']!=htmlspecialchars($_POST['newPwd2'])){
			$this->error("两次输入的密码不相同！");
		}
		if($_POST['newPwd1']==null||htmlspecialchars($_POST['newPwd2'])==null){
			$this->error("密码不能为空！");
		}
		$User=M('user');
		$user=$User->where("M_ID=$M_ID")->find();
		if(md5($_POST['oldPwd'])!=$user['M_Password']){
			$this->error("旧密码输入错误！");
		}else if(md5($_POST['oldPwd'])==$user['M_Password']){
			$res=$User->where("M_ID=$M_ID")->setField("M_Password",md5($_POST['newPwd1']));
			if($res){
				$this->redirect("Public/success2");
			}else{
				$this->error();
			}
		}
	}
        
        //下载小程序页面
        public function dowXCX(){
            //取检察院
            
            $M_Court = '';
            $Court = new CourtModel();
            $courts = $Court->getInputCourt($M_Court);
            $this->assign('courts',$courts);
            $this->display('dowXcx');
        }
        
        //下载小程序操作
        public function dowXCXHandler() {
            $courtIn = htmlspecialchars($_REQUEST['P_CourtRoomIn']);
            $file_path = "./Public/img/config.ini";
            $str = '[main]
livecode = '.$courtIn.'
[interface]
livemode = http://139.1.1.87/index.php/Public/auto
liveopen = http://139.1.1.87/index.php/Public/start
livepause = http://139.1.1.87/index.php/Public/stopLive
liveresume = http://139.1.1.87/index.php/Public/resumeLive
livefinish = http://139.1.1.87/index.php/Public/endLive
livedelay = http://139.1.1.87/index.php/Public/delay
liveinfo = http://139.1.1.87/index.php/Public/getInfo
openchannel = http://139.1.1.87/index.php/Public/openLive
[mode]
livemode_auto = 1
livemode_manual=2';
            file_put_contents($file_path,  $str);
            $arr[0] = './Public/img/config.ini';
            $arr[1] = './Public/img/tysx_fy.exe';
//            var_dump($arr);exit;
            dowFile($arr);
        }

                //我的日志
	public function myLog(){
		$Log=M('log');
		$log=$Log->join("t_user on t_log.L_Man=t_user.M_ID")->where("t_log.L_Man=$_SESSION[M_ID]")->order('L_DateTime desc')->limit(1000)->select();
		$this->assign('log',$log);
		$this->display("myLog");
	}
	
	//我的日志任务单详情
	public function checkTask(){
		$L_ID=htmlspecialchars($_GET['L_ID']);//日志L_ID
		$Log=M('log');
		//任务单信息
		$log=$Log->where("t_log.L_ID=$L_ID")
                        ->join("t_user on t_log.L_Man=t_user.M_ID")
                        ->find();
		$playlist=json_decode($log['L_Detail'],true);
		//入流信息查询
		$Courtroom=M('courtroom');
		$in=$Courtroom->table("t_courtroom courtroom,t_court court")
		->where("courtroom.CR_ID=$playlist[P_CourtRoomIn] and $playlist[P_CourtIn]=court.C_ID")
		->find();
		//出流信息查询
		$Live=M('live');
		$out=$Live->table("t_live live,t_court court")
		->where("live.L_PID=$playlist[P_OutPID] and $playlist[P_CourtOut]=court.C_ID")
		->find();
		//申请人信息查询
		$User=M('user');
		$user=$User->table('t_user user,t_court court')
		->where("user.M_ID=$playlist[P_RequestMan] and court.C_ID=$playlist[P_RequestCourt]")
		->find();
                $this->assign('log',$log);
		$this->assign('user',$user);
		$this->assign('in',$in);
		$this->assign('out',$out);
		$this->assign('playlist',$playlist);
		$this->display("checkTask");
	}
	
	//切换到垫片
	public function changeRTSPHandler(){
		$P_ID=htmlspecialchars($_GET['id']);
		$Playlist=M('playlist');
		//查询playlist中该条记录
		$playlist=$Playlist->where("P_ID=$P_ID")->find();
		//查询live出流表中channelID
		$Live=M('live');
		$live=$Live->where("L_PID=$playlist[P_OutPID]")->find();
		$xmldata='<?xml version="1.0" encoding="utf-8"?>
						<message module="MULTIPLEXER" version="1.0">
						<header action="REQUEST" command="UPDATE_INPUT" />
						<body>
						<channel>
						    <id>'.$live[L_Channel].'</id>
						<input>
						  <url>'.C("BACKUP_RTSP").'</url>
						  <pid>'.$playlist[P_OutPID].'</pid>
						    </input>
						</channel>
						</body>
						</message>';
		$contentLength = strlen($xmldata);
		//初始一个curl会话
		$curl = curl_init();
		$header[] = "Content-Type: text/xml";
		$header[] = "Content-length: ".$contentLength;
		
		//设置url
		curl_setopt($curl, CURLOPT_URL,self::getStreamServer($playlist[P_Decoder]));
		//设置发送方式：
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		 
		//设置发送数据
		curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);
		//抓取URL并把它传递给浏览器
		$response=curl_exec($curl);
		$xmlResult = simplexml_load_string($response);
		//关闭cURL资源，并且释放系统资源
		curl_close($curl);
		$log_detail="channel:".$live[L_Channel].",url:".C("BACKUP_RTSP").",pid:".$playlist[P_OutPID];
		if($xmlResult->body->channel->status=='ok'){
			//保存日志 
			saveLog("切垫片操作:成功",$log_detail);
			$this->ajaxReturn('','切换垫片成功',1);
		}else{
			//保存日志
			saveLog('切垫片操作:失败',$log_detail);
			$this->ajaxReturn('','切换垫片失败',0);
		}
	}
	
	
	//切回
	public function backRTSPHandler(){
		$P_ID=htmlspecialchars($_GET['id']);
		$Playlist=M('playlist');
		//查询playlist中该条记录
		$playlist=$Playlist->where("P_ID=$P_ID")->find();
		//查询live出流表中channelID
		$Live=M('live');
		$live=$Live->where("L_PID=$playlist[P_OutPID]")->find();
		//查询courtroom入流表中
		$Courtroom=M('courtroom');
		$courtroom=$Courtroom->where("CR_ID=$playlist[P_CourtRoomIn]")->find();
		$port=$courtroom['CR_Port'];
		$url_array=explode('/',$courtroom['CR_URL']);
		$xmldata='<?xml version="1.0" encoding="utf-8"?>
							<message module="MULTIPLEXER" version="1.0">
							<header action="REQUEST" command="UPDATE_INPUT" />
							<body>
							<channel>
							<id>'.$live[L_Channel].'</id>
							<input>
							<url>'.$url_array[0]."//".$url_array[2].":".$port."/".$url_array[3].'</url>
							<pid>'.$playlist[P_OutPID].'</pid>
							</input>
							</channel>
							</body>
							</message>';
		$contentLength = strlen($xmldata);
		//初始一个curl会话
		$curl = curl_init();
		$header[] = "Content-Type: text/xml";
		$header[] = "Content-length: ".$contentLength;
		
		//设置url
		curl_setopt($curl, CURLOPT_URL,self::getStreamServer($playlist[P_Decoder]));
		//设置发送方式：
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			
		//设置发送数据
		curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);
		//抓取URL并把它传递给浏览器
		$response=curl_exec($curl);
		$xmlResult = simplexml_load_string($response);
		//关闭cURL资源，并且释放系统资源
		curl_close($curl);
		$log_detail="channel:".$live[L_Channel].",url:".$courtroom['CR_URL'].",port:".$port.",pid:".$playlist[P_OutPID];
		if($xmlResult->body->channel->status=='ok'){
			//保存日志
			saveLog("切回操作:成功",$log_detail);
			$this->ajaxReturn('','切回成功',1);
		}else{
			//保存日志
			saveLog('切回操作:失败',$log_detail);
			$this->ajaxReturn('','切回失败',0);
		}
	}
	
	//结束节目操作
	public function stopRTSPHandler(){
		$P_ID=htmlspecialchars($_GET['id']);
		$Playlist=M('playlist');
		//查询playlist中该条记录
		$playlist=$Playlist->where("P_ID=$P_ID")->find();
		//查询live出流表中channelID
		$Live=M('live');
		$live=$Live->where("L_PID=$playlist[P_OutPID]")->find();
		$xmldata='<?xml version="1.0" encoding="utf-8"?>
		<message module="MULTIPLEXER" version="1.0">
		<header action="REQUEST" command="UPDATE_INPUT" />
		<body>
		<channel>
		<id>'.$live[L_Channel].'</id>
		<input>
		<url>rtsp://101.1.1.85:554/100</url>
		<pid>'.$playlist[P_OutPID].'</pid>
		</input>
		</channel>
		</body>
		</message>';
		$contentLength = strlen($xmldata);
		//初始一个curl会话
		$curl = curl_init();
		$header[] = "Content-Type: text/xml";
		$header[] = "Content-length: ".$contentLength;
		
		//设置url
		curl_setopt($curl, CURLOPT_URL,self::getStreamServer($playlist[P_Decoder]));
		//设置发送方式：
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			
		//设置发送数据
		curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);
		//抓取URL并把它传递给浏览器
		$response=curl_exec($curl);
		$xmlResult = simplexml_load_string($response);
		//关闭cURL资源，并且释放系统资源
		curl_close($curl);
		$log_detail="channel:".$live[L_Channel].",url:rtsp://101.1.1.85:554/100,pid:".$playlist[P_OutPID];
		if($xmlResult->body->channel->status=='ok'){
			//保存日志
			saveLog("结束节目操作:成功",$log_detail);
			$this->ajaxReturn('','结束节目成功',1);
		}else{
			//保存日志
			saveLog('结束节目操作:失败',$log_detail);
			$this->ajaxReturn('','结束节目失败',0);
		}
	}
	
	//修改结束时间操作
	public function modifyEndTimeHandler(){
		$P_ID=htmlspecialchars($_GET['id']);
		$newEndTime=htmlspecialchars($_GET['newEndTime']);
		$Playlist=M('playlist');
		//查询playlist中该条记录
		$playlist=$Playlist->where("P_ID=$P_ID")->find();
		$P_EndTime=substr($playlist[P_EndTime],0,10).' '.$newEndTime;//新的结束时间
		if(strtotime($P_EndTime)<=strtotime($playlist[P_StartTime])){
			$this->ajaxReturn('','结束时间不能早于开始时间!',0);
		}else{
			$Playlist->where("P_ID=$P_ID")->setField('P_EndTime',$P_EndTime);//修改结束时间
			//操作成功新建txt文件
			$filename="./WaitConfig/".time().rand(10,100).".txt";
			$file=fopen($filename,"w");
			$playlist=$Playlist->where("P_ID=$P_ID")->find();
			//保存日志
			saveLog('修改节目结束时间操作:成功',$P_ID);
			$this->ajaxReturn($playlist,'结束时间修改成功!',1);
		}
	}
	
	/**
	+--------------------------------
	* 提交节目失败操作
	+--------------------------------
	* @date: 2017年1月5日 上午10:58:11
	* @author: Str
	* @param: variable
	* @return:
	*/
	function ifSuccessHandler(){
	    $P_ID=htmlspecialchars($_GET['id']);
	    $failedReason=htmlspecialchars($_GET['failedReason']);
            if(empty($failedReason)){
                $this->error("请填写直播取消理由！");
                exit;
            }
	    $Playlist=M('playlist');
	    $res=$Playlist->where("P_ID=$P_ID")->setField(array('P_Fail','P_Result'),array($failedReason,0));
	    if($res){
	        saveLog('提交节目失败操作:成功',$P_ID);
	        $this->success("操作成功！");
	    }else{
	        saveLog('提交节目失败操作:失败',$P_ID);
	        $this->success("操作失败！");
	    }
	}
	
	/**
	+--------------------------------
	* 取消节目失败操作
	+--------------------------------
	* @date: 2017年1月5日 上午11:12:12
	* @author: Str
	* @param: variable
	* @return:
	*/
	function cancelFailedHandler(){
	    $P_ID=htmlspecialchars($_GET['id']);
	    $Playlist=M('playlist');
	    $res=$Playlist->where("P_ID=$P_ID")->setField(array('P_FailedReason','P_IfSuccess','P_RealEndTime'),array('',1,''));
	    if($res){
	        saveLog('取消节目失败操作:成功',$P_ID);
	        $this->ajaxReturn('','操作成功!',1);
	    }else{
	        saveLog('取消节目失败操作:失败',$P_ID);
	        $this->ajaxReturn('','操作失败!',0);
	    }
	}
	
	//根据解码器号码
	//判断解码服务器地址
	//return string
	public function getStreamServer($Decoder){
		if($Decoder==1){
			return C("STREAM_SERVER_1");
		}else if($Decoder==2){
			return C("STREAM_SERVER_2");
		}else if($Decoder==3){
			return C("STREAM_SERVER_3");
		}else if($Decoder==4){
			return C("STREAM_SERVER_4");
		}else if($Decoder==5){
			return C("STREAM_SERVER_5");
		}else if($Decoder==6){
			return C("STREAM_SERVER_6"); //<!--2018.3.5添加新的解码器 -->  
		}
	}
}