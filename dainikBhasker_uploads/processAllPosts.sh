pids=$( php statusUpdate.php unprocessed )
for c in $pids
do  
	if [ -d "/root/Documents/git_projects_fxnoob/youtube_news_video_maker/dainikBhasker_uploads/posts/$c" ]; then
		echo "processing post: $c"	
		bash tts "/root/Documents/git_projects_fxnoob/youtube_news_video_maker/dainikBhasker_uploads/posts/$c"
		php statusUpdate.php "processed" $c
		#notify-send "# $c post has been processed"
	fi
	php statusUpdate.php "processed" $c
	
done
