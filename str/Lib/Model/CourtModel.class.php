<?php
class CourtModel extends Model {
	protected $name = 'court';
	protected $_validate         =         array(
			array('C_Name','require','检察院名称必须填写！'), //默认情况下用正则进行验证
			array('CL_Name','require','检察院等级必须填写！'),
			//array('name','','帐号名称已经存在！',0,’unique’,1), // 在新增的时候验证name字段是否唯一
	);
	
	//取出入流检察院，只能选该用户对应的检察院及其下属检察院
	public function getInputCourt($M_Court = ''){
		$Court=M('court');
		//查询所有下属的检察院
                if($M_Court == 1){
                    $courts_array=self::getAllChildren($M_Court);
                    $courts_array[]=$M_Court;
                }else{
                    $courts_array=self::getAllChildren($_SESSION[M_Court]);
                    $courts_array[]=$_SESSION[M_Court];
                }
		//数组转字符串
		foreach($courts_array as $key=>$v){
			if($key==0){
				$courts.=$v;
			}else{
				$courts.=",".$v;
			}
		}
		$map['C_ID'] = array ('in',"$courts");
		$res=$Court->where($map)->select();
		return $res;
	}
	
        //20180403 取流取出所有检察院不分等级 
        //距离直播开始小于三天的能分配所有检察院的出流通道
        //距离直播开始大于三天的只能分配本检察院的出流通道
	public function getOutputCourt(){
		$Court=M('court');
                $PlayList=M('playlist');
                $start_date = date('Y-m-d', strtotime($_POST['P_StartTime']));
                $date_length = (strtotime($start_date) - strtotime(date('Y-m-d'))) / 86400;
                
//		//查询所有下属的检察院
//		$courts_children=self::getAllChildren($_SESSION[M_Court]);
//		//查询当前级别检察院的所有父检察院
//		$courts_father=self::getAllFather($_SESSION[M_Court]);
//		foreach($courts_children as $vo){
//			$courts_array[]=$vo;
//		}
//		foreach($courts_father as $vo){
//			$courts_array[]=$vo;
//		}
//		$courts_array[]=$_SESSION[M_Court];
//		//数组转字符串
//		foreach($courts_array as $key=>$v){
//			if($key==0){
//				$courts.=$v;
//			}else{
//				$courts.=",".$v;
//			}
//		}
                //20190304 改 通道选择
		/*$sql="select *,(select C_Name from t_court where C_ID=t_live.L_CourtName) as C_Name from t_live";
                if ($date_length > 1095) {
                    $M_Court = $_POST[P_CourtIn];   //大于三年只可使用自己的出流通道
                    $where = " where L_CourtName = '".$M_Court."'";
                    $by = " by L_Decoder asc";
                    $sql .= $where . $by;
                }
		$res=$Court->query($sql);
		return $res;*/
                
                if($_POST[P_IsConfig]==1){
                    $sql="select *,(select C_Name from t_court where C_ID=t_live.L_CourtName) as C_Name from t_live where L_IfAlways = 1";
                }  else {
                    $sql="select *,(select C_Name from t_court where C_ID=t_live.L_CourtName) as C_Name from t_live where L_IfAlways = 1 and L_Check = 2";
                }
                if($_POST[P_IsProtect]==1){
                    $ispro = ' and L_Encryption = 1';
                }elseif($_POST[P_IsProtect]==2) {
                    $ispro = ' and L_Encryption = 2';
                }else{
                    $ispro = ' ';
                }
                
                $sql = $sql.$ispro;
		
                if ($date_length > 1095) {
                    $M_Court = $_POST[P_CourtIn];   //大于三年只可使用自己的出流通道
                    $where = " And L_CourtName = '".$M_Court."'";
                    $by = " by L_Decoder asc";
                    $sql .= $where . $by;
                }
		$res=$Court->query($sql);
//                var_dump($res);exit;
		return $res;
		
	}
	
	//查询所有子检察院
	//return array
	public function getAllChildren($courtID){
		global $str_children;
		$Court=M('court');
		$res=$Court->where("C_ManageBy=$courtID")->select();
		if($res){
			foreach($res as $v){
				$str_children[]=$v['C_ID'];
				self::getAllChildren($v['C_ID']);
			}
		}
		return $str_children;
	}
	
	//查询检察院的父检察院
	//return array
	public function getAllFather($courtID){
		global $str_father;
		$Court=M('court');
		$res=$Court->where("C_ID=$courtID")->find();
		if($res['C_ManageBy']!=0){
				$str_father[]=$res['C_ManageBy'];
				self::getAllFather($res['C_ManageBy']);
		}
		return $str_father;
	}
	
	//判断审核检察院
	//@param $courtOutId 出流检察院ID
	//@param $submitCourtId 审核检察院，初始为申请人检察院
	// return array
	function submitCourt($courtOutId,$submitCourtId){
		if(!C(Verify_ON)){
			$arr['status']=1;
			$arr['submitCourtId']=$courtOutId;
			return $arr;
		}
		//查询出流检察院的父检察院
		$courtOutFatherArray=self::getAllFather($courtOutId);
		if(in_array($submitCourtId,$courtOutFatherArray)||($submitCourtId==$courtOutId)){
			//出流检察院父级中有申请人检察院或出流检察院就是申请人检察院时，直接通过审核
			$arr['status']=1;
			$arr['submitCourtId']=$submitCourtId;
			return $arr;
		}else{
			//如果申请人检察院不在父检察院集合中，或申请人不是出流检察院，直接由出流检察院审核
			$arr['status']=0;
			$arr['submitCourtId']=$courtOutId;
			return $arr;
		}
	}
	
}