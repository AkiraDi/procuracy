<?php
class ApplyTaskAction extends CommonAction{
	//申请资源首页
	public function index(){
		//获取编辑还未审核的任务 可改
		$M_ID=$_SESSION['M_ID'];
		$Playlist=M('playlist');
		$res=$Playlist->where("t_playlist.P_Status=1 and t_playlist.P_ApplyStatus=1 and t_playlist.P_Status=1 and P_RequestMan=$_SESSION[M_ID]") 
		->join("t_court on t_playlist.P_CourtIn=t_court.C_ID")
		->order("P_StartTime desc")->limit(500)->select();
//var_dump($res);exit;
		$this->assign('playlist',$res);
		$this->assign("level2","active open");
		$this->display();
	}
	
	//新增任务
	public function addTask(){
		$Court=new CourtModel();
		//取出入流检察院
		$courts=$Court->getInputCourt();
		//取出出流检察院
		//$live=$Court->getOutputCourt();
		$this->assign('courts',$courts);
		//$this->assign('live',$live);
		$this->display("addTask");
	}
	
	//查询检察院下的所有听证室
	//ajax
	public function searchCourtRoom(){
		$C_ID=htmlspecialchars($_GET[C_ID]);//检察院ID
		$Courtroom=M('courtroom');
		$res=$Courtroom->where("CR_Belongs=$C_ID")->select();
		$this->ajaxReturn($res);
	}
	
	//查询出流检察院的PID
	//ajax
	public function searchOutPID(){
		$L_ID=htmlspecialchars($_GET[L_ID]);//检察院ID
		$Live=M('live');
		$res=$Live->where("L_ID=$L_ID")->find();
		$this->ajaxReturn($res);
	}
	
	//新增任务操作
	public function _before_addTaskHandler(){
		//*******************************20160909取出出流检察院,动态分配出流检察院******************************
		$Court=new CourtModel();
		$live=$Court->getOutputCourt();
		shuffle($live);//随机排序出流数组
		$Playlist=M('playlist');
		for($i=0;$i<count($live);$i++){
		    $livePID=$live[$i]['L_PID'];
			$is_use=$Playlist
			->where("((P_StartTime<='$_POST[P_StartTime]' and P_EndTime>='$_POST[P_StartTime]') or (P_StartTime>='$_POST[P_StartTime]' and P_StartTime<='$_POST[P_EndTime]')) and (P_OutPID=$livePID)")
			->find();
			if(!$is_use){
				$_POST['P_CourtOut']=$live[$i][L_CourtName];
				$_POST['P_OutPID']=$live[$i][L_PID];
				$_POST['P_Decoder']=$live[$i][L_Decoder];
				break;
			}
		}
		//*******************************************************************************************
/* 		if (!$Playlist->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Playlist->getError()));
		} */
		/* //验证端口时间是否被占用
		$is_use=$Playlist
		->where("((P_StartTime<='$_POST[P_StartTime]' and P_EndTime>='$_POST[P_StartTime]') or (P_StartTime>='$_POST[P_StartTime]' and P_StartTime<='$_POST[P_EndTime]')) and (P_OutPID=$_POST[P_OutPID])")
		->find();
		if($is_use){
			exit($this->error("端口时间已被占用"));
		} */
	}
	public function addTaskHandler(){
	    if(!$_POST['P_OutPID']){
	        $this->error("所有端口已满！");
	    }
		$Playlist=M('playlist');
		$_POST['P_RequestCourt']=$_SESSION['M_Court'];//申请人所属检察院
		$_POST['P_RequestMan']=$_SESSION['M_ID'];//申请人
		$_POST['P_RequestTime']=date('Y-m-d H:i:s', time()); //完成编辑时间
		$_POST['P_ID']=date('YmdHis', time()).rand(10,100); //P_ID,年月日时分秒+两位随机数
		//$_POST['P_SubmitTo']=submitCourt($_POST['P_CourtIn'],$_POST['P_CourtOut'],$_POST['P_RequestCourt']);//审核检察院
		if (!$Playlist->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Playlist->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$Playlist->add();
		}
		if($res){
			//保存日志
			saveLog("申请任务",$_POST['P_ID']);
//			$this->success("操作成功");
		}else{
//			$this->error("操作失败");
		}
	}
	
	//任务编辑页面
	public function editTask(){
		$P_ID=htmlspecialchars($_GET['P_ID']); //任务ID
		$Playlist=M('playlist');
		$playlist=$Playlist->where("P_ID=$P_ID")->find();
		//取出入流检察院对应的所有听证室
		$Courtroom=M('courtroom');
		$courtrooms=$Courtroom->where("CR_Belongs=$playlist[P_CourtIn]")->select();
		$Court=new CourtModel();
		//取出入流检察院
		$courts=$Court->getInputCourt();
		//取出出流检察院
		//$live=$Court->getOutputCourt();
		$this->assign('courts',$courts);
		//$this->assign('live',$live);
		
		//取出Live表中的L_ID
		$Live=M('live');
		$res=$Live->where("L_PID='$playlist[P_OutPID]'")->find();
		$this->assign('LID',$res['L_ID']);
		
		$this->assign('courts',$courts);
		$this->assign('courtrooms',$courtrooms);
		$this->assign('playlist',$playlist);
		$this->display();
	}
	
	//任务编辑操作
	public function _before_editTaskHandler(){
		//*******************************20160909取出出流检察院,动态分配出流检察院******************************
		$Court=new CourtModel();
		$live=$Court->getOutputCourt();
		shuffle($live);//随机排序出流数组
		$Playlist=M('playlist');
		for($i=0;$i<count($live);$i++){
		    $livePID=$live[$i]['L_PID'];
			$is_use=$Playlist
			->where("((P_StartTime<='$_POST[P_StartTime]' and P_EndTime>='$_POST[P_StartTime]') or (P_StartTime>='$_POST[P_StartTime]' and P_StartTime<='$_POST[P_EndTime]')) and (P_OutPID=$livePID)")
			->find();
			if(!$is_use){
				$_POST['P_CourtOut']=$live[$i][L_CourtName];
				$_POST['P_OutPID']=$live[$i][L_PID];
				$_POST['P_Decoder']=$live[$i][L_Decoder];
				break;
			}
		}
		//*******************************************************************************************
		//验证端口时间是否被占用
		/* $is_use=$Playlist
		->where("((P_StartTime<='$_POST[P_StartTime]' and P_EndTime>='$_POST[P_StartTime]') or (P_StartTime>='$_POST[P_StartTime]' and P_StartTime<='$_POST[P_EndTime]')) 
		and(P_OutPID=$_POST[P_OutPID]) and (P_ID!='$_POST[P_ID]')")
		->find();
		if($is_use){
			exit($this->error("端口时间已被占用"));
		} */
	}
	public function editTaskHandler(){
		$Playlist=M('playlist');
		//$_POST['P_SubmitTo']=submitCourt($_POST['P_CourtOut'],$_SESSION['M_Court']);//审核检察院
		if (!$Playlist->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Playlist->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$Playlist->save();
		}
		if($res){
			//保存日志
			saveLog("编辑任务",$_POST['P_ID']);
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
	}
	
	//任务删除操作
	//ajax
	public function delTaskHandler(){
		$P_ID=htmlspecialchars($_GET['P_ID']); //任务P_ID
		$Playlist=M('playlist');
		//保存日志
               $data['P_Status'] = 3;
		saveLog("删除任务",$P_ID);
		$res=$Playlist->where("P_ID=$P_ID")->save($data);
		if($res){
			$this->ajaxReturn('','删除成功！',1);
		}else{
			$this->ajaxReturn('','删除失败！',0);
		}
	}
	
	
}