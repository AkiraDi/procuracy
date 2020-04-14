<?php

class UserModel extends Model {

    protected $_validate = array(
        array('M_Name', 'require', '用户名必须填写！'), //默认情况下用正则进行验证
        array('M_Name', '', '用户名已经存在！', 0, 'unique', 1), //默认情况下用正则进行验证
        array('M_Password', 'require', '密码必须填写！'),
//        array('M_Name', 'checkTime', '开始时间不能小于当前时间！', 1, 'callback'),
            //array('name','','帐号名称已经存在！',0,’unique’,1), // 在新增的时候验证name字段是否唯一
    );

//    public function checkTime() {
//        var_dump(11111);exit;
//        return true;
//    }

}
