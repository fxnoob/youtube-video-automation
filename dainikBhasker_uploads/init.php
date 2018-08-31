<?php

require_once('../dainik.php');
//  path_to_save_files-> /root/Documents/git_projects_fxnoob/youtube_news_video_maker/dainikBhasker_uploads/posts

$path_to_save_files = "/root/Documents/git_projects_fxnoob/youtube_news_video_maker/dainikBhasker_uploads/posts";
$channel_name = "This Channel";

$which_page_to_scrape = "trending"; // homepage | trending

$bhasker = new dainik($channel_name,$path_to_save_files);

