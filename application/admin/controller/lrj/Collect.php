<?php
namespace app\admin\controller\lrj;

use controller\BasicAdmin;
use iqy\Collect as iqyCollect;
use think\log;

class Collect extends BasicAdmin{
	
	public function index(){
		
		return view('', [
				'title' => '采集地址',
				'data' => input('get.'),
				//'alert' => $alert
			]);
		
	}
	
    public function run(){
		
		set_time_limit(3600*24);
		
		$categoryModel = model("category");
		
		$collect = new iqyCollect();
		
		$cid = input("post.cid");
		
		$url = input("post.url");
		
		$maxPage = input("post.max_page");
		
		if(!$maxPage){
			
			$maxPage = 2;
		}
		
		if(!$cid){
			
			$this->error('请输入id');
            
		}
		
		$category = $categoryModel->find($cid);
		
		if(!$category){
			
			$this->error('分类不存在');
			
		}
			
		$data = $collect->start(['url'=>$url,'maxPage'=>$maxPage]);
		
		$this->saveCollect($category,$data);
		
		$this->success('操作成功', '');
		
	}
	
	protected function saveCollect($category,$data){
		
		Log::record(print_r($data,1));
		
		$saveData = [];
			
		foreach($data as $v){
			
			if(in_array($v['type'],['iqyList','playList','videoA','dianshiju'])){
				
				//分类下有解析出来才插入
				
				if($v['list']){
					
					$model = $category->zcat()->save([
				
						'title'=>$v['title'],
						
						'desc'=>$v['url'],
						
						'icon'=>isset($v['icon'])?$v['icon']:"",
					
					]);
				
					$this->saveCollect($model,$v['list']);
					
				}else{
					
					Log::record('无法解析分类');
					
					Log::record(print_r($v,1));
					
				}
				
			}else{
				
				$saveData[] = [
				
					'title'=>$v['title'],
						
					'url'=>$v['url'],
						
					'storeat'=>'iqiyi',
						
					'extension'=>'mp4',
						
					'password'=>'',
						
					'cookiestr'=>'',
						
					'state'=>'1',
						
				];
				
			}
			
		}
		
		if($saveData){
			
			$category->videos()->saveAll($saveData);
			
		}
		
	}

}
