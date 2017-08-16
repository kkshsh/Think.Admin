<?php
namespace app\admin\controller\lrj;

use controller\BasicAdmin;
use service\DataService;
use service\NodeService;
use service\ToolsService;
use think\Db;
use think\View;

class Videos extends BasicAdmin
{
	
	use \app\admin\traits\Iud;
	
	protected function _model(){
		
		return model('videos');
		
	}
	
    public function index(){
		
        $category = $this->_model();
		
		$list = $category->where('categoryid',input('get.cid','0'))->select();
		
		return view('', [
				'title' => '分类管理',
				'list' => $list,
			]);
    }
	
	/**
     * 添加
     */
    public function add(){
		
		$model = $this->_model();
		
		if($this->request->isPost()){
			
			$result = $model->save(input('post.'));
			
			$result !== false ? $this->success('恭喜, 数据保存成功!', '') : $this->error('数据保存失败, 请稍候再试!');
			
		}else{
			
			return view('form', ['title' => '分类管理','info' => ['categoryid'=>input('get.cid')],]);
			
		}
		
    }

}
