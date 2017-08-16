<?php

namespace app\common\model;

class Category extends Base
{
	
	//protected $pk = 'id';
	
	// 设置当前模型对应的完整数据表名称
    protected $table = 'category_lrj_iqy';
	
	public function zcat(){
		
		return $this->hasMany('Category','parentid','id');
		
	}
	
	public function pcat(){
		
		return $this->belongsTo('Category','parentid','id');
		
	}
	
	public function videos(){
		
		return $this->hasMany('Videos','categoryid','id');
		
	}

}
