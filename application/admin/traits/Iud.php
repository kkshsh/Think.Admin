<?php

namespace app\admin\traits;

trait Iud
{
    /**
     * 添加
     */
    public function add(){
		
		$model = $this->_model();
		
		if($this->request->isPost()){
			
			$result = $model->save(input('post.'));
			
			$result !== false ? $this->success('恭喜, 数据保存成功!', '') : $this->error('数据保存失败, 请稍候再试!');
			
		}else{
			
			return view('form', ['title' => '分类管理','info' => [],]);
			
		}
		
    }

    /**
     * 编辑
     */
    public function edit(){
		
		$model = $this->_model();
		
		$info = $model->find(input('request.id',0));
		
		if($this->request->isPost()){
			
			$result = $info->save(input('post.'));
			
			$result !== false ? $this->success('恭喜, 数据保存成功!', '') : $this->error('数据保存失败, 请稍候再试!');
			
		}else{
			
			$info = $info->toArray();
			
			return view('form', ['title' => '分类管理','info' => $info,]);
			
		}
		
    }
	
	/**
     * 删除
     */
    public function del()
    {
		
		$model = $this->_model();
		
		if ($model->destroy(input('request.id'))) {
			
            $this->success("删除成功!", '');
			
        }
		
        $this->error("删除失败, 请稍候再试!");
		
    }
}
