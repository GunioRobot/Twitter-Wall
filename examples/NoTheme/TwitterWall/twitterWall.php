<?php
/**
  *  Author: Zimtea Robert
  *  Date: 5 December 2009
  *
*/

/***************************** CONFIGURATION ******************/
define('CLASS_PATH', ''); //if you have the core files in another directory, specifiy the directory here

if(!isset($_GET['hashtag'])){
	define('HASHTAG', '#nowplaying'); //you mostly don't need to define this, it is a falback incase there's none defined by javascript
}else{
	define('HASHTAG', $_GET['hashtag']);
}
/********************************************/
	//if we are on a production server, turn error reporting off
	error_reporting(0);
	//require the class file
	require CLASS_PATH.'class.twitterwall.php';

	//check if since_id is sent via ajax, if not set it to 0
	if(isset($_GET['since_id']) && $_GET['since_id'] !== 'undefined'){
		//the ajax request has sent us the since id, so we set the var according to it
		$since_id = $_GET['since_id'];
	}else{
		//no responce from the ajax call, so get the most 15 recent twits
		$since_id = 0;
	}


	//Initiate the class and begin the process
	$tWall = new tWall(USERNAME, PASSWORD);
	//do a search with the specified hashtag, and if the since_id is set, get only the tweets that are after it
	$result = $tWall->doSearch(HASHTAG, $since_id);
	//Twitter returns the result as JSON data, we need to decode it
	$result = json_decode($result);


	if($result->results || !empty($result->results)){
		//loop to each result and construct the tweet
		//also, if you want to change the tags order, here are some vars that should be useful
		// $twit->id = stores the twit id, keep in mind that this should always reside in the list item (li) since_id attribute
		// $twit->from_user = the twit 'owner'/who posted the twit
		// $twit->text = maybe the most important, the current twit
		// $twit->created_at = the time when the respective twit got posted
		// $twit->profile_image_url = returns the avatar link
		//
		// If you want to get a nice date, like (23 seconds ago) you can use getRelativeTime method, remeber to initiate the tWall class first
		foreach($result->results as $twit){
			$output = '<li since_id="'.$twit->id.'">';
			$output .= "<div class='twall-tweets'>";
			$output .= "<h3><a href='http://twitter.com/{$twit->from_user}'>{$twit->from_user}</a></h3>";
			$output .= "<p class='twall-tweet'>{$twit->text}</p>";
			$output .= '<p class="twall-dates">'.$tWall->getRelativeTime($twit->created_at).'</p>';
			$output .= '</div>';
			$output .= '<img class="twall-avatars" src="'.$twit->profile_image_url.'" />';
			$output .= "<div class='clear'></div>";
			$output .= '</li>';
			echo $output;
		}
	}
?>