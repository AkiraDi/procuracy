<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<title>中国检察听证网</title>

		<meta name="description" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="__PUBLIC__/css/bootstrap.min.css" />
		<link rel="stylesheet" href="__PUBLIC__/css/font-awesome.min.css" />

		<!-- page specific plugin styles -->
		<link rel="stylesheet" href="__PUBLIC__/css/chosen.css" />
		<!-- text fonts -->
		<link rel="stylesheet" href="__PUBLIC__/css/ace-fonts.css" />
		
		<!-- ace styles -->
		<link rel="stylesheet" href="__PUBLIC__/css/ace.min.css" id="main-ace-style" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="__PUBLIC__/css/ace-part2.min.css" />
		<![endif]-->
		<link rel="stylesheet" href="__PUBLIC__/css/ace-skins.min.css" />
		<link rel="stylesheet" href="__PUBLIC__/css/ace-rtl.min.css" />

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="__PUBLIC__/css/ace-ie.min.css" />
		<![endif]-->

		<!-- inline styles related to this page -->
		<link rel="stylesheet" href="__PUBLIC__/css/common.css" />
		<!-- ace settings handler -->
		<script src="__PUBLIC__/js/ace-extra.min.js"></script>

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="__PUBLIC__/js/html5shiv.min.js"></script>
		<script src="__PUBLIC__/js/respond.min.js"></script>
		<![endif]-->
	</head>

<style> 
    #cover{ 
        display:none; 
        position:fixed; 
        z-index:1; 
        top:0; 
        left:0; 
        width:100%; 
        height:100%; 
        background:rgba(0, 0, 0, 0.44); 
    } 
    #coverShow{ 
        display:none; 
        position:fixed; 
        z-index:2; 
        top:50%; 
        left:50%; 
        border:1px solid #127386; 
        width:300px; 
        height:100px; 
        margin-left:-150px; 
        margin-top:-150px; 
        background:#127386; 
    } 
</style>
<body class="no-skin">
    <div id="cover"></div> 
    <div id="coverShow"> 
        <table align="center" border="0" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; height: 100px; min-height: 100px;" bgcolor="#fff">
            <tr> 
                <td align="center" bgcolor="#ffffff"> 
                    正在请求，请正常开庭<br/>
                    <img src="/Public/img/go.gif"/>
                </td> 
            </tr> 
        </table> 
    </div>  
    <!-- #section:basics/navbar.layout -->
    <div id="navbar" class="navbar navbar-default">
        <script type="text/javascript">
            try {
            ace.settings.check('navbar', 'fixed')
            } catch (e) {
            }
        </script>

        <div class="navbar-container" id="navbar-container">
            <!-- #section:basics/sidebar.mobile.toggle -->
            <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler">
                <span class="sr-only">Toggle sidebar</span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>
            </button>

            <!-- /section:basics/sidebar.mobile.toggle -->
            <div class="navbar-header pull-left">
                <!-- #section:basics/navbar.layout.brand -->
                <a href="#" class="navbar-brand">
                    <small>
                        中国检察听证网
                    </small>
                </a>
                <!-- /section:basics/navbar.layout.brand -->
            </div>
            <!-- #section:basics/navbar.dropdown -->
<div class="navbar-buttons navbar-header pull-right" role="navigation">
    <ul class="nav ace-nav">
        <li class="grey"  style="display:none;">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <i class="ace-icon fa fa-tasks"></i>
                <span class="badge badge-grey">4</span>
            </a>
            <ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
                <li class="dropdown-header">
                    <i class="ace-icon fa fa-check"></i>
                    4 Tasks to complete
                </li>
                <li>
                    <a href="#">
                        <div class="clearfix">
                            <span class="pull-left">Software Update</span>
                            <span class="pull-right">65%</span>
                        </div>
                        <div class="progress progress-mini">
                            <div style="width:65%" class="progress-bar"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <div class="clearfix">
                            <span class="pull-left">Hardware Upgrade</span>
                            <span class="pull-right">35%</span>
                        </div>

                        <div class="progress progress-mini">
                            <div style="width:35%" class="progress-bar progress-bar-danger"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <div class="clearfix">
                            <span class="pull-left">Unit Testing</span>
                            <span class="pull-right">15%</span>
                        </div>
                        <div class="progress progress-mini">
                            <div style="width:15%" class="progress-bar progress-bar-warning"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <div class="clearfix">
                            <span class="pull-left">Bug Fixes</span>
                            <span class="pull-right">90%</span>
                        </div>
                        <div class="progress progress-mini progress-striped active">
                            <div style="width:90%" class="progress-bar progress-bar-success"></div>
                        </div>
                    </a>
                </li>
                <li class="dropdown-footer">
                    <a href="#">
                        See tasks with details
                        <i class="ace-icon fa fa-arrow-right"></i>
                    </a>
                </li>
            </ul>
        </li>
        <li class="purple" style="display:none;">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <i class="ace-icon fa fa-bell icon-animated-bell"></i>
                <span class="badge badge-important">8</span>
            </a>
            <ul class="dropdown-menu-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close">
                <li class="dropdown-header">
                    <i class="ace-icon fa fa-exclamation-triangle"></i>
                    8 Notifications
                </li>
                <li>
                    <a href="#">
                        <div class="clearfix">
                            <span class="pull-left">
                                <i class="btn btn-xs no-hover btn-pink fa fa-comment"></i>
                                New Comments
                            </span>
                            <span class="pull-right badge badge-info">+12</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="btn btn-xs btn-primary fa fa-user"></i>
                        Bob just signed up as an editor ...
                    </a>
                </li>
                <li>
                    <a href="#">
                        <div class="clearfix">
                            <span class="pull-left">
                                <i class="btn btn-xs no-hover btn-success fa fa-shopping-cart"></i>
                                New Orders
                            </span>
                            <span class="pull-right badge badge-success">+8</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <div class="clearfix">
                            <span class="pull-left">
                                <i class="btn btn-xs no-hover btn-info fa fa-twitter"></i>
                                Followers
                            </span>
                            <span class="pull-right badge badge-info">+11</span>
                        </div>
                    </a>
                </li>
                <li class="dropdown-footer">
                    <a href="#">
                        See all notifications
                        <i class="ace-icon fa fa-arrow-right"></i>
                    </a>
                </li>
            </ul>
        </li>
        <li class="green" style="display:none;">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <i class="ace-icon fa fa-envelope icon-animated-vertical"></i>
                <span class="badge badge-success">5</span>
            </a>
            <ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
                <li class="dropdown-header">
                    <i class="ace-icon fa fa-envelope-o"></i>
                    5 Messages
                </li>
                <li class="dropdown-content">
                    <ul class="dropdown-menu dropdown-navbar">
                        <li>
                            <a href="#">
                                <img src="__PUBLIC__/avatars/avatar.png" class="msg-photo" alt="Alex's Avatar" />
                                <span class="msg-body">
                                    <span class="msg-title">
                                        <span class="blue">Alex:</span>
                                        Ciao sociis natoque penatibus et auctor ...
                                    </span>
                                    <span class="msg-time">
                                        <i class="ace-icon fa fa-clock-o"></i>
                                        <span>a moment ago</span>
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img src="__PUBLIC__/avatars/avatar3.png" class="msg-photo" alt="Susan's Avatar" />
                                <span class="msg-body">
                                    <span class="msg-title">
                                        <span class="blue">Susan:</span>
                                        Vestibulum id ligula porta felis euismod ...
                                    </span>
                                    <span class="msg-time">
                                        <i class="ace-icon fa fa-clock-o"></i>
                                        <span>20 minutes ago</span>
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img src="__PUBLIC__/avatars/avatar4.png" class="msg-photo" alt="Bob's Avatar" />
                                <span class="msg-body">
                                    <span class="msg-title">
                                        <span class="blue">Bob:</span>
                                        Nullam quis risus eget urna mollis ornare ...
                                    </span>
                                    <span class="msg-time">
                                        <i class="ace-icon fa fa-clock-o"></i>
                                        <span>3:15 pm</span>
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img src="__PUBLIC__/avatars/avatar2.png" class="msg-photo" alt="Kate's Avatar" />
                                <span class="msg-body">
                                    <span class="msg-title">
                                        <span class="blue">Kate:</span>
                                        Ciao sociis natoque eget urna mollis ornare ...
                                    </span>
                                    <span class="msg-time">
                                        <i class="ace-icon fa fa-clock-o"></i>
                                        <span>1:33 pm</span>
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img src="__PUBLIC__/avatars/avatar5.png" class="msg-photo" alt="Fred's Avatar" />
                                <span class="msg-body">
                                    <span class="msg-title">
                                        <span class="blue">Fred:</span>
                                        Vestibulum id penatibus et auctor  ...
                                    </span>
                                    <span class="msg-time">
                                        <i class="ace-icon fa fa-clock-o"></i>
                                        <span>10:09 am</span>
                                    </span>
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown-footer">
                    <a href="inbox.html">
                        See all messages
                        <i class="ace-icon fa fa-arrow-right"></i>
                    </a>
                </li>
            </ul>
        </li>
        <!-- #section:basics/navbar.user_menu -->
        <li class="light-blue">
            <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                <img class="nav-user-photo" src="__PUBLIC__/avatars/avatar5.png" alt="Jason's Photo" />
                <span class="user-info">
                    <small>你好,</small>
                    <?php echo ($_SESSION['name']); ?> 
                </span>
                <i class="ace-icon fa fa-caret-down"></i>
            </a>
            <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                <li>
                    <a href="__GROUP__/Personal/changePassword">
                        <i class="ace-icon fa fa-user"></i>
                        个人设置
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="__GROUP__/Public/logout">
                        <i class="ace-icon fa fa-power-off"></i>
                        退出
                    </a>
                </li>
            </ul>
        </li>
        <!-- /section:basics/navbar.user_menu -->
    </ul>
</div>
<!-- /section:basics/navbar.dropdown -->
        </div><!-- /.navbar-container -->
    </div>

    <!-- /section:basics/navbar.layout -->
    <div class="main-container" id="main-container">
        <script type="text/javascript">
            try {
            ace.settings.check('main-container', 'fixed')
            } catch (e) {
            }
        </script>

        <!-- #section:basics/sidebar -->
        <div id="sidebar" class="sidebar responsive">
            <script type="text/javascript">
                try {
                ace.settings.check('sidebar', 'fixed')
                } catch (e) {
                }
            </script>

            ﻿<div class="sidebar-shortcuts" id="sidebar-shortcuts" style="margin-top: -20px">
    <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large" >
        <button class="btn btn-success" style="width:58px;" id="search">
            <i class="ace-icon fa fa-search"> 查询 </i>
        </button>
<!--        <a class="btn btn-info" href="__GROUP__/Public/collectPlaylist" style="width:58px;" id="collect">
            <i class="ace-icon ace-icon fa fa-signal"> 统计 </i>
        </a>-->
        <button class="btn btn-pink" style="width:58px;" id="log">
            <i class="ace-icon ace-icon fa fa-book"> 日志 </i>
        </button>
        <!-- /section:basics/sidebar.layout.shortcuts -->
    </div>

    <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
        <span class="btn btn-success"></span>
        <span class="btn btn-info"></span>
        <span class="btn btn-warning"></span>
        <span class="btn btn-danger"></span>
    </div>
</div><!-- /.sidebar-shortcuts -->

<ul class="nav nav-list">
    <!-- menu level1 -->
    <li class="<?php echo ($level1); ?>">
        <a href="__GROUP__/MyTask/index" >
            <i class="menu-icon fa fa-user"></i>
            <span class="menu-text">我的任务</span>
        </a>
        <b class="arrow"></b>
    </li>
    <!-- menu level1 End-->

    <!-- menu level7 -->
    <!--					<li class="<?php echo ($level7); ?>">
                                                    <a href="__GROUP__/CaseInput/index" >
                                                                    <i class="menu-icon fa  fa-pencil-square"></i>
                                                                    <span class="menu-text">案件录入查询</span>
                                                            </a>
                                                            <b class="arrow"></b>
                                            </li>-->
    <!-- menu level7 End-->

    <!-- menu level2 -->
    <li class="<?php echo ($level2); ?>">
        <a href="__GROUP__/ApplyTask/index" >
            <i class="menu-icon fa  fa-pencil-square-o"></i>
            <span class="menu-text">直播管理</span>
        </a>
        <b class="arrow"></b>
    </li>
    <!-- menu level2 End-->


    <!--menu level3--> 
    <li class="<?php echo ($level3); ?>">
        <a href="__GROUP__/VerifyTask/index" >
            <i class="menu-icon fa fa-eye"></i>
            <span class="menu-text">直播审批</span>
        </a>
        <b class="arrow"></b>
    </li> 
    <!--menu level3 End-->

    <!-- menu level8 -->
    <!--                                        <li class="<?php echo ($level8); ?>">
                                                    <a href="#" class="dropdown-toggle">
                                                            <i class="menu-icon ace-icon fa fa-rocket"></i>
                                                            <span class="menu-text">监控</span>
                                                            <b class="arrow fa fa-angle-down"></b>
                                                    </a>
                                                    <b class="arrow"></b>
                                                    <ul class="submenu">	
                                                            <li class="<?php echo ($level81); ?>">
                                                                    <a href="__GROUP__/Live/index">
                                                                            直播监控
                                                                    </a>
                                                                    <b class="arrow"></b>
                                                            </li>
    
                                                            <li class="<?php echo ($level82); ?>">
                                                                    <a href="__GROUP__/Live/monitor">
                                                                            书记员监控
                                                                    </a>
                                                                    <b class="arrow"></b>
                                                            </li>
                                                    </ul>
                                            </li>-->
    <li class="<?php echo ($level82); ?>">
        <a href="__GROUP__/Live/monitor" >
            <i class="menu-icon ace-icon fa fa-rocket"></i>
            <span class="menu-text">检察人员播控管理</span>
        </a>
        <b class="arrow"></b>
    </li> 
    <!-- menu level8 End-->

    <!-- menu level4 -->
    <li class="<?php echo ($level4); ?>">
        <a href="__GROUP__/Calendar/index" >
            <i class="menu-icon fa fa-calendar"></i>
            <span class="menu-text">直播周历</span>
        </a>
        <b class="arrow"></b>
    </li>
    <!-- menu level4 End-->

    <!-- menu level5 -->
    <li class="<?php echo ($level5); ?>">
        <a href="#" class="dropdown-toggle">
            <i class="menu-icon fa fa-cog"></i>
            <span class="menu-text">系统管理</span>
            <b class="arrow fa fa-angle-down"></b>
        </a>
        <b class="arrow"></b>
        <ul class="submenu">
            <li class="<?php echo ($level5_1); ?>">
                <a href="__GROUP__/System/court" >
                    检察院管理
                </a>
                <b class="arrow"></b>
            </li>

            <!-- <li class="<?php echo ($level5_2); ?>">
                    <a href="__GROUP__/System/courtLevel">
                            检察院级别管理
                    </a>
                    <b class="arrow"></b>
            </li> -->

            <li class="<?php echo ($level5_3); ?>">
                <a href="__GROUP__/System/portIn">
                    听证室管理（入流）
                </a>
                <b class="arrow"></b>
            </li>

            <li class="<?php echo ($level5_4); ?>">
                <a href="__GROUP__/System/portOut">
                    直播资源管理（出流）
                </a>
                <b class="arrow"></b>
            </li>

            <li class="<?php echo ($level5_5); ?>">
                <a href="__GROUP__/System/log">
                    日志查询
                </a>
                <b class="arrow"></b>
            </li>

            <!-- <li class="<?php echo ($level5_6); ?>">
                    <a href="__GROUP__/System/manual">
                            手动应急管理
                    </a>
                    <b class="arrow"></b>
            </li> -->

            <!--							<li class="<?php echo ($level5_7); ?>">
                                                                            <a href="__GROUP__/System/failedreason">
                                                                                    失败原因管理
                                                                            </a>
                                                                            <b class="arrow"></b>
                                                                    </li>-->
            <!--                                                        <li class="<?php echo ($level58); ?>">
                                                                            <a href="__GROUP__/Live/timeManager">
                                                                                    预通占用
                                                                            </a>
                                                                            <b class="arrow"></b>
                                                                    </li>-->

        </ul>
    </li>
    <!-- menu level5 End-->

    <!-- menu level6 -->
    <li class="<?php echo ($level6); ?>">
        <a href="#" class="dropdown-toggle">
            <i class="menu-icon fa fa-users"></i>
            <span class="menu-text">权限管理</span>
            <b class="arrow fa fa-angle-down"></b>
        </a>
        <b class="arrow"></b>
        <ul class="submenu">	
            <li class="<?php echo ($level6_1); ?>">
                <a href="__GROUP__/RBAC/user">
                    人员管理
                </a>
                <b class="arrow"></b>
            </li>

            <li class="<?php echo ($level6_2); ?>">
                <a href="__GROUP__/RBAC/role">
                    角色权限管理
                </a>
                <b class="arrow"></b>
            </li>

            <li class="<?php echo ($level6_3); ?>">
                <a href="__GROUP__/RBAC/node">
                    节点管理
                </a>
                <b class="arrow"></b>
            </li>
        </ul>
    </li>
    <!-- menu level6 End-->


</ul><!-- /.nav-list -->

            <!-- #section:basics/sidebar.layout.minimize -->
            <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
                <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
            </div>

            <!-- /section:basics/sidebar.layout.minimize -->
            <script type="text/javascript">
                try {
                ace.settings.check('sidebar', 'collapsed')
                } catch (e) {
                }
            </script>
        </div>

        <!-- /section:basics/sidebar -->
        <div class="main-content">
            <!-- #section:basics/content.breadcrumbs -->
            <div class="breadcrumbs" id="breadcrumbs">
                <script type="text/javascript">
                    try {
                    ace.settings.check('breadcrumbs', 'fixed')
                    } catch (e) {
                    }
                </script>
                <!-- /section:basics/content.searchbox -->
            </div>
            <!-- /section:basics/content.breadcrumbs -->
            <div class="page-content">
                <div class="page-content-area">
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="table-header" style="background-color: blueviolet" >
                                        <i class="ace-icon fa fa-table"></i>&nbsp&nbsp书记员监控(显示当天直播）
                                    </div>
                                    <div>
                                        <table id="table1" class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>庭审状态</th> 
                                                    <th>案号名称</th>
                                                    <th>审判组织成员</th>
                                                    <th>诉讼参与方</th>
                                                    <th>开庭与闭庭时间</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr>
                                                    <td><h4 style="color:red;"><?php echo ($vo["now"]); ?></h4></td> 
                                                    <td><h4><?php echo ($vo["P_CaseName"]); ?></h4></td>
                                                    <td><h4><?php echo ($vo["P_JudgeGroup"]); ?></h4></td>
                                                    <td><h4><?php echo ($vo["P_PartyGroup"]); ?></h4></td>
                                                    <td><h4><?php echo ($vo["P_StartTime"]); ?> <br><span style="color: red">--------</span><br/><?php echo ($vo["P_EndTime"]); ?></h4>
                                                    </td>
                                                    <td width="25%">
                                                <?php if($vo["P_LiveStatus"] == 1): ?><button type="submit" class="btn btn-primary" onclick="media('1', <?php echo ($vo["P_ID"]); ?>)"><h3>开庭</h3></button>
                                                <?php elseif($vo["P_LiveStatus"] == 2): ?>
                                                    <button type="submit" class="btn btn-success"  onclick="media('2', <?php echo ($vo["P_ID"]); ?>)"><h3>休庭</h3></button>
                                                    <button type="submit" class="btn btn-warning"  onclick="media('3', <?php echo ($vo["P_ID"]); ?>)"><h3>结束</h3></button>
                                                <?php elseif($vo["P_LiveStatus"] == 3): ?>
                                                    <button type="submit" class="btn btn-info"  onclick="media('11', <?php echo ($vo["P_ID"]); ?>)"><h3>恢复</h3></button>
                                                <?php elseif($vo["P_LiveStatus"] == 4): ?>
                                                    <h4 style="color:brown;">直播已结束</h4><?php endif; ?>
                                                </td>

                                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                            </tbody>
                                        </table>
                                    </div>



                                </div><!-- end col -->
                            </div><!-- end row -->
                            <!-- PAGE CONTENT ENDS -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content-area -->
            </div><!-- /.page-content -->
        </div><!-- /.main-content -->

        	<div class="footer">
		<div class="footer-inner">
			<!-- #section:basics/footer -->

			<!-- /section:basics/footer -->
		</div>
	</div>
			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='__PUBLIC__/js/jquery.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='__PUBLIC__/js/jquery1x.min.js'>"+"<"+"/script>");
</script>
<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='__PUBLIC__/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="__PUBLIC__/js/bootstrap.min.js"></script>

		<!-- page specific plugin scripts -->
		<script src="__PUBLIC__/js/jquery.dataTables.min.js"></script>
		<script src="__PUBLIC__/js/jquery.dataTables.bootstrap.js"></script>
		
		<!-- ace scripts -->
		<script src="__PUBLIC__/js/ace-elements.min.js"></script>
		<script src="__PUBLIC__/js/ace.min.js"></script>
		<script src="__PUBLIC__/layer/layer.js"></script>
		<script src="__PUBLIC__/laypage/laypage.js"></script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			jQuery(function($){
				//任务单查询层
				$("#search").click(function(){
					 layer.open({
						type: 2,
			            title: '申请单查询',
			            shadeClose: true,
			            shade: false,
			            maxmin: true, //开启最大化最小化按钮
			            area: ['893px', '600px'],
					    content:  "/index.php/Public/searchPlaylist",
					});
				});
				
			/* 	//任务单统计层
				$("#collect").click(function(){
					 layer.open({
						type: 2,
			            title: '任务单统计',
			            shadeClose: true,
			            shade: false,
			            maxmin: true, //开启最大化最小化按钮
			            area: ['893px', '600px'],
					    content:  "/index.php/Public/collectPlaylist",
					});
				}); */
				
				//日志层
				$("#log").click(function(){
					window.location.href= "/index.php/Personal/myLog";
				});
				
			});
		</script>
        <script type="text/javascript">
//            setTimeout(function(){location.reload()},120000); //指定1秒刷新一次
            function coverit(){
                var cover = document.getElementById("cover");
                var covershow = document.getElementById("coverShow");
                cover.style.display = 'block';
                covershow.style.display = 'block';
//                setTimeout("showTime()", 30000);
            }
            function showTime() {
            var cover = document.getElementById("cover");
                    var covershow = document.getElementById("coverShow");
                    cover.style.display = '';
                    covershow.style.display = '';
            }

            window.onload = function(){
                $('#table1').dataTable({
                stateSave: true, //保存表格状态
                        language: {
                        "url": "/Public/font/zh_CN.txt"
                        }
                });
            };
            function media(type, pid){
//            setTimeout(function(){timeout(type, pid)}, 120000); //两分钟后再调用接口
//                    coverit();
                timeout(type, pid);
            }

            function timeout(type, pid){
                $.ajax({
                url: '/index.php/Live/M_operate?type=' + type + '&pid=' + pid,
                        dataType: 'json',
                        type: 'GET',
                        async: false,
                        success: function (msg) {
                            layer.alert(msg.info, {icon: 1}, function (index) {
    //                        showTime()
                                    location.reload();
                            });
                        }
                });
            }

        </script>
</body>
</html>