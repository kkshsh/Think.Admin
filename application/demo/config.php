<?php

// +----------------------------------------------------------------------
// | Think.Admin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/Think.Admin
// +----------------------------------------------------------------------
use think\Env;

return [

	'db_gsj' => [
		// 数据库类型
		'type'        => 'mysql',
		// 服务器地址
		'hostname'    => Env::get('database.hostname'),
		// 数据库名
		'database'    => Env::get('database.database'),
		// 数据库用户名
		'username'    => Env::get('database.username'),
		// 数据库密码
		'password'    => Env::get('database.password'),
		// 数据库编码默认采用utf8
		'charset'     => Env::get('database.charset'),
		// 数据库表前缀
		'prefix'      => Env::get('database.prefix'),
		
		'debug'           => Env::get('database.debug'),
		
		// 是否需要进行SQL性能分析
		'sql_explain'     => Env::get('database.sql_explain'),
		
		'params' => [
		
			\PDO::ATTR_PERSISTENT   => Env::get('database.params_persistent'),
			
		],
	],
];
