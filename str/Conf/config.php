<?php
return array(
    //'配置项'=>'配置值'
     'APP_DEBUG' => true, //调试模式
     'SHOW_PAGE_TRACE' => true, //页面追踪
     'SHOW_ERROR_MSG' => true, // 显示错误信息
    'TMPL_CACHE_ON' => false, //关闭缓存
    'APP_GROUP_LIST' => 'Admin,Home',
    'DEFAULT_GROUP' => 'Admin',
    'DEFAULT_MODULE' => 'MyTask', //默认模块
    'DB_TYPE' => 'mysqli', // 数据库类型
    'DB_CHARSET' => 'utf8',
    'DB_NAME' => 'procuracy', // 数据库名称
    'DB_HOST' => '192.168.24.66', // 数据库服务器地址
    'DB_USER' => 'root', // 数据库用户名
    'DB_PWD' => 'fXL2bO$RQgaRS^lH', // 数据库密码
    'DB_PORT' => '3306', // 数据库端口
    'DB_PREFIX' => 't_', // 数据表前缀 
    'LOGIN_GATEWAY' => 'Admin-Public/login', // 默认认证网关
    'TMPL_CACHE_ON' => false, // 默认开启模板缓存
    'TMPL_ACTION_ERROR' => 'Public:error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Public:success', // 默认成功跳转对应的模板文件
    'LOG_RECORD' => true, // 开启日志记录
    'LOG_RECORD_LEVEL' => array('SQL'),
    'TOKEN_ON' => false, // 是否开启令牌验证
    'TOKEN_NAME' => '__hash__', // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE' => 'md5', //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => true, //令牌验证出错后是否重置令牌 默认为true
//    'STREAM_SERVER_1' => 'http://192.168.188.104:8800', //解码器地址1
//    'STREAM_SERVER_2' => 'http://192.168.188.104:8800', //解码器地址2
//    'STREAM_SERVER_3' => 'http://192.168.188.104:8800', //解码器地址3
//    'STREAM_SERVER_4' => 'http://192.168.188.104:8800', //解码器地址4
//    'STREAM_SERVER_5' => 'http://192.168.188.104:8800', //解码器地址5
//    'STREAM_SERVER_6' => 'http://219.239.86.246:9000', //解码器地址6 <!--2018.3.5添加新的解码器 -->   
    'BACKUP_RTSP' => '', //垫片地址，格式例如rtsp://139.56.50.21:554/m1a4
    'ENDING_RTSP' => '', //结束画面地址
    'Verify_ON' => true, //是否需要审核 (true需要; false不需要)
    //RBAC 配置
    'USER_AUTH_ON' => true, //是否需要认证
    'USER_AUTH_TYPE' => 2, ////验证方式（1、登录验证；2、实时验证）
    'USER_AUTH_KEY' => 'uid', // 认证识别号
    'USER_AUTH_MODEL' => 'user', //模型实例（用户表名）
    'RBAC_ADMIN' => 'admin', //管理员账户名称
    'ADMIN_AUTH_KEY' => 'administrator', //超级管理员
    'NOT_AUTH_MODULE' => 'Public,Personal', //无需认证模块
    'NOT_AUTH_ACTION' => 'searchCourtRoom,searchOutPID,getTasks,exportToExcel', //无需认证方法
    'USER_AUTH_GATEWAY' => 'Admin-Public/login', //认证网关
    //RBAC_DB_DSN  数据库连接DSN
    'RBAC_ROLE_TABLE' => 't_role', //角色表名称
    'RBAC_USER_TABLE' => 't_role_user', //用户和角色对应关系表名称
    'RBAC_ACCESS_TABLE' => 't_access', //权限分配表名称
    'RBAC_NODE_TABLE' => 't_node', // 权限表名称
    
    //yms指令接口 授权码
    'YMSCODE'=>'yms',
    'YMSAPP'=>'4621d373cade4e83',
    'YMSURL'=>'http://192.168.23.128:28300/',//测试
    //延时接口
    'DELAYURL'=>'http://192.168.23.128:28040/', //测试
    
);
?>