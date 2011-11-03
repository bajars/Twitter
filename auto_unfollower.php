<?php

header("Content-type: text/html; charset=utf-8");

$r = mysql_connect(localhost, "user", "pass");
mysql_select_db("database", $r);

mysql_query("set names utf8");
mysql_query("set charset set utf8"); 

//$twitter_account = "studijas";
//$tn = 2;

//$twitter_account = "skola";
//$tn = 3;

$twitter_account = "darbs";
$tn = 1; 

require_once('twitteroauth/twitteroauth.php');

 	if ($twitter_account == "darbs") {
		$connection = new TwitterOAuth('x', 'x', 'x', 'x');
	}else if ($twitter_account == "skola") {
		$connection = new TwitterOAuth('x', 'x', 'x', 'x');
	}else if ($twitter_account == "studijas") {
		$connection = new TwitterOAuth('x', 'x', 'x', 'x');
	} 

$qFriends="SELECT * FROM friends WHERE ProfilId = ". $tn ." AND Atsekots = 0";
$rFriends=mysql_query($qFriends);
$nFriends=mysql_numrows($rFriends);
for ($i=0;$i<$nFriends;$i++) {
	$tuid=mysql_result($rFriends,$i,"TwitterUserId");
	print "tuid = $tuid\n";

	$r = $connection->post('friendships/destroy', array('user_id' => $tuid));

	$query="UPDATE friends SET Atsekots = 1 WHERE TwitterUserId = $tuid AND ProfilId = ". $tn;
	$result=mysql_query($query);
}
?>