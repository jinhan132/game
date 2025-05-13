<?php
error_reporting(0);

header("Content-Type:text/html; charset=euc-kr");

require_once("/home/www/inc/define.h");
require_once("$docPath/inc/session.inc");
require_once("$docPath/inc/db_connect.inc");

$betInfo			= trim($_POST['bet_info']);
$sCode			= "mobilebet";
$deBetInfo		= Decode($betInfo, $sCode);

$userInfo			= explode("|", $deBetInfo);

$userId			= $userInfo[1];
$userNum		= $userInfo[2];
$userLevel		= $userInfo[4];
$userExpireEnd	= $userInfo[5];
$userPaid			= false;

if($userExpireEnd > date("Y-m-d H:i:s") and $userLevel > 0)
	$userPaid	= true;

if($userId != "" and $userNum != "" and is_numeric($userNum))
{
?>
<div id="tabmenu">
	<ul class="betmenu"> 
		<li id="bettabmenulive" class="bmenu_on" style="width:33%">실시간 내역</li>
		<li id="bettabmenuresult" class="bmenu_off" onclick="pageLoadPage('1');" style='cursor:pointer;width:33%'>완료내역</li>
		<li id="bettabmenuresult" class="bmenu_off" onclick="pagePrePage('1');" style='cursor:pointer;padding-top:20px;width:34%;height:69px'>사전승부<br>예측내역</li>
	</ul>
</div>
<div id="con_list">
	<table class="list1">
	<colgroup>
		<col width="14%" />
		<col width="21%" />
		<col width="10%" />
		<col width="21%" />
		<col width="14%" />
		<col width="20%" />
	</colgroup>
<?php
	$sql				= "SELECT G.game_seq, G.game_time, G.black_id, G.black_nick, G.black_ccode, G.white_id, G.white_nick, G.white_ccode, G.server_no, G.room_no, G.black_ranknum, G.white_ranknum FROM bet_game G, bet_list L WHERE G.game_seq = L.game_seq AND G.game_result = '0' AND L.user_num = ".$userNum." GROUP BY G.game_seq ORDER BY G.game_time DESC LIMIT 20";
	$rs					= mysql_query($sql, $connect);

	if(mysql_num_rows($rs) > 0)
	{
		while($rows = mysql_fetch_array($rs))
		{
			$bUser			= "";
			$wUser			= "";

			$gameSeq		= $rows['game_seq'];
			$betDate			= $rows['game_time'];
			$roomNo			= $rows['room_no'];
			$serverNo		= $rows['server_no'];
			$roomGoLink	= "javascript:alert('유효회원 기능입니다!');";

			if($userPaid || 1 == 1)
				$roomGoLink	= "javascript:goRoom('".$serverNo."', '".$roomNo."', '".$rows['black_nick']."', '".$rows['white_nick']."', '".$rows['black_ranknum']."', '".$rows['white_ranknum']."', '".$rows['black_ccode']."', '".$rows['white_ccode']."');";
				
				//$roomGoLink = "javascript:window.parent.postMessage('[TYGEM]server=".$serverNo."&roomNo=".$roomNo."&playerB=".$rows['black_nick']."&playerW=".$rows['white_nick']."&geupB=".$rows['black_ranknum']."&geupW=".$rows['white_ranknum']."&sCodeB=".$rows['black_ccode']."&sCodeW=".$rows['white_ccode']."&temp='+new Date().getTime()+'&', '*');";


			if($rows['black_ccode'] == 0)
				$bUser	= $rows['black_id'];
			else
				$bUser	= $rows['black_nick'];

			if($rows['white_ccode'] == 0)
				$wUser	= $rows['white_id'];
			else
				$wUser	= $rows['white_nick'];

			$betMyPoint		= 0;

			$subSql			= "SELECT sum(L.bet_money) as money FROM bet_game G, bet_list L WHERE G.game_seq = L.game_seq AND L.user_num = ".$userNum." AND G.game_seq = ".$gameSeq;
			$subRs			= mysql_query($subSql, $connect);

			if(mysql_num_rows($subRs) > 0)
				$betMyPoint		= mysql_result($subRs, 0, "money");

			mysql_free_result($subRs);
			$subSql			= "";
?>
	<tr class="type1">
		<td><img src="http://img.tygem.com/images/betting/mobile/2016_ai_icon_white.png"></td>
		<td class="fontl"><?=$wUser?></td>
		<td class="vs">VS</td>
		<td class="fontr"><?=$bUser?></td>
		<td><img src="http://img.tygem.com/images/betting/mobile/2016_ai_icon_black.png"></td>
		<td class="enter" rowspan="2"><a href="<?=$roomGoLink?>">입장하기</a></td>
	</tr>
	<tr class="type2">
		<td class="bg_or fontl" colspan="4"><span class="mn">나의 예측금액 |</span>  <?=number_format($betMyPoint)?>PT</td>
		<td class="bg_wh"><a href="javascript:goDetail('<?=$gameSeq?>', '<?=$serverNo?>', '<?=$roomNo?>');">예측내역</a></td>
	</tr>
<?php
		}
	}

	mysql_free_result($rs);
	$sql				= "";

	mysql_close($connect);
?>
	</table>
</div>
<?php
}
?>