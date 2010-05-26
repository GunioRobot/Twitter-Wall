<?php
/**
  * Twitter Wall Class
  *
  *  Author: Zimtea Robert
  *  Date: 5 December 2009
  *	
  *
  * Usage:
  *  $twall = new tWall("username", "password");
  *  $twits = $twall->doSearch("your search therm here");
  *
  * Methods:
  *  doSearch([$hashtag], $since_id, $format);
  *  getRelativeTime($date)
  *  endSession()
  *  lastStatusCode()
  *  lastAPICall()
  */

class tWall {
	/* Contains the last HTTP status code returned */
	private $http_status;
	
	/* Contains the last API call */
	private $last_api_call;
	
	/* Last tweet id */
	public $last_tweet_id;
	
	/* tWall class constructor */
	function tWall($username, $password) {
		$this->credentials = sprintf("%s:%s", $username, $password);
	}
	
	function doSearch($hashtag, $since_id = '', $rrp='20', $format = 'json'){
		$api_call = sprintf("http://search.twitter.com/search.%s?q=%s&since_id=%s&rrp=%s", $format, urlencode($hashtag), $since_id, $rrp);
		return $this->APICall($api_call);
	}
	
	function showStatus($format, $id) {
		$api_call = sprintf("http://twitter.com/statuses/show/%d.%s", $id, $format);
		return $this->APICall($api_call);
	}
	
	function plural($num) {
		if ($num <> 1)
			return "s";
	}

	function getRelativeTime($date) {
		$diff = time() - strtotime($date);
		if ($diff<60)
			return $diff . " second" . $this->plural($diff) . " ago";
		$diff = round($diff/60);
		if ($diff<60)
			return $diff . " minute" . $this->plural($diff) . " ago";
		$diff = round($diff/60);
		if ($diff<24)
			return $diff . " hour" . $this->plural($diff) . " ago";
		$diff = round($diff/24);
		if ($diff<7)
			return $diff . " day" . $this->plural($diff) . " ago";
		$diff = round($diff/7);
		if ($diff<4)
			return $diff . " week" . $this->plural($diff) . " ago";
		return "on " . date("F j, Y", strtotime($date));
	}
	
	function endSession() {
		$api_call = "http://twitter.com/account/end_session";
		return $this->APICall($api_call, true);
	}
	
	private function APICall($api_url, $require_credentials = false, $http_post = false) {
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $api_url);
		if ($require_credentials) {
			curl_setopt($curl_handle, CURLOPT_USERPWD, $this->credentials);
		}
		if ($http_post) {
			curl_setopt($curl_handle, CURLOPT_POST, true);
		}
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
		$twitter_data = curl_exec($curl_handle);
		$this->http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		$this->last_api_call = $api_url;
		curl_close($curl_handle);
		return $twitter_data;
	}
	
	function lastStatusCode() {
		return $this->http_status;
	}
	
	function lastAPICall() {
		return $this->last_api_call;
	}
}
?>