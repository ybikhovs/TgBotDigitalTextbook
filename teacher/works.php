<?php

require '../connect.php';
$res = $conn->query("SELECT * FROM bot_work WHERE provereno<>'да'");
$rows = $res->fetch_all(MYSQLI_ASSOC);
print ("<table>");
foreach ($rows as $row ) {
	$profile = $conn->query("SELECT * FROM bot_spisok WHERE chatid = '".$row['chatid']."'");
	$prof = $profile->fetch_all(MYSQLI_ASSOC);
	foreach ($prof as $pr ) {
		print ("<tr><td>".$row['time']." | ".$pr['first_name']." ".$pr['second_name']." | ".$pr['class']." | <a href=".$row['url']." target=_blank>работа</a> | </td><td><form action='provereno.php' method='post' target='_blank'><input type='hidden' name='time' value='".$row['time']."'><input type='submit' value='Проверено'></form></td></tr>");
	}
}
print ("</table>");
