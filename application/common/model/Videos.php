<?php

namespace app\common\model;

class Videos extends Base
{
	
	//protected $pk = 'id';
	
	// 设置当前模型对应的完整数据表名称
    protected $table = 'videos_lrj_iqy';
	
	public function category(){
		
		return $this->belongsTo('Category','categoryid','id');
		
	}

}
