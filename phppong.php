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

$startTime = isset($_REQUEST['startTime'])?$_REQUEST['startTime']:time()-(.25*24*60*60);
$endTime = isset($_REQUEST['endTime'])?$_REQUEST['endTime']:time()+1000;
$jsLog = json_encode($logArr);


?>
<html>
<head>
	<title>PhpPong</title>
	<style>
	body {
		background-color: #000011;
	}
	.phpPong.table {
		margin:auto;
		height:90%;
		width: 87%;
		border:1px dotted black;
		background-color: #00aa22;
	}
	.phpPong.table #ips {
		width:20%;
		height:100%;
	}
	.phpPong.table #pages {
		width:20%;
		height:100%;
	}
	.phpPong.time {
		position: absolute;
		font-size: 200%;
		font-weight: bold;
		font-family: "Courier New";
		color:#eeddde;
	}
	.circle {
		border-radius: 50%;
		width:8px;
		height:8px;
		background-color: blue;
	}
	</style>
	<script src="jquery-2.1.1.min.js"></script>
</head>
<body>
	<div class='phpPong time'></div>

	<table class='phpPong table'>
		<tr>
			<td id='ips'></td><td id='innerMove'></td><td id='pages'></td>
		</tr>
	</table>

<script>
inputLog = JSON.parse('<?php echo $jsLog ?>');
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



var thisLogInd = inputLogStartInd;
var pagesListed = new Array();
setInterval(function(){
	if(inputLog[thisLogInd].time==thisSecond){
		var entCount = 0;
		while(inputLog[thisLogInd].time==thisSecond){
			var repeatPage = false;
			console.log(pagesListed);
			for(var i = 0; i < pagesListed.length; i++){
				
				if(pagesListed[i][3]==inputLog[thisLogInd].file){
					repeatPage = true;
					break;
				}
			}
			if(!repeatPage){
				pagesListed.push(['pageName','ent'+entCount,'s'+thisSecond,inputLog[thisLogInd].file]);
				$(".phpPong.table #pages").append('<div class="pageName ent'+entCount+'" id="s'+thisSecond+'">'+inputLog[thisLogInd].file+'</div>'); // make pagesListed to handle duplicates
			}
			$(".phpPong.table #ips").append('<div class="ipAdd ent'+entCount+'" id="s'+thisSecond+'">'+inputLog[thisLogInd].ip+'</div>'); // make ipsListed to handle duplicates
			/*
			console.log($('.ipAdd.ent'+entCount+'#s'+thisSecond));*/
			var ipAddTop = ($('.ipAdd.ent'+entCount+'#s'+thisSecond).offset().top - $(window).scrollTop());
			var ipAddLeft = ($('.ipAdd.ent'+entCount+'#s'+thisSecond).offset().left);

			$("body").append(
				'<div class="circle ent'+entCount+'" id="s'+thisSecond+'" \
				style="position:absolute;\
				top:'+ipAddTop+';\
				left:'+ipAddLeft+';"></div>');
			
			//console.log($('.pageName.ent'+entCount+'#s'+thisSecond).length);
			var pageNameTop,pageNameLeft;
			if(!repeatPage){
				pageNameTop = ($('.pageName.ent'+entCount+'#s'+thisSecond).offset().top - $(window).scrollTop());
				pageNameLeft = ($('.pageName.ent'+entCount+'#s'+thisSecond).offset().left);
			} else {
				pageNameTop = ($('.pageName.'+pagesListed[i][1]+'#'+pagesListed[i][2]).offset().top - $(window).scrollTop());
				pageNameLeft = ($('.pageName.'+pagesListed[i][1]+'#'+pagesListed[i][2]).offset().left);
			}
			
			$('.circle.ent'+entCount+'#s'+thisSecond).animate({
				'top':pageNameTop+'px',
				'left':pageNameLeft+'px'
			},2000,
			function(){
				$(this).animate({
					'left':ipAddLeft,// Can modify this to make it more pong-like (bounce at inverted angle)
					'top':ipAddTop
				},2000,function(){
					$(this).fadeOut(700);
					$('.ipAdd.'+$(this).attr('class').split(" ")[1]+'#'+$(this).attr('id')).fadeOut(700);
				});
			});
			entCount++;
			thisLogInd++;
			if(inputLog.length<=thisLogInd){
				getNewLog(endTime,((new Date()).getTime())+1000);
				break;
			}
		}
	}


	



	$(".phpPong.time").html(timeConverter(thisSecond));
	thisSecond++;
	console.log("thisNewSecond: "+thisSecond);
},1000);

} else {
	console.log("No log entries, waiting 2 minutes");
}
//http://stackoverflow.com/questions/847185/convert-a-unix-timestamp-to-time-in-javascript
function timeConverter(UNIX_timestamp){
 var a = new Date(UNIX_timestamp*1000);
 var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
     var year = a.getFullYear();
     var month = months[a.getMonth()];
     var date = a.getDate();
     var hour = a.getHours();
     var min = a.getMinutes();
     var sec = a.getSeconds();
     var time = date+','+month+' '+year+' '+hour+':'+min+':'+sec ;
     return time;
 }

 function getNewLog(start,end){
 	$.post('serveLog.php',{'startTime':start,'endTime':end},function(data){
 		inputLog = JSON.parse('<?php echo $jsLog ?>');
 		thisLogInd = 0;
 		for(var i = 0; i < inputLog.length; i++){
			if(inputLog[i].time >= thisSecond){
				inputLogStartInd = thisLogInd = i;
				//thisLogInd = i;
				thisSecond = inputLog[i].time;
				break;
			}
		}
 	});
 }

</script>

</body>
</html>