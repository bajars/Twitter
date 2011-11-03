<?php
header("Content-type: text/html; charset=utf-8");

$r = mysql_connect(localhost, "user", "pass");
mysql_select_db("db", $r);

mysql_query("set names utf8");
mysql_query("set charset set utf8");

$curl = curl_init();

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, 'track=darbs,skola,studijas');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, 'https://stream.twitter.com/1/statuses/filter.json');
curl_setopt($curl, CURLOPT_USERPWD, 'user:pass');

curl_setopt($curl, CURLOPT_WRITEFUNCTION, 'progress');
curl_exec($curl);
curl_close($curl);

function progress($curl, $str)
{
	$obj = json_decode($str);

	if (!empty($obj)) {
		$valoda = detect_language($obj->{'text'});
		print $obj->{'text'}."\n";
		if ($valoda == "lv") {	

			$tweet_query="INSERT INTO tweets values ('". mysql_escape_string($str) ."');";
			$tweet_result=mysql_query($tweet_query);			

			$profile = 0;
			$twitter_account = getTwitterAccount($obj->{'text'});
			if ($twitter_account == "darbs") $profile = 1;
			if ($twitter_account == "skola") $profile = 2; 
			if ($twitter_account == "studijas") $profile = 3; 

	 		$qFriends="SELECT id FROM friends WHERE TwitterUserId = ". $obj->{'user'}->{'id'} ." AND ProfilId = $profile";
			$rFriends=mysql_query($qFriends);
			$nFriends=mysql_numrows($rFriends);
				
	 		$qFollowers="SELECT id FROM followers WHERE TwitterUserId = ". $obj->{'user'}->{'id'} ." AND ProfilId = $profile";
			$rFollowers=mysql_query($qFollowers);
			$nFollowers=mysql_numrows($rFollowers);

			if (($nFriends > 0)||($nFollowers > 0)) {
				print "Following exists: ". $obj->{'user'}->{'screen_name'} ." ". $obj->{'user'}->{'id'};
			}else{	
				print $obj->{'text'}."\n"; // 12345
				//print $obj->{'user'}->{'name'}."\n"; // 12345
				//print $obj->{'user'}->{'screen_name'}."\n"; // 12345
				//print $obj->{'user'}->{'id'}; // 12345
		
				
				followUser($obj->{'user'}->{'screen_name'}, $twitter_account);
			}
			print "\n\n";
		}
	}
	return strlen($str);
}

function getTwitterAccount($text) {
	if (substr_count($text,"darbs") > 0) return 'darbs';
	if (substr_count($text,"skola") > 0) return 'skola';
	if (substr_count($text,"studijas") > 0) return 'studijas';
	return 'darbs';
}

function detect_language($text)
{
	$arr = array("â","è","ç","ì","î","í","ï","ò","ð","û","þ");
	if (strpos_arr($text,$arr)) return "lv";

	$text = preg_replace("/(http:\/\/)[\w]+[\.]{1}[\w]+[\/]+[\w]+/i", " ", $text); // filtre linkus
	$text = preg_replace("/@[\w]+/i", " ", $text); // filtre autorus
	$text = preg_replace("/#[\w]+/i", " ", $text); // filtree tegus

	$clean = ereg_replace("[^A-Za-zâ-þÂ-Þ ]", " ", $text );
	$clean = str_replace("  "," ",$clean);
	$clean = str_replace("  "," ",$clean);

	$arr = explode(" ",$clean);
	$iv = 0;
	if (sizeof($arr) > 0) {
		foreach ($arr as $value)
		{
			if ($value != " ") {
				$qFriends="SELECT * FROM lv_vardi WHERE vards='". $value ."'";	
				$rFriends=mysql_query($qFriends);
				$nFriends=mysql_numrows($rFriends);
				if ($nFriends != 0) $nFriends = 1;
				$iv = $iv + $nFriends;
			}
		} 
		if ($iv >= (sizeof($arr) / 2)) return "lv";
		else return "ne";
	}

	return "not";
	//izrâdâs nevar google automâtiski lietot ðo servisu (IP banned :D)
	/*
	$version = '1.0';
	$url = 'http://www.google.com/uds/GlangDetect?v='.$version.'&q='.urlencode($text);

	$response = json_decode(file_get_contents($url), true);

	if ($response['responseStatus'] == 200)
		return $response['responseData']['language'];
	else
		return false;
	*/
}

function strpos_arr($haystack, $needle) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $what) {
        if(($pos = strpos($haystack, $what))!==false) return $pos;
    }
    return false;
}

function followUser($screen_name, $twitter_account)
{
	require_once('twitteroauth/twitteroauth.php');

	if ($twitter_account == "darbs") {
		$connection = new TwitterOAuth('x', 'x', 'x', 'x');
	}else if ($twitter_account == "skola") {
		$connection = new TwitterOAuth('x', 'x', 'x', 'x');
	}else if ($twitter_account == "studijas") {
		$connection = new TwitterOAuth('x', 'x', 'x', 'x');
	}
	$connection->post('friendships/create', array('screen_name' => $screen_name));	  
}

?>