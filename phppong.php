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

//for()

// Need time map for events



?>
<html>
<head>
	<title>PhpPong</title>
	<style>
	.phpPongTable {
		margin:auto;
		height:90%;
		width: 87%;
		border:1px dotted black;
	}
	.phpPongTable #ips {
		width:20%;
		height:100%;
	}
	.phpPongTable #pages {
		width:20%;
		height:100%;
	}
	</style>
	<script src="jquery-2.1.1.min.js"></script>
</head>
<body>

	<table class='phpPongTable'>
		<tr>
			<td id='ips'></td><td id='innerMove'></td><td id='pages'></td>
		</tr>
	</table>

<script>
var inputLog = JSON.parse('<?php echo $jsLog ?>');
var numOfPings = 0;



var inputLogStartInd = -1;
var thisSecond = '<?php echo $startTime ?>';
for(var i = 0; i < inputLog.length; i++){
	if(inputLog[i].time >= thisSecond){
		inputLogStartInd = i;
		thisSecond = inputLog[i].time;
		break;
	}
}
if(inputLogStartInd!=-1){



var bncEnXCoord = '70%';
var bncStXCoord = '70%';
var thisLogInd = inputLogStartInd;
console.log("thisLogInd: "+thisLogInd);
console.log(inputLog[thisLogInd]);
var pagesListed = new Array();
setInterval(function(){
	console.log(inputLog[thisLogInd].time);
	console.log(thisSecond+"\n");
	if(inputLog[thisLogInd].time==thisSecond){
		var entCount = 0;
		while(inputLog[thisLogInd].time==thisSecond){
			$(".phpPongTable #pages").append('<div class="pageName ent'+entCount+'" id="s'+thisSecond+'">'+inputLog[thisLogInd].file+'</div>'); // make pagesListed to handle duplicates
			$(".phpPongTable #ips").append('<div class="ipAdd ent'+entCount+'" id="s'+thisSecond+'">'+inputLog[thisLogInd].ip+'</div>'); // make ipsListed to handle duplicates
			console.log($('.ipAdd.ent'+entCount+'#s'+thisSecond));
			var ipAddTop = ($('.ipAdd.ent'+entCount+'#s'+thisSecond).offset().top - $(window).scrollTop());
			var ipAddLeft = ($('.ipAdd.ent'+entCount+'#s'+thisSecond).offset().left);
			$("body").append(
				'<div class="circle ent'+entCount+'" id="s'+thisSecond+'" \
				style="position:absolute;\
				top:'+ipAddTop+';\
				left:'+ipAddLeft+';">0</div>');
			console.log($('.pageName.ent'+entCount+'#s'+thisSecond).length);
			var pageNameTop = ($('.pageName.ent'+entCount+'#s'+thisSecond).offset().top - $(window).scrollTop());
			var pageNameLeft = ($('.pageName.ent'+entCount+'#s'+thisSecond).offset().left);
			$('.circle.ent'+entCount+'#s'+thisSecond).animate({
				'top':pageNameTop+'px',
				'left':pageNameLeft+'px'
			},2000,
			function(){
				$(this).animate({
					'left':ipAddLeft,// Can modify this to make it more pong-like (bounce at inverted angle)
					'top':ipAddTop
				},2000,function(){
					$(this).remove();
					$('.ipAdd.'+$(this).attr('class').split(" ")[1]+'#'+$(this).attr('id')).remove();
					console.log($(this).attr('class'));
					console.log($('.ipAdd.'+$(this).attr('class').split(" ")[1]+'#'+$(this).attr('id')));
				});
			});
			thisLogInd++;
			entCount++;
		}
	}


	




	thisSecond++;
	console.log("thisNewSecond: "+thisSecond);
},1000);

} else {
	console.log("No log entries");
}



</script>

</body>
</html>