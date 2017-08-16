<?php
namespace iqy;

use CURLFile;

class Collect
{
	
	public function start($param){
		
		$param['content'] = $this->get($param['url']);
		
		return $this->analysis($param);
		
	}
	
	public function contentType($param){
		
		$content = $param['content'];
		
		$pattern = '您访问的页面不存在';
		
		if(!$content || mb_strpos($content,$pattern)){
			
			return "error404";
			
		}
		
		return "pass";
	}
	
	public function urlType($param){
		
		$url = $param['url'];
		
		$pattern='/\/playlist/';
		
		if(preg_match($pattern, $url)){
			
			return "playList";
			
		}
		
		$pattern='/www\.iqiyi\.com\/w\_/';
		
		if(preg_match($pattern, $url)){
			
			return "videoW";
			
		}
		
		$pattern='/www\.iqiyi\.com\/a\_/';
		
		if(preg_match($pattern, $url)){
			
			return "videoA";
			
		}
		
		$pattern='/list\.iqiyi\.com\/www\//';
		
		if(preg_match($pattern, $url)){
			
			return "iqyList";
			
		}
		
		$pattern='/www\.iqiyi\.com\/dianshiju\/\d+\//';
		
		if(preg_match($pattern, $url)){
			
			return "dianshijuVideo";
			
		}
		
		$pattern='/www\.iqiyi\.com\/dianshiju\//';
		
		if(preg_match($pattern, $url)){
			
			return "dianshiju";
			
		}
		
		return 'unknown';
		
	}
	
	public function analysis($param){
		
		$type = $this->contentType($param);
		
		if($type == 'pass'){
			
			$type = $this->urlType($param);

		}
		
		return $this->$type($param);
		
	}
	
	public function videoW($param){
		
	}
	
	public function unknown($param){
		
	}
	
	public function dianshijuVideo($param){
		
	}
	
	public function error404(){
		
		return [];
		
	}
	
	public function dianshiju($param){
		
		$url = $param['url'];
		
		$content = $param['content'];
		
		$rule='/title="([^"]*?)" href="([^"]*?)">[^<]*<\/a><\/p><p><\/p><\/li>/is';
		
		preg_match_all($rule,$content,$array,PREG_SET_ORDER);  
		
		$list = [];
		
		foreach($array as $key=>$v){
			
			$url = $v[2];
			
			$title = trim($v[1]);
			
			$list[$key] = $this->typeFormat(['url'=>$url,'title'=>$title]);
			
		}
		
		return $list;
		
	}
	
	public function videoA($param){
		
		$url = $param['url'];
		
		$content = $param['content'];
		
		$rule = '/Q\.PageInfo\.playPageInfo[^}]+=[^}]+albumId: (\d+),[^}]+tvId: \d+,[^}]+sourceId: (\d+),[^}]+cid: (\d+),[^}]+videoLine: 0,[^}]+position: \'album\'[^}]+};/';
		 
		//$rule = '/data-trailer-sidoraid="(\d+)"/';
		
		preg_match_all($rule,$content,$array,PREG_SET_ORDER);

		if($array){
			
			$albumId = $array[0][1];
			
			$sourceId = $array[0][2];
			
			$cid = $array[0][3];
		
			$list = $this->getVideoAList($albumId,$sourceId,$cid);
			
		}else{
			
			$rule = '/<p class="site-piclist\_info\_title">.*?<a href="(.*?)" rseat="\d+_title" target="\_blank">(.*?)<\/a>.*?<\/p>/is';
		
			preg_match_all($rule,$content,$array,PREG_SET_ORDER);
			
			$list = [];
			
			foreach($array as $v){
				
				$url = $v[1];
				
				$title = trim($v[2]);
				
				$list[] = $this->typeFormat(['url'=>$url,'title'=>$title]);
			}
			
		}
		
		return 	$list;
		
	}
	
	public function getVideoAList($albumId,$sourceId,$cid){
		
		if($sourceId){
			
			$pageUrl = "http://cache.video.iqiyi.com/jp/sdvlst/%s/%s/";
			
			$list = [];
			
			$url = sprintf($pageUrl,$cid,$sourceId);
			
			$pageContent = $this->get($url);
			
			$pageContent = substr($pageContent,13);
			
			$array = json_decode($pageContent,true);
			
			$vlist = $array['data'];
			
			foreach($vlist as $a){
				
				$url = $a['vUrl'];
				
				$title = trim($a['aAlias']);
				
				$list[] = $this->typeFormat(['url'=>$url,'title'=>$title]);
				
			}
			
		}else{
			
			$pageUrl = "http://cache.video.iqiyi.com/jp/avlist/%s/%s/%s/";
		
			$page = 1;
			
			$list = [];
			
			while(true){
				
				$url = sprintf($pageUrl,$albumId,$page,50);
				
				$pageContent = $this->get($url);
				
				$pageContent = substr($pageContent,13);
				
				$array = json_decode($pageContent,true);
				
				$count = $array['data']['allNum'];
				
				$vlist = $array['data']['vlist'];
				
				foreach($vlist as $a){
					
					$url = $a['vurl'];
					
					$title = trim($a['vn']);
					
					$list[] = $this->typeFormat(['url'=>$url,'title'=>$title]);
					
				}
				
				if($page * 50 > $count){
					
					break;
					
				}else{
					
					$page ++ ;
					
				}
				
			}
			
		}
		
		return $list;
	}
	
	public function iqyList($param){
		
		$content = $param['content'];
		
		//$rule = '/href="([^\"]*?)"[^>]*?class="site-piclist\_pic\_link"[^>]*?target="\_blank">[^>]*?<img[^>]*?width=".*?"[^>]*?height=".*?"[^>]*?rseat=".*?"[^>]*?alt=".*?"[^>]*?title="([^\"]*?)"[^>]*?src.*?=.*?"([^\"]*?)"[^>]*?\/>/is';
		//$rule='/rseat="bigTitle".*?href="(.*?)".*?target="\_blank">(.*?)<\/a>/is';
		$rule = '/\"([^\"]*?)\"[^\"]*?class=\"site-piclist\_pic\_link\"[^>]*?>[^<]*?<img[^>]*?title=\"([^\"]*?)\"[^>]*?src[^=]*?=[^\"]*?"([^\"]*?)"[^>]*?\/>/is';
		preg_match_all($rule,$content,$array,PREG_SET_ORDER); 		
		
		$list = [];
		
		foreach($array as $key=>$v){
			
			$url = $v[1];
			
			$title = trim($v[2]);
			
			$icon = trim($v[3]);
			
			$list[$key] = $this->typeFormat(['url'=>$url,'title'=>$title,'icon'=>$icon]);
			
		}
		
		if(!$list){
			
			return [];
			
		}
		
		
		$maxPage = isset($param['maxPage']) ? $param['maxPage'] : 2;
		
		$url = $param['url'];
		
		$page = preg_replace("/(.*?)([^\-]*)(-[^\-]*-iqiyi-[^\-]*-\.html.*)/is", '\\2', $url);
		
		if(!$page){
			$page = 1;
		}
		
		$page = $page + 1;
		
		if($page > $maxPage){
			
			return $list;
			
		}else{
			
			$order = preg_replace("/(.*?)([^\-]*)(-[^\-]*-[^\-]*-iqiyi-[^\-]*-\.html.*)/is", '\\2', $url);
			
			if($order){
				
				$url = preg_replace("/(.*?)([^\-]*)(-[^\-]*-iqiyi-[^\-]*-\.html.*)/is", '\\1%s\\3', $url);
		
				$url = sprintf($url,$page);
				
			}else{
				
				$url = preg_replace("/(.*?)([^\-]*)(-)([^\-]*)(-[^\-]*-iqiyi-[^\-]*-\.html.*)/is", '\\1%s\\3%s\\5', $url);
		
				$url = sprintf($url,11,$page);

			}
			
			$next = $this->start(['url'=>$url]);
			
			if($next){
				
				return array_merge($list,$next);
				
			}else{
				
				return $list;
				
			}
			
		}
		
	}
	
	public function typeFormat($param){
		
		$url = $param['url'];
		
		$title = $param['title'];
		
		$type = $this->urlType($param);
		
		if($type == 'videoW'){
				
				$res = [
					'type'=>$type,
					'url'=>$url,
					'title'=>$title,
				];
				
		}else if( in_array($type,['playList','iqyList','videoA','dianshiju'])  ){
				
				$res = [
					'type'=>$type,
					'url'=>$url,
					'title'=>$title,
					'icon'=>isset($param['icon'])?$param['icon']:'',
					'list'=>$this->start(['url'=>$url])
				];
				
		}else{
				
				$res = [
					'type'=>$type,
					'url'=>$url,
					'title'=>$title,
				];
				
		}
			
		return $res;
	}
	
	public function playList($param){
		
		$url = $param['url'];
		
		$content = $param['content'];
		
		$rule='/<p class="site-piclist_info_title"><a href="([^"]*?)"[^>]*?>(.*?)<\/a>[^<]*?<\/p>/is';
		
		preg_match_all($rule,$content,$array,PREG_SET_ORDER);  
		
		$list = [];
		
		foreach($array as $key=>$v){
			
			$url = $v[1];
			
			$title = trim($v[2]);
			
			$list[$key] = $this->typeFormat(['url'=>$url,'title'=>$title]);
			
		}
		
		return $list;
		
	}

    /**
     * HTTP GET 请求
     * @param string $url 请求的URL地址
     * @param array $data GET参数
     * @param int $second 设置超时时间（默认30秒）
     * @param array $header 请求Header信息
     * @return bool|string
     */
    public static function get($url, $data = [], $second = 30, $header = [])
    {
		
		//echo $url;
		//echo "\n";
		
		try 
		{
			
			$strm = stream_context_create(['http' => ['timeout' => 5]]); 
			
			$cnt = 0;
			
			while($cnt < 3 && ($res = file_get_contents($url, FALSE, $strm)) == ''){
				
				//echo $url;
				
				//echo "\n";
				
				$cnt ++;
				
			}
			
			if(!$res){
				
				$res = '';
				
			}
			
		} 
		catch(\Exception $e){
			
			$res = '';
			
		}
		
		return $res;
		
        if (!empty($data)) {
            $url .= (stripos($url, '?') === false ? '?' : '&');
            $url .= (is_array($data) ? http_build_query($data) : $data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, $second);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        !empty($header) && curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        self::_setSsl($curl, $url);
        $content = curl_exec($curl);
        $status = curl_getinfo($curl);
        curl_close($curl);
        return (intval($status["http_code"]) === 200) ? $content : false;
    }

    /**
     * POST 请求（支持文件上传）
     * @param string $url HTTP请求URL地址
     * @param array|string $data POST提交的数据
     * @param int $second 请求超时时间
     * @param array $header 请求Header信息
     * @return bool|string
     */
    static public function post($url, $data = [], $second = 30, $header = [])
    {
        self::_setUploadFile($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, $second);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        !empty($header) && curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        self::_setSsl($curl, $url);
        $content = curl_exec($curl);
        $status = curl_getinfo($curl);
        curl_close($curl);
        return (intval($status["http_code"]) === 200) ? $content : false;
    }

    /**
     * 设置SSL参数
     * @param $curl
     * @param string $url
     */
    private static function _setSsl(&$curl, $url)
    {
        if (stripos($url, "https") === 0) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSLVERSION, 1);
        }
    }

}
