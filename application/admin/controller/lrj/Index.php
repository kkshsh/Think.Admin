<?php
namespace app\admin\controller\lrj;

use controller\BasicAdmin;

class Index extends BasicAdmin
{
	
	use \app\admin\traits\Iud;
	
	protected function _model(){
		
		return model('category');
		
	}
	
    public function index()
    {
        //$alert = ['type' => 'danger', 'title' => '安全警告', 'content' => '结构为系统自动生成, 状态数据请勿随意修改!'];
		
		$category = model("category");
		
		$all = $category->all();
		/*
			$all = json_decode(json_encode($all),true);
			
			print_r($all);exit;
			
			$list = ToolsService::arr2tree($all, 'id', 'parentid');
			
			print_r($list);exit;
			
			$list = [
			
				
			
			];
		*/
		
        return view('', [
				'title' => '分类管理',
				'nodeList' => $all,
				//'alert' => $alert
			]);
    }
	
	public function getList(){
		
		$category = model("category");
		
		$list = $category->with(['zcat','videos'])->where('parentid',input('get.pid','0'))->select();
		
		return view('', [
				'title' => '分类管理',
				'list' => $list,
				//'alert' => $alert
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
			
			$pid = input('get.pid');
			
			if(!$pid){
				$pid = 0;
			}
			
			return view('form', ['title' => '分类管理','info' => ['parentid'=>$pid],]);
			
		}
		
    }

}
