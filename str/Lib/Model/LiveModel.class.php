<?php

class LiveModel extends Model {

    protected $name = 'live';
    protected $_validate = array(
        array('L_Channel', 'require', '直播频道号必须选择！'),
        array('L_PUSHURL', 'require', '直播频道号必须选择！'),
        array('L_PULLURL', 'require', '直播频道号必须选择！'),
//			array('L_Comment','require','为了区分出流检察院，出流描述必须填写！'),
//			array('L_PID','','PID重复！',0,'unique',1), //默认情况下用正则进行验证
//			array('L_PID','checkCount','出流数量不能超过40路！',1,'callback',1), // 自定义函数验
//			array('L_Channel','checkAddChannel','该通道已满',2,'callback',1), // 自定义函数验
//			array('L_Channel','checkEditChannel','该通道已满',2,'callback',2), // 自定义函数验
            //array('name','','帐号名称已经存在！',0,’unique’,1), // 在新增的时候验证name字段是否唯一
    );

    //检查当前选择的复用通道数，不能多于40个
    public function checkCount() {
        $Live = M("live");
        $count = $Live->where("L_Decoder='$_POST[L_Decoder]'")->count();
        if ($count >= 40) {
            return false;
        } else {
            return true;
        }
    }

    //添加时检查当前选择的复用通道数，不能多于12个（2018.3.8 原定义10个因为加码器不够用所以将复用通道改为12路）
    public function checkAddChannel($L_Channel) {
        $Live = M("live");
        $count = $Live->where("L_Channel='$L_Channel' and L_Decoder='$_POST[L_Decoder]'")->count();
        if ($count >= 13) {
            return false;
        } else {
            return true;
        }
    }

    //编辑时检查当前选择的解码器的复用通道数，不能多于12个（2018.3.8 原定义10个因为加码器不够用所以将复用通道改为12路）
    public function checkEditChannel($L_Channel) {
        $L_ID = $_POST['L_ID'];
        $Live = M("live");
        $live = $Live->where("L_ID=$L_ID")->find();
        if ($L_Channel == $live['L_Channel'] and $live['L_Decoder'] == $_POST['L_Decoder']) {
            return true;
        }
        $count = $Live->where("L_Channel='$L_Channel' and L_Decoder='$_POST[L_Decoder]'")->count();
        if ($count >= 13) {
            return false;
        } else {
            return true;
        }
    }

}
