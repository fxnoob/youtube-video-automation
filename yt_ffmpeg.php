<?php

require_once('config.php');
/*
*
* 
*
*
*/

class yt_ffmpeg extends config
{
	
	function __construct($channel_name)
	{
		if (!file_exists($file = __DIR__ . '/vendor/autoload.php')) {
		    throw new \Exception('please run "composer require php-ffmpeg/php-ffmpeg" in "' . __DIR__ .'"');
		}
		parent::__construct($channel_name);
	}
	function test(){
		require 'vendor/autoload.php';
		$ffmpeg = FFMpeg\FFMpeg::create();
		$video = $ffmpeg->open('/root/Documents/git_projects_fxnoob/youtube_news_video_maker/dainikBhasker_uploads/posts/10/out.mkv');
		$video
		    ->filters()
		    ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
		    ->synchronize();
		$video
		    ->save(new FFMpeg\Format\Video\WebM(), '/root/Documents/git_projects_fxnoob/youtube_news_video_maker/dainikBhasker_uploads/posts/10/export-wmv.wmv'); 
	}

}

//test
$ffmpeg = new yt_ffmpeg("FX Noob");
$ffmpeg->test();

