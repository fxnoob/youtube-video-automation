<?php 

require_once('config.php');
/*
*
* 
*
**/

 class YT extends config
 {
 	public $accessToken;
 	//constructor
 	function __construct($channel_name)
 	{
 		if (!file_exists($file = __DIR__ . '/vendor/autoload.php')) {
		    throw new \Exception('please run "composer require google/apiclient:~2.0" in "' . __DIR__ .'"');
		}
 		parent::__construct($channel_name);
 	}

 	//auth
	function getClient() {
		require_once __DIR__ . '/vendor/autoload.php';
		$client = new Google_Client();
		$client->setAuthConfigFile('client_secrets.json');
		$client->addScope(Google_Service_YouTube::YOUTUBE);
  		$client->setAccessType('offline');
		$this->accessToken = $this->getCredsFromDB();
		if($this->accessToken==null){
			$authUrl = $client->createAuthUrl();
		    printf("Open the following link in your browser:\n%s\n", $authUrl);
		    print 'Enter verification code: ';
		    $authCode = trim(fgets(STDIN));
			$this->accessToken = $client->authenticate($authCode);
			$client->setAccessToken($this->accessToken);
			$this->setCredsIntoDB($this->accessToken);
		}
		else{
			$client->setAccessToken($this->accessToken);
		}
		 // Refresh the token if it's expired.
		if ($client->isAccessTokenExpired()) {
		    $client->refreshToken($client->getRefreshToken());
		    $this->refreshAccessTokenIntoDB($this->accessToken,$client->getAccessToken());
		}
		return $client;	
 	}
 	function uploadVideo($videoDetails){
 		$res = array();
 		$res['status'] = null; 
 		if (file_exists($videoDetails['file_path'])) {
 			try{
 				$client = $this->getClient();
	 			$snippet = new Google_Service_YouTube_VideoSnippet();
	 			$youtube = new Google_Service_YouTube($client);
			    $snippet->setTitle($videoDetails['title']);
			    $snippet->setDescription($videoDetails['description']);
			    $snippet->setTags(explode(',',$videoDetails['tags']));
			    // Numeric video category. See
			    // https://developers.google.com/youtube/v3/docs/videoCategories/list
			    $snippet->setCategoryId("22");
			    // Set the video's status to "public". Valid statuses are "public",
			    // "private" and "unlisted".
			    $status = new Google_Service_YouTube_VideoStatus();
			    $status->privacyStatus = $videoDetails['privacyStatus'];
			    // Associate the snippet and status objects with a new video resource.
			    $video = new Google_Service_YouTube_Video();
			    $video->setSnippet($snippet);
			    $video->setStatus($status);
			    // Specify the size of each chunk of data, in bytes. Set a higher value for
			    // reliable connection as fewer chunks lead to faster uploads. Set a lower
			    // value for better recovery on less reliable connections.
			    $chunkSizeBytes = 1 * 1024 * 1024;

			    // Setting the defer flag to true tells the client to return a request which can be called
			    // with ->execute(); instead of making the API call immediately.
			    $client->setDefer(true);
				// Create a request for the API's videos.insert method to create and upload the video.
			    $insertRequest = $youtube->videos->insert("status,snippet", $video);

			    // Create a MediaFileUpload object for resumable uploads.
			    $media = new Google_Http_MediaFileUpload(
			        $client,
			        $insertRequest,
			        'video/*',
			        null,
			        true,
			        $chunkSizeBytes
			    );
			    var_dump($videoDetails['file_path']);
			    $media->setFileSize(filesize($videoDetails['file_path']));	    
			    // Read the media file and upload it chunk by chunk.
			    $status = false;
			    $handle = fopen($videoDetails['file_path'], "rb");
			    while (!$status && !feof($handle)) {
			      $chunk = fread($handle, $chunkSizeBytes);
			      $status = $media->nextChunk($chunk);
			    }

			    fclose($handle);

			    // If you want to make other calls after the file upload, set setDefer back to false
			    $client->setDefer(false);
 			}
			catch (Google_Service_Exception $e) {
			    var_dump($e->getMessage());
			} catch (Google_Exception $e) {
			    var_dump(($e->getMessage()));
			}
		    $res['result'] = $status;
		    $res['status'] = 200;
 		}
 		return $res;
 	}
 	//setting flag if a new yt account needs to be added
 	function getCredsFromDB($fetch=true){
 		if ($fetch) {  	
 		$res  = mysqli_query($this->db,"SELECT yt_next.*, y_tcrendentials.yt_creds,y_tcrendentials.name FROM yt_next INNER JOIN y_tcrendentials WHERE y_tcrendentials.id=yt_next.pid ORDER BY yt_next.updated_at DESC LIMIT 1");
 		$data = mysqli_fetch_assoc($res);
 		return $data['yt_creds'];
 		}
 		return null;
 	}
 	function setCredsIntoDB($creds){
 		$cre = addslashes(json_encode($creds));
 		$sql = "INSERT INTO y_tcredentials(yt_creds,name) VALUES('$cre','no name for now')";
 		$res = mysqli_query($this->db,$sql);
 		return $res;	
 	}	
 	function refreshAccessTokenIntoDB($oldAccessToken,$newAccessToken){
 		$creOld = addslashes(json_encode($oldAccessToken));
 		$creNew = addslashes(json_encode($newAccessToken));
 		$sql = "UPDATE y_tcredentials SET yt_creds='$creNew' WHERE yt_creds='$creOld'";
 		$res = mysqli_query($this->db,$sql);
 		$this->accessToken = $newAccessToken;
 		return $res;
 	}
 } 



//test

// $videoDetails  = array('file_path' => '/root/Documents/git_projects_fxnoob/youtube_news_video_maker/dainikBhasker_uploads/posts/21/out.mkv','title'=>'Testing ','description'=>'testing','tags'=>'1,2,3,4','privacyStatus'=>'private');

// $yt_test = new YT('FX Noob');
// var_dump($yt_test->uploadVideo($videoDetails));
