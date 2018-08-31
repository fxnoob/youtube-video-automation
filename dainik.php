<?php

/******
* 
*
*******/
//including class config /
require_once('config.php');
require_once('yt.php');
 
class dainik extends Processing
{

	function __construct($channel_name , $save_to){
		parent::__construct($channel_name,$save_to);  
	}
	//scraping links on trending page 
	function scrapeLink_trending_only($pageNo=0){ 
		require_once('lib/simple_html_dom.php');
		$res = array();
		$base_url = "https://www.bhaskar.com/flicker/?eod";
		if ($pageNo==0) {
			$url = $base_url;
		}else{
			$pageNo++;
			$url = "https://www.bhaskar.com/flicker?page=$pageNo";
		}
		$data = $this->http($url);
		$html = str_get_html($data);
		$res["data"] = array();
		foreach ($html->find(".itembg") as $element) {
				foreach ($element->find("a") as $a) {
					$title = $a->attr["title"];
					$link  = $a->attr["href"];
					array_push($res["data"], array("title"=>$title,"link"=>$link));
					break;			
				} 
		}
		$nextExist = 0;
		foreach ($html->find("a#next") as $next) {
			$nextExist = 1;
		}
		if($nextExist){
			$res["next"] = "yes";
		}
		else{
			$res["next"] = "no";
		}
		$res["page"] = $pageNo;
		return json_encode($res,JSON_UNESCAPED_UNICODE);
	
	}
	//homepage scraping
	function scrapeLink_homepage_only($pageNo){
		
	}
	function scrapeLink_insertDB($pageNo = 0,$typeofpage){
		$res = '';
		if ($typeofpage=="trending") {
			$res = $this->scrapeLink_trending_only($pageNo);				
		}
		else if ($typeofpage=="homepage") {
			//scraping from homepage
		   //$res = $this->scrapeLink_homepage_only($pageNo);
		}
		else{
			return 0;
		}
		$json = json_decode($res);
		foreach ($json->data as $val) {
				$title =addslashes($val->title);
				$url  = addslashes($val->link);
				$r = mysqli_query($this->db,"SELECT *FROM dainik_bhasker WHERE title='$title'");
				if (mysqli_num_rows($r)==0) {
					echo $q = "INSERT INTO dainik_bhasker(url,title)VALUES('$url','$title')";echo PHP_EOL;
					 mysqli_query($this->db,$q);
				}
		}
		return $res;
	}
	//this is the public api 
	function scrapeAllLinks($typeofpage="trending"){
		$i=0;
		while (true) {
			$res = $this->scrapeLink_insertDB($i,$typeofpage);
			$data = json_decode($res);
			$i++;
			if ($data->next=='no') {
					echo PHP_EOL."Done".PHP_EOL;break;
			}	
		}
	}
	//public api
	function scrapePost($post_id){ 
		require_once('lib/simple_html_dom.php');
		$textData = '';
		$mr = mysqli_query($this->db,"SELECT *FROM dainik_bhasker WHERE pid=$post_id AND scraped=0");
		$my_data = mysqli_fetch_assoc($mr); 
		if (mysqli_num_rows($mr)>0) { 
			$photosArray = array();
			$data = $this->http($my_data['url']);
			$html = str_get_html($data);
			$i=1; // save photo index starting from 1
			foreach ($html->find('meta[name="news_keywords"]') as $key) {
				$tags = $key->attr["content"]; 
			}
			//when lazy loading is used for loading the images 
			foreach($html->find('.lazy') as $element){  
					//$save_to="/opt/lampstack-7.1.13-1/apache2/htdocs/youtube/posts/".$my_data["pid"]."/"; 
					$save_to=$this->save_to.'/'.$my_data["pid"]."/";
					$photo_link = '';
					if ($element->src) { 
						if (!mkdir($save_to, 0777, true)) {
						    $photo_link = $save_to.''.$i.".jpg";
						    array_push($photosArray, $photo_link);
						    file_put_contents($photo_link, file_get_contents($element->src)); 
							$i++;
						}  
					}
					elseif ($element->attr["data-original"]) {
						if (!mkdir($save_to, 0777, true)) {
							$photo_link = $save_to.''.$i.".jpg";
							array_push($photosArray, $photo_link);
							file_put_contents($photo_link, file_get_contents($element->attr["data-original"])); 
							$i++;
						}
					}
			}
			//when .articlelazy is used to load the single image
			foreach ($html->find('.articlelazy') as $element) {
					$save_to=$this->save_to.'/'.$my_data["pid"]."/";
					$photo_link = '';
					if ($element->src) { 
						if (!mkdir($save_to, 0777, true)) {
						    $photo_link = $save_to.''.$i.".jpg";
						    array_push($photosArray, $photo_link);
						    file_put_contents($photo_link, file_get_contents($element->src)); 
							$i++;
						}  
					}
					elseif ($element->attr["data-original"]) {
						if (!mkdir($save_to, 0777, true)) {
							$photo_link = $save_to.''.$i.".jpg";
							array_push($photosArray, $photo_link);
							file_put_contents($photo_link, file_get_contents($element->attr["data-original"])); 
							$i++;
						}
					}
			}
			foreach ($html->find('.introFirst') as $text) {
				$textData = $this->str_replace_gizmodization($text->plaintext);break;
			} 
			$textData = htmlspecialchars_decode($textData);
			$textData = str_replace("&#39;","", $textData);
			$textData = str_replace("#","", $textData);
			file_put_contents($save_to.'text.txt', $textData);
			$textData = addslashes($textData);
			$tags = addslashes($tags);

			$json_photos_string =addslashes(json_encode($photosArray,JSON_UNESCAPED_UNICODE));
			echo $s = "UPDATE dainik_bhasker SET photos='$json_photos_string',text_data='$textData',tags='$tags',scraped=1 WHERE pid=$post_id";
			$resss  = mysqli_query($this->db,$s); 
			$tag_saveto = $this->save_to.'/'.$post_id.'/tags.txt';
			if (file_exists($this->save_to.'/'.$post_id))
				file_put_contents($tag_saveto,$tags);
		}
		
	}
	//public api
	function scrapeAllPosts(){
		$res = mysqli_query($this->db,"SELECT *FROM dainik_bhasker WHERE scraped=0");
		$dir = $this->save_to;
		 while ($d=mysqli_fetch_assoc($res)) {
			$id = $d['pid'];
			echo "scraping post -> $id ".PHP_EOL;
			$this->scrapePost($id); 
			file_put_contents($dir.'/'.$id.'/title.txt', $d['title']);
		}
	}
	//upload videos to youtube
	function uploadAllUnuploadedVideostoYoutube(){
		$yt_obj = new YT($this->channelName);
		$res = mysqli_query($this->db,"SELECT *FROM dainik_bhasker WHERE uploaded=0 AND photos<>'[]' LIMIT 1");
		while ($d = mysqli_fetch_assoc($res)) {
			   $pid = $d['pid'];
			   echo PHP_EOL." Uploading video -> $pid ".PHP_EOL;
			   $videoDetails  = array('file_path' =>$this->save_to.'/'.$pid.'/output.mkv','title'=>$d['title'],'description'=>$d['text_data'],'tags'=>$d['tags'],'privacyStatus'=>'public' );

			   $uploaded_video_res = $yt_obj->uploadVideo($videoDetails);
			   $this->uploadedVideoUpdates($pid,$uploaded_video_res);
			   $this->incrementPidCount($pid);	
		} 	
	}
	function incrementPidCount($pid){
		$res = mysqli_query($this->db,"SELECT yt_next.*, y_tcrendentials.yt_creds,y_tcrendentials.name FROM yt_next INNER JOIN y_tcrendentials WHERE y_tcrendentials.id=yt_next.pid ORDER BY yt_next.updated_at DESC LIMIT 1");
		if(mysqli_num_rows($res)==1) {
			mysqli_query($this->db,"UPDATE yt_next SET count=count+1,updated_at=NOW() WHERE pid=$pid");			
		}	
		else{
			mysqli_query($this->db,"INSERT INTO yt_next(pid,count,updated_at) VALUES($pid,1,NOW())");
		}
	}
	function uploadedVideoUpdates($id,$uploaded_video_res){
		if ($uploaded_video_res['status']==200) {
			$yt_id = $uploaded_video_res['result']['id'];
			$res = mysqli_query($this->db,"UPDATE dainik_bhasker SET yt_id='$yt_id',uploaded=1 WHERE pid=$id");	
		}
		else{
			$res = mysqli_query($this->db,"UPDATE dainik_bhasker SET uploaded=1 WHERE pid=$id");
		}
	}
	//public api -> can only be used on terminal 
	function statusUpdate(){
		global $argv;
		//for updating state of 'processed' feild in db of a scraped post
		if ($argv[1]=="processed") {
			$id = $argv[2]; //post id
			$res = mysqli_query($this->db,"UPDATE dainik_bhasker SET processed=1 WHERE pid=$id");
		}else if($argv[1]=="uploaded"){
			$id = $argv[2]; //post id
			$res = mysqli_query($this->db,"UPDATE dainik_bhasker SET uploaded=1 WHERE pid=$id");
		}else if ($argv[1]=="unprocessed") { 
			$res = mysqli_query($this->db,"SELECT pid FROM dainik_bhasker WHERE processed=0 AND photos<>'[]'");
			$data = "";
			while ($d = mysqli_fetch_assoc($res)) {
			 		$data.="{$d['pid']} "; 
			} 
			echo $data;
		}else if ($argv[1]=="notuploaded") {
			$res = mysqli_query($this->db,"SELECT pid FROM dainik_bhasker WHERE uploaded=0");
			$data = "";
			while ($d = mysqli_fetch_assoc($res)) {
			 		$data.="{$d['pid']} "; 
			} 
			echo $data;
		}
	}


}
 
