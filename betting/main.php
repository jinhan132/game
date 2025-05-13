<?php
error_reporting(0);

header("Content-Type:text/html; charset=euc-kr");

require_once("/home/www/inc/define.h");
require_once("$docPath/inc/session.inc");

$userId		= trim($_POST['uid']);
$userPwd		= trim($_POST['pwd']);


$userId = $s_uid;

if($userId == ""){
	exit;
}


if($_SERVER['REMOTE_ADDR'] == '172.16.100.34')
{
$userId = "예압님하";
$userPwd = "b424bb671b1d41692a7ee212eeddc9b2f49fd48023ab7e6586082c56032ac51c";
}


if($userId == "" or $userPwd == "")
{
	echo "{\"charge_msg\":\"회원정보가 아닙니다.\"}";
	exit();
}
else
{
	$deUserPwd	= @exec("/home/www/client/mycrypt_decoding '$userPwd'");
	
	require_once("$docPath/inc/db_con2.inc");

	$dbUserId				= "";
	$dbUserNum			= 0;
	$dbUserExpireEnd		= "0000-00-00 00:00:00";
	$dbUserLevel			= 0;
	$dbUserCyberMoney	= 0;
	$dbUserCyberMoney	= "";

	$sql						= "SELECT user.user_id, user.user_num, user_info.expire_end, user_info.level, user_info.cyberMoney, user_info.c_club_url, user.rank_num FROM user, user_info WHERE user.user_id = user_info.user_id AND user.user_id = '".$userId."' AND password = old_password('".$deUserPwd."') LIMIT 1";
	$rs							= mysql_query($sql, $con2);

	if(mysql_num_rows($rs) > 0)
	{
		$dbUserId				= mysql_result($rs, 0, "user_id");
		$dbUserNum			= mysql_result($rs, 0, "user_num");
		$dbUserExpireEnd		= mysql_result($rs, 0, "expire_end");
		$dbUserLevel			= mysql_result($rs, 0, "level");
		$dbUserCyberMoney	= mysql_result($rs, 0, "cyberMoney");
		$dbUserClubUrl			= mysql_result($rs, 0, "c_club_url");

		if($dbUserId == $userId)
		{
			$sText			= date("His")."|".$dbUserId."|".$dbUserNum."|".$deUserPwd."|".$dbUserLevel."|".$dbUserExpireEnd."|".$dbUserClubUrl;
			$sCode		= "mobilebet";
			$betInfo		= Encode($sText, $sCode);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport"  id="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link rel="stylesheet" href="/mobile/js/mocss.css" type="text/css">
<title>승부예측 현황판</title>
</head>
<body>
<div id='content'>
</div>
<span id='betuser' style='display:none'><?=$betInfo?></span>
</body>
<script src="/mobile/js/jquery.min.js"></script>
<script>
	document.domain = "tygem.com";

function pageLoad()
{
	$.ajax({
		async:false,
		cache:false,
		type: 'POST',
		dataType: 'html',
		data: { 'bet_info' : $("#betuser").text() },
		url: 'betlive.php?temp='+new Date().getTime(),
		success:function(data){
			$("#content").empty().append(data);
		}
	});
}

function pageLoadPage(temp)
{
	$.ajax({
		async:false,
		cache:false,
		type: 'POST',
		dataType: 'html',
		data: { 'bet_info' : $("#betuser").text(), 'page' : temp },
		url: 'betresult.php?temp='+new Date().getTime(),
		success:function(data){
			$("#content").empty().append(data);
		}
	});
}

function goDetail(tempSeq, tempSno, tempRno)
{
	if(tempSeq != "" && tempSno != "" && tempRno != "")
	{
		$.ajax({
			async:false,
			cache:false,
			type: 'POST',
			dataType: 'html',
			data: { 'bet_info' : $("#betuser").text(), 'seq' : tempSeq, 'sno' : tempSno, 'rno' : tempRno },
			url: 'betdetail.php?temp='+new Date().getTime(),
			success:function(data){
				$("#content").empty().append(data);
			}
		});
	}
}


function goRoom(tempSno, tempRno, tempBplayer, tempWplayer, tempBrank, tempWrank, tempBccode, tempWccode)
{
	window.parent.postMessage('[TYGEM]server='+tempSno+'&roomNo='+tempRno+'&playerB='+tempBplayer+'&playerW='+tempWplayer+'&geupB='+tempBrank+'&geupW='+tempWrank+'&sCodeB='+tempBccode+'&sCodeW='+tempWccode+'&temp='+new Date().getTime()+'&', "*");
	//window.location.href = 'betgo.php?server='+tempSno+'&roomNo='+tempRno+'&playerB='+tempBplayer+'&playerW='+tempWplayer+'&geupB='+tempBrank+'&geupW='+tempWrank+'&sCodeB='+tempBccode+'&sCodeW='+tempWccode+'&temp='+new Date().getTime()+'&';
}

$(document).ready(function(){
	pageLoad();
});
</script>
</html>
<?php
		}
		else
		{
			mysql_close($con2);

			echo "{\"charge_msg\":\"회원정보가 아닙니다.2\"}";
			exit();
		}
	}
	else
	{
		mysql_close($con2);

		echo "{\"charge_msg\":\"회원정보가 아닙니다.1\"}";
		exit();
	}

	mysql_free_result($rs);
	$sql			= "";

	mysql_close($con2);
}
?>