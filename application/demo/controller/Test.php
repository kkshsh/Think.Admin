<?php
namespace app\demo\controller;

use controller\BasicApi;
use iqy\Collect;
use think\log;

class Test extends BasicApi
{

    public function index(){
		
		$categoryModel = model("category");
		
		$collect = new Collect();
		
		
		
		$data = $collect->start(['url'=>'http://list.iqiyi.com/www/25/-------------4-1-2-iqiyi-1-.html']);
		
		print_r($data);exit;
			
		$this->saveCollect($categoryModel->find(),$data);
		
		exit;
	
		
		
		$category = $categoryModel->where('id',">","4830")->select();
		
		//print_r(json_decode(json_encode($data),true));exit;
		
		foreach($category as $cat){
			
			$data = $collect->start(['url'=>$cat->desc]);
			
			$this->saveCollect($cat,$data);
			
		}
		
		exit;
		
		$data = $collect->start(['url'=>"http://list.iqiyi.com/www/1/----------------iqiyi--.html"]);
		
		//$data = $collect->start(['url'=>"http://www.iqiyi.com/a_19rrh9ue1h.html#vfrm=2-4-0-1"]);
		
		Log::record(print_r($data,1));
		
		//print_r($data);
		
	}
	
	public function saveCollect($category,$data){
		
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
