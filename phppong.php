<?php

require('php.php');

if($_REQUEST['p']!="password") exit();





$logArr = runLogViewer();
//print_r($logArr);

$startTime = isset($_REQUEST['startTime'])?$_REQUEST['startTime']:time()-(12);//(.25*24*60*60);
$endTime = isset($_REQUEST['endTime'])?$_REQUEST['endTime']:time();
$jsLog = json_encode($logArr);


?>
<html>
<head>
	<title>PhpPong</title>
	<style>
	body {
		background-color: #000011;
		color: #ccccff;
	}
	.phpPong.table {
		margin:auto;
		height:90%;
		width: 87%;
		border:1px dotted black;
		background-color: #00440a;
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
		width:7px;
		height:7px;
		background-color: #0CD3B6;
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
window.inputLog = JSON.parse('<?php echo $jsLog ?>');
window.inputLogStartInd = -1;
window.thisSecond = '<?php echo $startTime ?>';
window.endTime = '<?php echo $endTime ?>';


for(var i = 0; i < inputLog.length; i++){
	if(inputLog[i].time >= thisSecond){
		inputLogStartInd = i;
		thisSecond = inputLog[i].time;
		break;
	}
}
if(inputLogStartInd!=-1){

	window.thisLogInd = inputLogStartInd;
	window.pagesListed = new Array();
	window.ipsListed = new Array();
	runLogViewer();

} else {
	runLogViewer();
	console.log("No log entries, waiting 10 seconds");
	setTimeout(function(){
		getNewLog(getNewLog(endTime,Math.round((new Date()).getTime()/1000)));
	},10000)
}


function runLogViewer(){
	this.logViewerInterval = setInterval(function(){
		if(inputLogStartInd!=-1 && typeof inputLog[thisLogInd] != "undefined")
		if(inputLog[thisLogInd].time==thisSecond){
			var entCount = 0;
			while(inputLog[thisLogInd].time==thisSecond){
				var repeatPage = false;
				var repeatIp = false;
				var uid = Math.random();
				//console.log(pagesListed);
				for(var i = 0; i < pagesListed.length; i++){
					if(pagesListed[i][3]==inputLog[thisLogInd].file){
						repeatPage = true;
						break;
					}
				}
				for(var i = 0; i < ipsListed.length; i++){
					if(ipsListed[i][3]==inputLog[thisLogInd].ip){
						repeatIp = true;
						break;
					}
				}
				if(!repeatPage){
					pagesListed.push(['pageName','ent'+entCount,'s'+thisSecond,inputLog[thisLogInd].file]);
					$(".phpPong.table #pages").append('<div class="pageName ent'+entCount+' uid'+uid+'" id="s'+thisSecond+'">'+inputLog[thisLogInd].file+'</div>'); // make pagesListed to handle duplicates
				}
				if(!repeatIp){
					ipsListed.push(['ipAdd','ent'+entCount,'s'+thisSecond,inputLog[thisLogInd].ip]);
					$(".phpPong.table #ips").append('<div class="ipAdd ent'+entCount+' uid'+uid+'" id="s'+thisSecond+'">'+inputLog[thisLogInd].ip+'</div>'); // make ipsListed to handle duplicates
				}
				
				/*
				console.log($('.ipAdd.ent'+entCount+'#s'+thisSecond));*/
				var ipAddTop,ipAddLeft;
				if(!repeatIp){
					ipAddTop = ($('.ipAdd.ent'+entCount+'#s'+thisSecond).offset().top - $(window).scrollTop());
					ipAddLeft = ($('.ipAdd.ent'+entCount+'#s'+thisSecond).offset().left);
				} else {
					ipAddTop = ($('.ipAdd.'+ipsListed[i][1]+'#'+ipsListed[i][2]).offset().top - $(window).scrollTop())+5;
					ipAddLeft = ($('.ipAdd.'+ipsListed[i][1]+'#'+ipsListed[i][2]).offset().left);
				}

				$("body").append(
					'<div class="circle ent'+entCount+' ip'+inputLog[thisLogInd].ip+'" id="s'+thisSecond+'" \
					style="position:absolute;\
					top:'+ipAddTop+';\
					left:'+ipAddLeft+';"></div>');
				
				//console.log($('.pageName.ent'+entCount+'#s'+thisSecond).length);
				var pageNameTop,pageNameLeft;
				if(!repeatPage){
					pageNameTop = ($('.pageName.ent'+entCount+'#s'+thisSecond).offset().top - $(window).scrollTop())+5;
					pageNameLeft = ($('.pageName.ent'+entCount+'#s'+thisSecond).offset().left);
				} else {
					pageNameTop = ($('.pageName.'+pagesListed[i][1]+'#'+pagesListed[i][2]).offset().top - $(window).scrollTop())+5;
					pageNameLeft = ($('.pageName.'+pagesListed[i][1]+'#'+pagesListed[i][2]).offset().left);
				}
				animCircle("ent"+entCount,"s"+thisSecond,pageNameTop,pageNameLeft);
				function animCircle(entCount, thisSecond,pageNameTop,pageNameLeft){
					$('.circle.'+entCount+'#'+thisSecond).stop().animate({
						'top':pageNameTop+'px',
						'left':pageNameLeft+'px'
					},2000,
					function(){
						for(var i = 0; i < ipsListed.length; i++){
							//console.log("thisIp: "+ipsListed[i][3]+" circleIp: "+$(this).attr('class').split(" ")[2].substring(2));
							if($(this).attr('class').split(" ")[2].substring(2)==ipsListed[i][3]){
								ipAddTop = ($('.ipAdd.'+ipsListed[i][1]+'#'+ipsListed[i][2]).offset().top - $(window).scrollTop())+5;
								ipAddLeft = ($('.ipAdd.'+ipsListed[i][1]+'#'+ipsListed[i][2]).offset().left);
								console.log("ipAddTop: "+ipAddTop);
								break;
							}
						}
						//console.log("ipAddLeft: "+ipAddLeft);
						$(this).animate({/*
							'left':($('.ipAdd.'+$(this).attr('class').split(" ")[2]+'#'+$(this).attr('id')).offset().left),// Can modify this to make it more pong-like (bounce at inverted angle)
							'top':($('.ipAdd.'+$(this).attr('class').split(" ")[2]+'#'+$(this).attr('id')).offset().top - $(window).scrollTop())+5*/

							'left':ipAddLeft,// Can modify this to make it more pong-like (bounce at inverted angle)
							'top':ipAddTop
						},2000,function(){
							for(var i = 0; i < ipsListed.length; i++){
								if($(this).attr('class').split(" ")[2].substring(2)==ipsListed[i][3]){/*
									ipAddTop = ($('.ipAdd.'+ipsListed[i][1]+'#'+ipsListed[i][2]).offset().top - $(window).scrollTop());
									ipAddLeft = ($('.ipAdd.'+ipsListed[i][1]+'#'+ipsListed[i][2]).offset().left);*/

									console.log(ipsListed);
									if($('.ip'+ipsListed[i][3]).length>1){
										$('.ipAdd.'+ipsListed[i][1]+'#'+ipsListed[i][2]).fadeOut(700,function(){$(this).remove();});
										ipsListed.splice(i,1);
									}
									break;
								}
							}
							$(this).fadeOut(700,function(){$(this).remove();});
							
							// Adjust all dots
							/*
							console.log($(".circle"));
							for(var i = 0; i < $(".circle").length; i++){
								//$('.circle.'+$(".circle")[i].attr('class').split(" ")[1]+'#'+$(".circle")[i].attr('id')).stop();
								if($(".circle:nth-child("+(i+1)+")").length != 0){
									console.log($(".circle"));
									animCircle(
										$(".circle:nth-child("+(i+1)+")").attr('class').split(" ")[1],
										$(".circle:nth-child("+(i+1)+")").attr('id'),
										$('.ipAdd.'+$(".circle:nth-child("+(i+1)+")").attr('class').split(" ")[1]+'#'+$(".circle:nth-child("+(i+1)+")").attr('id')).offset().top,
										$('.ipAdd.'+$(".circle:nth-child("+(i+1)+")").attr('class').split(" ")[1]+'#'+$(".circle:nth-child("+(i+1)+")").attr('id')).offset().left
										);
								}
								
								
							}*/
						});
					});
				}
				entCount++;
				thisLogInd++;
				if(inputLog.length<=thisLogInd && window.gettingLog == false){
					getNewLog(endTime,Math.round((new Date()).getTime()/1000));
					clearInterval(this);
					break;
				}
			}
		}
		if(typeof inputLog[thisLogInd] != 'undefined' && (inputLog[thisLogInd].time<thisSecond || inputLog.length<=thisLogInd) && window.gettingLog == false){
			getNewLog(endTime,Math.round((new Date()).getTime()/1000));
			clearInterval(this);
		}

		console.log("thisLogInd: "+thisLogInd+" inputLog.length: "+inputLog.length);



		$(".phpPong.time").html(timeConverter((thisSecond+2)));
		thisSecond++;
		console.log("thisNewSecond: "+thisSecond);
	},1000);
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

window.gettingLog = false;
function getNewLog(start,end){
	console.log("No log data, waiting 10 seconds...");
	window.gettingLog = true;
	setTimeout(function(){
	 	$.post('serveLog.php',{'startTime':start,'endTime':end},function(data){
	 		if(data.substring(0,1)=="<"){getNewLog(start,end);return;}
	 		resData = JSON.parse(data);
	 		inputLog = resData[0];
	 		
	 		startTime = resData[2];
	 		inputLogStartInd = -1;
	 		thisLogInd = 0;
	 		for(var i = 0; i < inputLog.length; i++){
				if(inputLog[i].time >= thisSecond){
					inputLogStartInd = thisLogInd = i;
					//thisLogInd = i;
					thisSecond = inputLog[i].time;
					console.log("New log retrieved");
					endTime = resData[1];
					break;
				}
				//console.log(inputLog[i].time);
			}
			if(inputLog.length == 0){

				getNewLog(endTime,Math.round((new Date()).getTime()/1000));
			}
			window.gettingLog = false;
	 	});
 	},10000);
}

</script>

</body>
</html>