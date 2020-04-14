<?php
class RBACAction extends CommonAction
{
	//*********************************************人员管理**************************************************
	//人员管理主页
	public function user(){
		$User=M('user');
/* 		$res=$User->table('t_user user,t_court court')
		->where("user.M_Court=court.C_ID")
		->select(); */
		//table2 on table1.a = table2.b
		$res=$User->join("t_court on t_user.M_Court=t_court.C_ID")
                        ->order('M_ID desc')
		->select();
		$this->assign("level6","active open");
		$this->assign("level6_1","active open");
		$this->assign('user',$res);
		$this->display();
	}
	
	//新增人员主页
	public function addUser(){
		//获取所有检察院
		$Court=M('court');
		$res=$Court->select();
		$this->assign('courts',$res);
		$this->display();
	}
	
	//新增人员操作
	public function addUserHandler(){
		$User=D('user');
		$_POST['M_Password']=md5($_POST['M_Password']);
		if (!$User->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($User->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$User->add();
		}
		if($res){
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
	}
	
	//编辑人员页面
	public function editUser(){
		$M_ID=$_GET['M_ID'];//人员ID
		//人员信息
		$User=M('user');
		$user=$User->where("M_ID=$M_ID")->find();
		//获取所有检察院
		$Court=M('court');
		$court=$Court->select();
		$this->assign('courts',$court);
		$this->assign('user',$user);
		$this->display();
	}
	//人员编辑操作
	public function editUserHandler(){
		$User=M('user');
		if (!$User->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($User->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$User->save();
		}
		if($res){
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
	}
	
	//人员删除操作
	//ajax
	public function delUserHandler(){
		$M_ID=$_GET['M_ID']; //人员ID
		$User=M('user');
		$res=$User->where("M_ID=$M_ID")->delete();
		if($res){
			$this->ajaxReturn('','删除成功！',1);
		}else{
			$this->ajaxReturn('','删除失败！',0);
		}
		//后续删除角色对应的人员
	}
	
	//用户角色分配
	public function roleAccess(){
		$userId=$_GET[M_ID];
		$Role=M('role');
		//取出所有角色
		$roles=$Role->select();
		//取出该用户的角色
		$role_user=M('role_user');
		$userRoles=$role_user->where("user_id=$userId")->select();
		$this->assign('roles',$roles);
		$this->assign('userRoles',$userRoles);
		$this->assign('userId',$userId);
		$this->display();
	}
	
	//角色分配操作
	public function roleAccessHandler(){
		$userId=$_POST['userId'];
		//删除该用户已分配的角色
		$role_user=M('role_user');
		$role_user->where("user_id=$userId")->delete();
	
		//添加选中的角色
		foreach($_POST[role_id] as $role_id){
			$role_user->add(array('role_id'=>$role_id,'user_id'=>$userId));
		}
		$this->success("操作成功");
	}
	
	//*************************************************************节点管理******************************************
	//节点首页
	public function node(){
		$this->assign("level6","active open");
		$this->assign("level6_3","active");
		$this->display();
	}
	
	//读取节点
	public function getNodes(){
		//取出所有节点
		$Nodes=M('node');
		$res=$Nodes->select();
		$nodes='';
		//转换成字符串
		foreach($res as $v){
			if($v[level]>1){
				$tmp=",open:true";
			}
			$nodes.='{id:'.$v[id].',pId:'.$v[pid].',name:"'.$v[title].'('.$v[name].')",nLevel:'.$v[level].$tmp.'},';
		}
		echo '['.$nodes.']';
	}
	
	//添加节点层
	public function addNode(){
		//查询父节点名称
		$Node=M('node');
		$res=$Node->where("id=$_GET[id]")->find();
		$this->assign('pNode',$res['title']);
		$this->assign('id',$_GET['id']);
		$this->assign('level',$_GET['level']+1);
		$this->display('addNode');
	}
	
	//添加节点操作
	public function addNodeHandler(){
		$Node=M('node');
		if (!$Node->create()){
			exit($this->error($Node->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$Node->add();
			if($res){
				$this->success();
			}else{
				$this->error();
			}
		}
	}
	
	//删除节点操作
	public function delNodeHandler(){
		$Node=M('node');
                $id = htmlspecialchars($_POST[id]);
		//查询是否存在子节点，不允许删除有子节点的父节点
		$res=$Node->where("pid=$id")->find();
		if($res){
			$this->ajaxReturn('该节点存在子节点','',0);
		}else{
			//删除node节点
			$res=$Node->where("id=$id")->delete();
			if($res){
				$this->ajaxReturn('删除成功','',1);
			}else{
				$this->ajaxReturn('删除失败','',0);
			}
	
		}
	}
	
	//********************************************************角色管理************************************
	//角色管理
	public function role()
	{
		$this->assign("level6","active open");
		$this->assign("level6_2","active");
		//查询角色表
		$Role=M("role");
		$res=$Role->select();
		$this->assign('roles',$res);
		$this->display('role');
	}
	
	/* //禁用/启用角色操作
	public function roleStatusHandler(){
		$Role=M('role');
		$status=1-$_POST['status'];
		$res=$Role->where("id=$_POST[id]")->setField("status",$status);
		if($res){
			$this->ajaxReturn('操作成功','',1);
		}else{
			$this->ajaxReturn('操作失败','',0);
		}
	} */
	
	//权限分配页
	public function rightAccess(){
		$roleId=$_GET[roleId];
		$Node=M('node');
		//取出全部控制器节点
		$res=$Node->where('pid=1')->select();
		//取出控制器所有对应的操作节点
		foreach($res as $k=>$v){
			$child_nodes=$Node->where("pid=$v[id]")->select();
			$res[$k][]=$child_nodes;
		}
		//取出该角色对应的所有权限
		$Access=M('access');
		$rightNodes=$Access->where("role_id=$roleId")->field('node_id')->select();
		$this->assign('rightNodes',$rightNodes);
		$this->assign('roleId',$roleId);
		$this->assign('nodes',$res);
		$this->display();
	}
	
	//权限分配处理
	public function rightAccessHandler(){
		//删除所有权限
		$Access=M('access');
		$Access->where("role_id=$_POST[roleId]")->delete();
	
		//再分配权限，取出控制器节点ID
		//加入Admin模块根节点
		$Access->add(array('role_id'=>$_POST[roleId],'node_id'=>1,'level'=>1,'pid'=>0));
		foreach($_POST as $k=>$node){
			//如果为数字,为控制器id
			if(is_numeric($k)){
				//添加控制器权限到access表
				$Access->add(array('role_id'=>$_POST[roleId],'node_id'=>$k,'level'=>2,'pid'=>1));
				//取出操作的节点ID
				foreach($node as $nodeId){
					//添加操作权限
					$Access->add(array('role_id'=>$_POST[roleId],'node_id'=>$nodeId,'level'=>3,'pid'=>$k));
				}
			}
		}
		$this->success("操作成功");
	}
	
	//角色人员列表
	public function userList(){
		$role_user=M('role_user');
                $roleId=htmlspecialchars($_GET[roleId]);
		$res=$role_user->table("t_role_user role_user,t_user user")
		->where("role_user.role_id=$roleId and role_user.user_id=user.M_ID")
		->select();
		$this->assign("user",$res);
		$this->display();
	}
	
	//添加角色
	public function addRole(){
		$this->display();
	}
	
	//添加角色操作
	public function addRoleHandler(){
		$Role=D('role');
		if (!$Role->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Role->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$Role->add();
		}
		if($res){
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
	}
	
	//编辑角色
	public function editRole(){
		$roleId=htmlspecialchars($_GET[roleId]);//角色id
		$Role=M('role');
		$res=$Role->where("id=$roleId")->find();
		$this->assign('role',$res);
		$this->display();
	}
	
	//编辑角色操作
	public function editRoleHandler(){
		$Role=M('role');
		if (!$Role->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			exit($this->error($Role->getError()));
		}else{
			// 验证通过 可以进行其他数据操作
			$res=$Role->save();
		}
		if($res){
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
	}
	
	
	//密码重置
	public function PasswordReset(){
		$MId=htmlspecialchars($_GET['M_ID']);//角色id
		$User=M('user');
		if($_POST){
                $possword['M_Password'] = md5(htmlspecialchars($_POST['Password']));
		$res=$User->where("M_ID=$MId")->save($possword);	
		if($res){
			$this->success("操作成功");
		}elseif($res === 0){
			$this->error("操作失败,修改密码为原账号密码");
		}
		}
		$Name=$User->where('M_ID='.$MId)->field('M_Name')->find();
		$this->assign('M_ID',$MId);
		$this->assign('Name',$Name['M_Name']);
		$this->display();
	}
}
?>