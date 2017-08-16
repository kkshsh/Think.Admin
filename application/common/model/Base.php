<?php

namespace app\common\model;

use think\Model;

class Base extends Model
{
	
	// 设置当前模型的数据库连接
    protected $connection = "db_gsj";
	
}
