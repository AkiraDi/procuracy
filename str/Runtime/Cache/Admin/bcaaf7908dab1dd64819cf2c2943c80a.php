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
    <form method="post" action="__GROUP__/CaseInput/handAddCaseHandler" >
        <div class="page-header">
            <h1>案件详情</h1>
        </div><!-- /.page-header -->
        <table width="100%" border="0" cellpadding="2" cellspacing="0">
            <tr>
<!--                <td><label><span style='color:red;'>*</span>检察院名称：</label><input type="text" name="P_CourtName" style="width:200px" value="<?php echo ($_GET['court_code']); ?>"></td>
                <td><label><span style='color:red;'>*</span>听证室名称：</label><input type="text" name="P_CourtHome" style="width:200px" value="<?php echo ($_GET['room']); ?>"></td>-->
                <td>
                    <label><span style='color:red;'>*</span>检察院名称：</label>
                    <select class="chosen-select" id="selectCourt1" data-placeholder="检察院名称..."  name="P_CourtIn" style="width:200px;">
                        <option value="">  </option>
                        <?php if(is_array($courts)): $i = 0; $__LIST__ = $courts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><option value=<?php echo ($vo["C_ID"]); ?>><?php echo ($vo["C_Name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>																					
                    </select>
                </td>
                <td>
                    <label><span style='color:red;'>*</span>听证室名称：</label>
                    <select class="chosen-select" id="selectCourtRoom" data-placeholder="听证室名称..." name="P_CourtRoomIn" style="width:200px;">
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><span style='color:red;'>*</span>开始时间：</label><input type="text" name="P_StartTime" style="width:300px" onfocus="WdatePicker({skin: 'whyGreen', dateFmt: 'yyyy-M-d H:mm:ss'})" required="required"></td>
                <td><label><span style='color:red;'>*</span>结束时间：</label><input type="text" name="P_EndTime" style="width:300px" onfocus="WdatePicker({skin: 'whyGreen', dateFmt: 'yyyy-M-d H:mm:ss'})" required="required"></td>
            </tr>
            <tr>
                <td><label><span style='color:red;'>*</span>案件编号：</label><input type="text" name="P_CaseName" style="width:200px" value="<?php echo ($_GET['case_no']); ?>" required="required"></td>
                <td><label><span style='color:red;'>*</span>庭次：</label><input type="text" name="P_CourtNo" style="width:200px" value="<?php echo ($_GET['tc']); ?>" required="required"></td>
            </tr>
            <tr>
                <td colspan="3"><label><span style='color:red;'>*</span>案件详情：</label><textarea class="form-control" id="form-field-8" rows="3" name="P_CaseDetail" required="required"></textarea></td>
            </tr>
            <tr>
                
                <td><label><span style='color:red;'>*</span>案件类别：</label>
                    <select class="chosen-select" id="selectCourt1" data-placeholder="检察院名称..."  name="P_Catagory" style="width:200px;">
                        <option value="刑事案件"> 刑事案件 </option>		
                        <option value="民事案件"> 民事案件 </option>		
                        <option value="行政案件"> 行政案件 </option>						
                        <option value="公益诉讼"> 公益诉讼 </option>	   
                        <option value="其他案件"> 其他案件 </option>													
												
                    </select>
                
                </td>
                
                <td><label>案件延时分钟数：</label>
                    <select class="chosen-select" id="selectCourt1" data-placeholder="分钟数..."  name="P_DelayMin" style="width:200px;">
                        <option value="0" check> 0分钟 </option>
                        <option value="5"> 5分钟 </option>		
                        <option value="10"> 10分钟 </option>		
                        <option value="15"> 15分钟 </option>																		
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><span style='color:red;'>*</span>承办部门：</label><input type="text" name="P_Department" style="width:200px"required="required"></td>
                <td><label><span style='color:red;'>*</span>承办人：</label><input type="text" name="P_Contractor" style="width:200px" required="required"></td>
            </tr>
            <tr>
                <td id = 'member'><label><span style='color:red;'>*</span>检察人员：</label>
                    <input type="button" onclick="addtr();" id="add" value="添加检察人员"/><br/>
                    <!--<input type="text" name="P_JudgeGroup" style="width:200px" value="<?php echo ($_GET['judge']); ?>" placeholder="书记员:XXX; 法官:XXX">-->
                    <span><input type="text" name="monitor[]" value=""  placeholder="角色"><input type="text" name="worth[]" value="" placeholder="姓名"></span></br>
                
                </td>
                <td id = 'party'><label><span style='color:red;'>*</span>诉讼对象：</label>
                    <input type="button" onclick="addtr1();" id="add1" value="添加诉讼对象"/><br/>
                    <!--<input type="text" name="P_JudgeGroup" style="width:200px" value="<?php echo ($_GET['judge']); ?>" placeholder="书记员:XXX; 法官:XXX">-->
                    <span><input type="text" name="trial[]" value=""  placeholder="诉讼地位"><input type="text" name="name[]" value="" placeholder="姓名"></span></br>
                
                </td>
            </tr>
<!--            <tr>
                <td><label><span style='color:red;'>*</span>诉讼参与方：</label><input type="text" name="P_PartyGroup" style="width:200px" value="<?php echo ($_GET['party']); ?>" placeholder="原告:郑士艳;被告:张斌"></td>
                <td id = 'party'><label><span style='color:red;'>*</span>诉讼对象：</label>
                    <input type="button" onclick="addtr1();" id="add1" value="添加诉讼对象"/><br/>
                    <input type="text" name="P_JudgeGroup" style="width:200px" value="<?php echo ($_GET['judge']); ?>" placeholder="书记员:XXX; 法官:XXX">
                    <span><input type="text" name="trial[]" value=""  placeholder="诉讼地位"><input type="text" name="name[]" value="" placeholder="姓名"></span>
                
                </td>
                
            </tr>-->
            <tr style="height:90px">
                <td colspan="1"></td>
                <td colspan="1"><button class="btn btn-info" type="submit"><i class="ace-icon fa fa-check bigger-110"></i>保存</button></td>
                <!--<td colspan="1"><button class="btn" type="reset"><i class="ace-icon fa fa-undo bigger-110"></i>取消</button></td>-->
            </tr>
        </table>
        <br>
<!--        <input type="hidden" name="P_OutPID"  id="PID2" value="" />
        <input type="hidden" name="P_Decoder"  id="Decoder2" value="" />-->
    </form>

    <script src="__PUBLIC__/js/jquery.min.js"></script>
    <script src="__PUBLIC__/js/chosen.jquery.min.js"></script>
    <script src="__PUBLIC__/My97DatePicker/WdatePicker.js"></script>
    <script type="text/javascript">
        //添加行
        function addtr() {
            var td = document.getElementById("member");
            var span = document.createElement("span");
            var input = document.createElement("input");
            var input2 = document.createElement("input");
            var inputDel = document.createElement("input");
            input2.setAttribute("type", "text");
            input2.setAttribute("required", "required");
            input2.setAttribute("name", "worth[]");
            input2.setAttribute("placeholder", "姓名");
            input.setAttribute("type", "text");
            input.setAttribute("required", "required");
            input.setAttribute("name", "monitor[]");
            input.setAttribute("placeholder", "角色");
            inputDel.setAttribute("type", "button");
            inputDel.setAttribute("value", "删除此组");
            inputDel.setAttribute("onclick", "deltr(this)");

            td.appendChild(span);
            span.appendChild(input);
            span.appendChild(input2);
            span.appendChild(inputDel);

        }
        function addtr1() {
            var td = document.getElementById("party");
            var span = document.createElement("span");
            var input = document.createElement("input");
            var input2 = document.createElement("input");
            var inputDel = document.createElement("input");
            input2.setAttribute("type", "text");
            input2.setAttribute("name", "name[]");
            input2.setAttribute("required", "required");
            input2.setAttribute("placeholder", "姓名");
            input.setAttribute("type", "text");
            input.setAttribute("name", "trial[]");
            input.setAttribute("required", "required");
            input.setAttribute("placeholder", "诉讼地位");
            inputDel.setAttribute("type", "button");
            inputDel.setAttribute("value", "删除此组");
            inputDel.setAttribute("onclick", "deltr(this)");

            td.appendChild(span);
            span.appendChild(input);
            span.appendChild(input2);
            span.appendChild(inputDel);

        }
        function deltr(obj) {
            obj.parentNode.parentNode.removeChild(obj.parentNode);
        }
            
            
        jQuery(function ($) {
            //清空检察院
            $('#selectCourt1').prop('selectedIndex', 0);
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
                            $("#selectCourtRoom").append("<option value='" + msg.data[i].CR_ID + "'>[" + msg.data[i].CR_Name + "]  " + msg.data[i].CR_URL + "  端口:" + msg.data[i].CR_Port + "</option>");
                        }
                    }
                });
                $("#selectCourtRoom").chosen();
            });

            //选择出流检察院时，查询PID
            $('#selectCourt2').change(function () {
                var L_ID = $(this).val();
                $.ajax({
                    url: '/index.php/ApplyTask/searchOutPID?L_ID=' + L_ID,
                    dataType: 'json',
                    type: 'GET',
                    async: false,
                    success: function (msg) {
                        $("#PID1,#PID2").val(msg.data.L_PID);
                        $("#P_CourtOut").val(msg.data.L_CourtName);
                        $("#Decoder1,#Decoder2").val(msg.data.L_Decoder);
                    }
                });
            });

            $('.chosen-select').chosen();

        });
    </script>
</body>
</html>