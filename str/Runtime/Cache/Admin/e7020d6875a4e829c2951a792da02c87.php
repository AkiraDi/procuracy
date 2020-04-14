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

<body class="no-skin" style="background-color: white;">
    <br>
    <form class="nav-search" id="nav-search" method="post" action="__GROUP__/Public/searchPlaylist">
        <p>
            <input type="hidden" name="isSearch" value="1" />
            <input type="text" placeholder="开始时间" name="startTime" onclick="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm:ss'})" value="<?php echo ($startTime); ?>" />
            <input type="text" placeholder="结束时间" name="endTime" onclick="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm:ss'})" value="<?php echo ($endTime); ?>"  />
            <button type="submit" class="btn btn-success btn-sm">查询<i class="ace-icon fa fa-search icon-on-right bigger-110"></i></button>
        </p>
    </form>
    <div class="row">
        <div class="col-xs-12">
            <div class="table-header">
                <i class="ace-icon fa fa-table"></i>&nbsp&nbsp查询结果
            </div>
            <!-- <div class="dataTables_borderWrap"> -->
            <div>
                <table id="table1" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>开始时间</th>
                            <th>结束时间</th>
                            <th>案件名称</th>
                            <th>案件状态</th>
                            <th>案件审核状态</th>
                            <th>入流检察院</th>
                            <th>入流听证室</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($playlist)): $i = 0; $__LIST__ = $playlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr>
                            <td><?php echo ($vo["P_StartTime"]); ?></td>
                            <td><?php echo ($vo["P_EndTime"]); ?></td>
                            <td><?php echo ($vo["P_CaseName"]); ?></td>
                            <td><?php echo ($vo["SName"]); ?></td>
                            <td><?php echo ($vo["ASName"]); ?></td>
                            <td><?php echo ($vo["C_Name"]); ?></td>
                            <td><?php echo ($vo["CR_Name"]); ?></td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
            </div><!-- end<div class="dataTables_borderWrap"> -->
        </div><!-- end col -->
    </div><!-- end row -->
    <script src="__PUBLIC__/js/jquery.min.js"></script>
    <script src="__PUBLIC__/js/bootstrap.min.js"></script>

    <!-- page specific plugin scripts -->
    <script src="__PUBLIC__/js/jquery.dataTables.min.js"></script>
    <script src="__PUBLIC__/js/jquery.dataTables.bootstrap.js"></script>

    <!-- ace scripts -->
    <script src="__PUBLIC__/js/ace-elements.min.js"></script>
    <script src="__PUBLIC__/js/ace.min.js"></script>
    <script src="__PUBLIC__/layer/layer.js"></script>
    <script src="__PUBLIC__/laypage/laypage.js"></script>
    <script src="/Public/My97DatePicker/WdatePicker.js"></script>
    <script type="text/javascript">
                jQuery(function($) {
                    $('#table1').dataTable({
                        stateSave: false, //保存表格状态
                        lengthMenu: [8],
                        language: {
                            "url": "/Public/font/zh_CN.txt"
                        }
                    });

                    //未通过标签
                    $("span[class='label label-danger']").click(function() {
                        var id = $(this).attr('id');
                        var val = $(this).attr('value');
                        layer.tips(val, '#' + id, {
                            tips: [2, 'rgb(209,91,71)'],
                        });
                    });

                    //查看
                    $("button[class='btn btn-xs btn-success check']").click(function() {
                        var id = $(this).attr('id');
                        var index = layer.open({
                            type: 2,
                            title: "详细内容",
                            content: '/index.php/MyTask/checkTask?P_ID=' + id,
                            area: ['800px', '850px'],
                        });
                    });

                    //编辑
                    $("button[class='btn btn-xs btn-success edit']").click(function() {
                        var id = $(this).attr('id');
                        var index = layer.open({
                            type: 2,
                            title: "详细内容",
                            content: '/index.php/MyTask/editTask?P_ID=' + id,
                            area: ['800px', '850px'],
                        });
                    });

                    //删除任务
                    $("button[class='btn btn-xs btn-danger']").click(function() {
                        var id = $(this).attr('id');
                        layer.confirm('是否删除该条任务?', {icon: 3, title: '提示'}, function(index) {
                            $.ajax({
                                url: '/index.php/MyTask/delTaskHandler?P_ID=' + id,
                                dataType: 'json',
                                type: 'GET',
                                success: function(msg) {
                                    if (!msg.status) {
                                        //删除失败
                                        layer.alert(msg.info, {icon: 2}, function(index) {
                                            location.reload();
                                        });
                                    } else {
                                        //删除成功
                                        layer.alert(msg.info, {icon: 1}, function(index) {
                                            location.reload();
                                        });
                                    }
                                }
                            });
                        });
                    });

                    //提交任务(未通过审核的任务)
                    $("button[class='btn btn-xs btn-primary']").click(function() {
                        var id = $(this).attr('id');
                        layer.confirm('是否提交该任务?', {icon: 3, title: '提示'}, function(index) {
                            $.ajax({
                                url: '/index.php/MyTask/submitTaskHandler?P_ID=' + id,
                                dataType: 'json',
                                type: 'GET',
                                success: function(msg) {
                                    if (!msg.status) {
                                        //提交失败
                                        layer.alert(msg.info, {icon: 2}, function(index) {
                                            location.reload();
                                        });
                                    } else {
                                        //提交成功
                                        layer.alert(msg.info, {icon: 1}, function(index) {
                                            location.reload();
                                        });
                                    }
                                }
                            });
                        });
                    });

                });
    </script>
</body>
</html>