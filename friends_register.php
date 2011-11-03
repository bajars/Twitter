<?php
header("Content-type: text/html; charset=utf-8");

$r = mysql_connect(localhost, "user", "pass");
mysql_select_db("database", $r);

mysql_query("set names utf8");
mysql_query("set charset set utf8");

//choose this

//$username="studijas";
//$ac = 3;

//$username="skola";
//$ac = 2;

$username="darbs";
$ac = 1;

$i =0;
$aUserIds = get_follower_ids($username); 
$size = sizeof($aUserIds);

	for ($i = $size - 1; $i > 200; $i--) {
		$query="INSERT INTO friends values (null, $ac, ". $aUserIds[$i] .", 0);";
		$result=mysql_query($query);
		print $i .". ". $aUserIds[$i] ." ";
	} 

function get_follower_ids($username)
{
    $url = 'http://api.twitter.com/1/friends/ids.xml?screen_name=' . $username;
    $xml = file_get_contents($url);
    preg_match_all('/<id>([^<]+)<\/id>/', $xml, $aMatch);

    return $aMatch[1];
}
?> 