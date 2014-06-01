<?php

require('php.php');
$logArr = runLogViewer();



//print_r($logArr);

$startTime = isset($_REQUEST['startTime'])?$_REQUEST['startTime']:time()-60;//(5*24*60*60);
$endTime = isset($_REQUEST['endTime'])?$_REQUEST['endTime']:time();


// Clean up old entries or new ones (out of schedule range)
$tempLogArr = $logArr;
$logArr = array();
//echo $startTime;
for($i = 0; $i < count($tempLogArr); $i++){
	if(($tempLogArr[$i]['time']>=$startTime) && ($tempLogArr[$i]['time']<=$endTime)){
		$logArr[] = $tempLogArr[$i];
	}
	//print_r($tempLogArr[$i])."\n";
}


$jsLog = json_encode(array($logArr,$endTime,$startTime));

echo $jsLog;













?>