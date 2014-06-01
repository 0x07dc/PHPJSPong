<?php

function runLogViewer(){

date_default_timezone_set('AMERICA/NEW_YORK');
$log = file_get_contents('');
$logArrPre = explode("\n",$log);
$logArr = array();
for($i = 0; $i < count($logArrPre); $i++){
	preg_match('/^[^\s]*/', $logArrPre[$i], $newIp);
	$newIp = $newIp[0];
	preg_match('/\[(.*)\]/', $logArrPre[$i], $newTime);
	$newTime = $newTime[1];
	preg_match('/"(.*)"/', $logArrPre[$i], $pageNType);
	$pageNType = $pageNType[1];
	preg_match('/([^\s]*)\s([^\s]*)\s([^"])/', $pageNType, $newPageNType);
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
return $logArr;

}

?>