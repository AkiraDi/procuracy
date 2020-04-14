<?php

class CourtroomModel extends Model {
    protected $_validate = array(
        array('CR_Name', 'require', '听证室名称必须填写！'), //默认情况下用正则进行验证
        array('CR_Belongs', 'require', '所属检察院必须填写！'),
            //array('name','','帐号名称已经存在！',0,’unique’,1), // 在新增的时候验证name字段是否唯一
    );
}
