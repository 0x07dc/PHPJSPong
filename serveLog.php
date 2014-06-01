<?php

date_default_timezone_set('AMERICA/NEW_YORK');
$log = file_get_contents('/usr/local/apache/logs/access_log');
$logArrPre = explode("\n",$log);
$logArr = array();
for($i = 0; $i < count($logArrPre); $i++){
	preg_match('/^[^\s]*/', $logArrPre[$i], $newIp);
	$newIp = $newIp[0];
	preg_match('/\[(.*)\]/', $logArrPre[$i], $newTime);
	$newTime = $newTime[1];
	preg_match('/"(.*)"/', $logArrPre[$i], $pageNType);
	$pageNType = $pageNType[1];
	preg_match('/([^\s]*)\s([^\s]*)\s(.*)/', $pageNType, $newPageNType);
	$newType = $newPageNType[1];
	$newPage = $newPageNType[2];
	$newProt = $newPageNType[3];
	$newRow = array(
		"ip"=>$newIp,
		"time"=>strtotime($newTime),
		"file"=>$newPage,
		"reqType"=>$newType,
		"reqProt"=>$newProt,
		);
	$logArr[] = $newRow;

}
//print_r($logArr);

$startTime = isset($_REQUEST['startTime'])?$_REQUEST['startTime']:time()-(5*24*60*60);
$endTime = isset($_REQUEST['endTime'])?$_REQUEST['endTime']:time()+1000;
$jsLog = json_encode($logArr);

echo $jsLog;












?>