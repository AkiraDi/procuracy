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
    tr{height:50px;}		
</style>		
<body style="background-color:white;padding:15px;font-size:16px">
    <form method="post" action="__GROUP__/CaseInput/editCaseHandler" >
        <div class="page-header">
            <h1>案件详情</h1>
        </div><!-- /.page-header -->
        <table width="100%" border="0" cellpadding="2" cellspacing="0">
            <tr>
                <td>
                    <label><span style='color:red;'>*</span>检察院名称：</label>
                    <select class="chosen-select" id="selectCourt1" data-placeholder="检察院名称..."  name="P_CourtIn" style="width:200px;" value="<?php echo ($playlist["P_CourtIn"]); ?>" disabled="disabled">
                        <option value="">  </option>
                        <?php if(is_array($courts)): $i = 0; $__LIST__ = $courts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php if($playlist["P_CourtIn"] == $vo['C_ID']): ?><option value="<?php echo ($vo["C_ID"]); ?>" selected="selected"><?php echo ($vo["C_Name"]); ?></option><?php endif; ?><?php endforeach; endif; else: echo "" ;endif; ?>																					
                    </select>
                </td>
                <td>
                    <label><span style='color:red;'>*</span>听证室名称：</label>
                    <select class="chosen-select" id="selectCourtRoom" data-placeholder="听证室名称..." name="P_CourtRoomIn" style="width:200px;" value="<?php echo ($playlist["P_CourtRoomIn"]); ?>" disabled="disabled">
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>开庭时间：</label><input type="text" name="P_StartTime" style="width:300px" onfocus="WdatePicker({skin: 'whyGreen', dateFmt: 'yyyy-M-d H:mm:ss'})" value="<?php echo ($playlist["P_StartTime"]); ?>" disabled="disabled"></td>
                <td><label>结束时间：</label><input type="text" name="P_EndTime" style="width:300px" onfocus="WdatePicker({skin: 'whyGreen', dateFmt: 'yyyy-M-d H:mm:ss'})" value="<?php echo ($playlist["P_EndTime"]); ?>" disabled="disabled"></td>
            </tr>
            <tr>
                <td><label>案件编号：</label><input type="text" name="P_CaseName" style="width:200px" value="<?php echo ($playlist["P_CaseName"]); ?>" disabled="disabled"></td>
                <td><label>庭次：</label><input type="text" name="P_CourtNo" style="width:200px" value="<?php echo ($playlist["P_CourtNo"]); ?>" disabled="disabled"></td>
            </tr>
            <tr>
                <td colspan="3"><label>案件详情：</label><textarea class="form-control" id="form-field-8" rows="3" name="P_CaseDetail" disabled="disabled"><?php echo ($playlist["P_CaseDetail"]); ?></textarea></td>
            </tr>
            <tr>
                <td><label>承办部门：</label><input type="text" name="P_Department" style="width:200px" value="<?php echo ($playlist["P_Department"]); ?>" disabled="disabled"></td>
                <td><label>承办人：</label><input type="text" name="P_Contractor" style="width:200px" value="<?php echo ($playlist["P_Contractor"]); ?>" disabled="disabled"></td>
            </tr>
            <tr>
                <td><label>案件延时分钟数：</label><input type="text" name="P_DelayMin" style="width:200px" value="<?php echo ($playlist["P_DelayMin"]); ?>" disabled="disabled"></td>
                <td><label>案件类别：</label><input type="text" name="P_Catagory" style="width:200px" value="<?php echo ($playlist["P_Catagory"]); ?>" disabled="disabled"></td>
            </tr>
            <tr>
                <td><label>审判组织成员：</label><input type="text" name="P_JudgeGroup" style="width:200px" value="<?php echo ($playlist["P_JudgeGroup"]); ?>" disabled="disabled"></td>
                <td><label>诉讼参与方：</label><input type="text" name="P_PartyGroup" style="width:200px" value="<?php echo ($playlist["P_PartyGroup"]); ?>" disabled="disabled"></td>
            </tr>
        </table>
        <input type="hidden" name="P_ID"  value="<?php echo ($playlist["P_ID"]); ?>" />
    </form>

    <script src="__PUBLIC__/js/jquery.min.js"></script>
    <script src="__PUBLIC__/js/chosen.jquery.min.js"></script>
    <script src="__PUBLIC__/My97DatePicker/WdatePicker.js"></script>
    <script type="text/javascript">
                        jQuery(function ($) {
                            //清空检察院
//                            $('#selectCourt1').prop('selectedIndex', 0);
                            //选择入流检察院时，查询该检察院所有的听证室，填入听证室select
                            $('#selectCourt1').change(function () {
                                $("#selectCourtRoom").chosen("destroy");
                                var courtId = $(this).val();
                                $.ajax({
                                    url: '/index.php/ApplyTask/searchCourtRoom?C_ID=' + courtId,
                                    dataType: 'json',
                                    type: 'GET',
                                    async: false,
                                    success: function (msg) {
                                        //删除所有option
                                        $("#selectCourtRoom  option").remove();
                                        $("#selectCourtRoom").append("<option value=''>  </option>");
                                        for (i = 0; i < msg.data.length; i++) {
                                            //循环插入option
                                            if( <?php echo ($playlist["P_CourtRoomIn"]); ?> == msg.data[i].CR_ID){
                                                $("#selectCourtRoom").append("<option value='" + msg.data[i].CR_ID + "' selected='selected'>[" + msg.data[i].CR_Name + "]  " + msg.data[i].CR_URL + "  端口:" + msg.data[i].CR_Port + "</option>");
                                            }else{
                                                $("#selectCourtRoom").append("<option value='" + msg.data[i].CR_ID + "'>[" + msg.data[i].CR_Name + "]  " + msg.data[i].CR_URL + "  端口:" + msg.data[i].CR_Port + "</option>");
                                            }
                                            
                                        }
                                    }
                                });
                                $("#selectCourtRoom").chosen();
                            });

                            $('.chosen-select').chosen();
                            
                            $("#selectCourt1").trigger("change")
                            
                        });
    </script>
</body>
</html>