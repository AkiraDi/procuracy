<?php
class SystemAction extends CommonAction{
	
	//*********************************************检察院管理***********************************************************
	//检察院管理首页
	public function court(){
		$this->assign("level5","active open");
		$this->assign("level5_1","active open");
		$Court=M('court');
		$res=$Court->table("t_court court,t_courtlevel courtlevel")
		->where("court.CL_Name=courtlevel.CL_ID")
		->select();
		//取得所属检察院名称
		foreach($res as $k=>$v){
			$tmp=$Court->where("C_ID=$v[C_ManageBy]")->find();
			$res[$k]['C_ManageBy']=$tmp['C_Name'];
		}
		$this->assign('courts',$res);
		$this->display();
	}
	
	//添加检察院
	public function addCourt(){
		$Court=M('court');
		//取出所有检察院
		$courts=$Court->select();
		//取出所有检察院等级
		$Courtlevel=M('courtlevel');
		$courtlevel=$Courtlevel->select();
		$this->assign('courts',$courts);
		$this->assign('courtlevel',$courtlevel);
		$this->display();
	}
	
	//编辑检察院
	public function editCourt(){
		$Court=M('court');
		//所有检察院选项
		$courts=$Court->select();
		//取出所有检察院等级
		$Courtlevel=M('courtlevel');
		$courtlevel=$Courtlevel->select();
		//取出该条编辑记录
                $id = htmlspecialchars($_GET[id]);
		$res=$Court->where("C_ID=$id")->find();
		$this->assign('courts',$courts);
		$this->assign('courtlevel',$courtlevel);
		$this->assign('info',$res);
		$this->display();
	}
	
	//添加检察院操作
	public function addCourtHandler(){
		$Court=D('court');
		if (!$Court->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Court->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$Court->add();
		}
		if($res){
			$this->success("操作成功");			
		}else{
			$this->error("操作失败");
		}
	}
	
	//更新检察院操作
	public function editCourtHandler(){
		$Court=D('court');
		if (!$Court->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Court->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$Court->save();
		}
		if($res){
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
	}
	
	//删除听证室 
	//ajax方式
	public function delCourtHandler(){
		$C_ID=htmlspecialchars($_GET[C_ID]);
		//查询该听证室是否有所属听证室，有则不能删除
		$Court=M('court');
		$is_father=$Court->where("C_ManageBy=$C_ID")->find();
		if($is_father){
			$this->ajaxReturn('','该听证室存在所属检察院不能删除！',0);
		}else{
			$res=$Court->where("C_ID=$C_ID")->delete();
			if($res){
				$this->ajaxReturn('','删除成功！',1);
			}else{
				$this->ajaxReturn('','删除失败！',0);
			}
		}
	}
	//*********************************************听证室管理（入流）**************************************************
	//听证室管理入流主页
	public function portIn(){
		//查询所有入流听证室
		$Courtroom=M('courtroom');
		$res=$Courtroom->table('t_courtroom courtroom,t_court court')
		->where("courtroom.CR_Belongs=court.C_ID")
		->select();
		$this->assign("courtroom",$res);
		$this->assign("level5","active open");
		$this->assign("level5_3","active open");
		$this->display();
	}
	
	//添加听证室入流
	public function addPortIn(){
		//获取所有检察院
		$Court=M('court');
		$res=$Court->select();
		$this->assign('courts',$res);
		$this->display();
	}
	
	//编辑听证室入流
	public function editPortIn(){
		$CR_ID=htmlspecialchars($_GET['CR_ID']);//入流ID
		//获取所有检察院
		$Court=M('court');
		$courts=$Court->select();
		
		//查询该条编辑听证室入流
		$Courtroom=M('courtroom');
		$courtroom=$Courtroom->where("CR_ID=$CR_ID")->find();
		$this->assign('courtroom',$courtroom);
		$this->assign('courts',$courts);
		$this->display();
	}
	
	//添加听证室入流操作
	public function addPortInHandler(){
		$Courtroom=M('courtroom');
		if (!$Courtroom->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Courtroom->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$Courtroom->add();
		}
		if($res){
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
	}
	
	//听证室入流编辑操作
	public function editPortInHandler(){
		$Courtroom=M('courtroom');
		if (!$Courtroom->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Courtroom->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$Courtroom->save();
		}
		if($res){
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
	}
	
	//听证室入流删除操作
	public function delPortInHandler(){
		$CR_ID=htmlspecialchars($_GET['CR_ID']);//听证室入流ID
		$Courtroom=M('courtroom');
		$res=$Courtroom->where("CR_ID=$CR_ID")->delete();
		if($res){
			$this->ajaxReturn('','删除成功！',1);
		}else{
			$this->ajaxReturn('','删除失败！',0);
		}
	}
	
	//*********************************************直播资源管理（出流）**************************************************
	//直播资源管理出流主页
	public function portOut(){
		$Live=M('live');
		$res=$Live->join("t_court on t_live.L_CourtName=t_court.C_ID")->select();
		$this->assign('name',$_SESSION['name']);
		$this->assign('live',$res);
		$this->assign("level5","active open");
		$this->assign("level5_4","active open");
		$this->display();
	}
	
	//添加直播资源出流
	public function addPortOut(){
		//获取所有检察院
		$Court=M('court');
		$res=$Court->select();
		$this->assign('courts',$res);
		$this->display();
	}
	
	//添加直播资源出流操作
	public function addPortOutHandler(){
		$Live=D('live');
//                var_dump($Live->create());exit;
		if (!$Live->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Live->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$Live->add();
		}
		if($res){
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
	}
	
	//编辑直播资源出流
	public function editPortOut(){
		$L_ID=htmlspecialchars($_GET['L_ID']);//出流ID
		//获取所有检察院
		$Court=M('court');
		$courts=$Court->select();
		//该条出流信息
		$Live=M('live');
		$live=$Live->where("L_ID=$L_ID")->find();
		$this->assign('courts',$courts);
		$this->assign('live',$live);
		$this->display();
	}
	
	//编辑直播资源出流操作
	public function editPortOutHandler(){
		$Live=D('live');
		if (!$Live->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Live->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
//                        var_dump($_REQUEST);exit;
			$res=$Live->save();
		}
		if($res){
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
	}
        
        //编辑直播资源出流
	public function checkPortOut(){
		$L_ID=htmlspecialchars($_GET['L_ID']);//出流ID
		//获取所有检察院
		$Court=M('court');
		$courts=$Court->select();
		//该条出流信息
		$Live=M('live');
		$live=$Live->where("L_ID=$L_ID")->find();
		$this->assign('courts',$courts);
		$this->assign('live',$live);
		$this->display();
	}
	
	//删除直播资源出流操作
	//ajax
	public function delPortOutHandler(){
		$L_ID=htmlspecialchars($_GET['L_ID']);//直播出流ID
		$Live=M('live');
		$res=$Live->where("L_ID=$L_ID")->delete();
		if($res){
			$this->ajaxReturn('','删除成功！',1);
		}else{
			$this->ajaxReturn('','删除失败！',0);
		}
	}
	
	//一键分配所有通道
	public function initChannel(){
		switch($_GET['channel']){
			case 1:
				//$result=self::configChannelHandler(1);
				$result=self::updateChannelHandler(1,$_GET[decoder]);
				if(!$result){
					$this->error('复用通道1配置失败！');
				}else{
					$this->redirect("Public/success2");
				}
				break;
			case 2:
				//$result=self::configChannelHandler(2);
				$result=self::updateChannelHandler(2,$_GET[decoder]);
				if(!$result){
					$this->error('复用通道2配置失败！');
				}else{
					$this->redirect("Public/success2");
				}
				break;
			case 3:
				//$result=self::configChannelHandler(3);
				$result=self::updateChannelHandler(3,$_GET[decoder]);
				if(!$result){
					$this->error('复用通道3配置失败！');
				}else{
					$this->redirect("Public/success2");
				}
				break;
			case 4:
				//$result=self::configChannelHandler(4);
				$result=self::updateChannelHandler(4,$_GET[decoder]);
				if(!$result){
					$this->error('复用通道4配置失败！');
				}else{
					$this->redirect("Public/success2");
				}
				break;
		}
	}
	
	//初始化分配操作
	public function  configChannelHandler($channelID){
		$Live=M('live');
		$live=$Live->where("L_Channel='$channelID' ")->select();
		if($live){
			foreach($live as $v){
				$input.="<input><url>".$v['L_URL'].":".$v['L_Port']."</url><pid>".$v['L_PID']."</pid></input>";
			}
			$xmldata='<?xml version="1.0" encoding="utf-8"?>
			<message module="MULTIPLEXER" version="1.0">
			<header action="REQUEST" command="CONFIG_CHANNEL" />
			<body>
			<channel>
			<id>'.$channelID.'</id>'.$input.'<output>
			<url>asi://1:'.$channelID.'</url>
			</output></channel>
			</body>
			</message>';
			$contentLength = strlen($xmldata);
			//初始一个curl会话
			$curl = curl_init();
			$header[] = "Content-Type: text/xml";
			$header[] = "Content-length: ".$contentLength;
			
			//设置url
			curl_setopt($curl, CURLOPT_URL,C("STREAM_SERVER"));
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
			if($xmlResult->body->channel->status=="ok"){
				self::startChannel($channelID);
				return true;
			}else{
				return false;
			}
		}
	}
	
	//启动复用通道
	public function startChannel($channelID){
		$xmldata='<?xml version="1.0" encoding="utf-8"?><message module="MULTIPLEXER" version="1.0"><header action="REQUEST" command="START_CHANNEL"/><body><channel><id>'.$channelID.'</id></channel></body></message>';

		$contentLength = strlen($xmldata);
		//初始一个curl会话
		$curl = curl_init();
		$header[] = "Content-Type: text/xml";
		$header[] = "Content-length: ".$contentLength;
			
		//设置url
		curl_setopt($curl, CURLOPT_URL,C("STREAM_SERVER"));
		//设置发送方式：
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		//设置发送数据
		curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);
		//抓取URL并把它传递给浏览器
		curl_exec($curl);
		//关闭cURL资源，并且释放系统资源
		curl_close($curl);
	}
	
	//初始化复用通道，update_input 
	public function updateChannelHandler(){
		$Live=M('live');
                $LID = htmlspecialchars($_GET[L_ID]);
		//查询出流信息
		$live=$Live->where("L_ID=$LID")->find();

		$input.="<input><url>rtsp://139.1.1.85:554/100</url><pid>".$live[L_PID]."</pid></input>";
		$xmldata='<?xml version="1.0" encoding="utf-8"?>
		<message module="MULTIPLEXER" version="1.0">
		<header action="REQUEST" command="UPDATE_INPUT" />
		<body>
		<channel><id>'.$live[L_Channel].'</id>'.$input.'</channel>
		</body>
		</message>';
		$contentLength = strlen($xmldata);
		//初始一个curl会话
		$curl = curl_init();
		$header[] = "Content-Type: text/xml";
		$header[] = "Content-length: ".$contentLength;
			
		//设置url
		curl_setopt($curl, CURLOPT_URL,self::getStreamServer($live[L_Decoder]));
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
		if($xmlResult->body->channel->status=="ok"){
			$this->ajaxReturn('','初始化成功！',1);
		}else{
			$this->ajaxReturn('','初始化失败！',0);
		}

	}
	//*********************************************日志查询**************************************************
	//日志查询主页
	public function log(){
		$Log=M('log');
		$log=$Log->join("t_user on t_log.L_Man=t_user.M_ID")->order('L_DateTime desc')->limit(1000)->select();
		//$sql="select * from t_log,t_user where t_log.L_Man=t_user.M_ID order by t_log.L_ID limit 1000";
		//$log=$Log->query($sql);
		$this->assign('log',$log);
                $this->assign("admin", $_SESSION['administrator']);
		$this->assign("level5","active open");
		$this->assign("level5_5","active open");
		$this->display();
	}
        
        public function findlog(){
            $Log=M('log');
            $StartTime = htmlspecialchars($_REQUEST[StartTime]);
            $EndTime = htmlspecialchars($_REQUEST[EndTime]);
            $log=$Log->where(" L_DateTime>'$StartTime' and L_DateTime<'$EndTime'")
                    ->join("t_user on t_log.L_Man=t_user.M_ID")
                    ->order('L_DateTime desc')->limit(200)->select();
            //$sql="select * from t_log,t_user where t_log.L_Man=t_user.M_ID order by t_log.L_ID limit 1000";
            //$log=$Log->query($sql);
            $this->assign('log',$log);
            $this->assign('time',($_REQUEST));
            $this->assign("level5","active open");
            $this->assign("level5_5","active open");
            $this->display('log');
            
        }
        
	//查看日志任务单详情
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
	
	//*********************************************手动应急切换**************************************************
	//手动切换主页
	public function manual(){
		$Court=M('court');
		//取出所有检察院
		$courts=$Court->select();
		
		//取出所有出流检察院
		$Live=M('live');
		$live=$Live->table('t_live live,t_court court')
		->where("live.L_CourtName=court.C_ID")
		->select();
		
		$this->assign('courts',$courts);
		$this->assign('live',$live);
		$this->assign("level5","active open");
		$this->assign("level5_6","active open");
		$this->display();
	}
	
	//手动应急切换操作
	public function manualHandler(){
		$Courtroom=M('courtroom');
		$InInfo=$Courtroom->where("CR_ID='$_POST[P_CourtRoomIn]'")->find();
		$url=$InInfo['CR_URL'];
		$port=$InInfo['CR_Port'];
		$pid=$_POST['P_CourtOut'];
		
		//根据pid查询出流表中的复用通道号
		$Live=M('live');
		$live=$Live->where("L_PID='$_POST[P_CourtOut]' ")->find();
		$channel=$live['L_Channel'];
		$url_array=explode('/',$url); //拆分入流地址到数组
		
		$xmldata='<?xml version="1.0" encoding="utf-8"?>
								<message module="MULTIPLEXER" version="1.0">
								<header action="REQUEST" command="UPDATE_INPUT" />
								<body>
								<channel>
								    <id>'.$channel.'</id>
								<input>
								  <url>'.$url_array[0]."//".$url_array[2].":".$port."/".$url_array[3].'</url>
								  <pid>'.$pid.'</pid>
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
		curl_setopt($curl, CURLOPT_URL,self::getStreamServer($live[L_Decoder]));
//                var_dump(self::getStreamServer($live[L_Decoder]));exit;
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
		$log_detail="channel:".$channel.",url:".$url.",port:".$port.",pid:".$pid;
		if($xmlResult->body->channel->status=='ok'){
			//保存日志 
			saveLog("手动应急操作:成功",$log_detail);
			$this->redirect("Public/success2");
		}else{
			//保存日志
			saveLog('手动应急操作:失败',$log_detail);
			$this->error();
		}
	}
	
	/**
	+--------------------------------
	* 失败原因管理
	+--------------------------------
	* @date: 2017年1月19日 上午11:43:13
	* @author: Str
	* @param: variable
	* @return:
	*/
	function failedreason(){
	    //查询失败原因表
	    $Failedreason=M('failedreason');
	    $reason=$Failedreason->select();
	    
	    $this->assign('reason',$reason);
	    $this->display();
	}
	
	/**
	+--------------------------------
	* 添加失败原因操作
	+--------------------------------
	* @date: 2017年1月19日 上午11:55:07
	* @author: Str
	* @param: variable
	* @return:
	*/
	function addFailedreasonHandler(){
	    $Failedreason=D('failedreason');
		if (!$Failedreason->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Failedreason->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$Failedreason->add();
		}
		if($res){
			$this->success("操作成功");			
		}else{
			$this->error("操作失败");
		}
	}
	
	/**
	+--------------------------------
	* 失败原因编辑页
	+--------------------------------
	* @date: 2017年1月19日 下午2:11:33
	* @author: Str
	* @param: variable
	* @return:
	*/
	function editFailedreason(){
	    $id=htmlspecialchars($_GET[id]);
	    $Failedreason=M('failedreason');
	    $res=$Failedreason->where("F_ID=$id")->find();
	    $this->assign('res',$res);
	    $this->display();
	}
	
	/**
	+--------------------------------
	* 失败原因编辑操作
	+--------------------------------
	* @date: 2017年1月19日 下午2:17:37
	* @author: Str
	* @param: variable
	* @return:
	*/
	function editFailedreasonHandler(){
	   $Failedreason=D('failedreason');
	    if (!$Failedreason->create()){
	        // 如果创建失败 表示验证没有通过 输出错误提示信息
	        exit($this->error($Failedreason->getError()));
	    }else{
	        // 验证通过 可以进行其他数据操作
	        $res=$Failedreason->save();
	    }
	    if($res){
	        $this->success("操作成功");
	    }else{
	        $this->error("操作失败");
	    }
	}
	
	/**
	+--------------------------------
	* 删除失败原因操作
	+--------------------------------
	* @date: 2017年1月19日 下午2:21:01
	* @author: Str
	* @param: variable
	* @return:
	*/
	function delFailedreasonHandler(){
	    $id=htmlspecialchars($_GET[id]);
	    $Failedreason=M('failedreason');
        $res=$Failedreason->where("F_ID=$id")->delete();
        if($res){
            $this->ajaxReturn('','删除成功！',1);
        }else{
            $this->ajaxReturn('','删除失败！',0);
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
			return C("STREAM_SERVER_6");//<!--2018.3.5添加新的解码器 -->  
		}
	}
}