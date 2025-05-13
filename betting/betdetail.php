<?php
error_reporting(0);

header("Content-Type:text/html; charset=euc-kr");

require_once("/home/www/inc/define.h");
require_once("$docPath/inc/session.inc");

$betInfo			= trim($_POST['bet_info']);
$gameSeq		= trim($_POST['seq']);
$serverNo		= trim($_POST['sno']);
$roomNo			= trim($_POST['rno']);

if($gameSeq != "" and $serverNo != "" and $roomNo != "")
{
	if(is_numeric($gameSeq) and is_numeric($serverNo) and is_numeric($roomNo))
	{
		require_once("$docPath/inc/db_connect.inc");
		require_once("$docPath/mobile/fun/fun_betting.php");

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

		$sql				= "SELECT G.game_time, G.black_id, G.black_nick, G.black_ccode, G.white_id, G.white_nick, G.white_ccode FROM bet_game G, bet_list L WHERE G.game_seq = L.game_seq AND L.user_num = ".$userNum." AND G.game_result = '0' AND G.game_Seq = ".$gameSeq." AND G.server_no = ".$serverNo." AND G.room_no = ".$roomNo." LIMIT 1";
		$rs					= mysql_query($sql, $connect);

		if(mysql_num_rows($rs) > 0)
		{
			$row				= mysql_fetch_array($rs);

			$bUser			= "";
			$wUser			= "";

			$betDate			= $row['game_time'];
			$roomGoLink	= "javascript:alert('유효회원 기능입니다!');";

			if($userPaid)
				$roomGoLink	= "javascript:moveRoom();";

			if($row['black_ccode'] == 0)
				$bUser	= $row['black_id'];
			else
				$bUser	= $row['black_nick'];

			if($row['white_ccode'] == 0)
				$wUser	= $row['white_id'];
			else
				$wUser	= $row['white_nick'];

			$roomGoLink	= "javascript:alert('유효회원 기능입니다!');";

			if($userPaid)
				//$roomGoLink	= "javascript:moveRoom('');";
			$roomGoLink	= "javascript:goRoom('".$serverNo."', '".$roomNo."', '".$row['black_nick']."', '".$row['white_nick']."', '".$row['black_ranknum']."', '".$row['white_ranknum']."', '".$row['black_ccode']."', '".$row['white_ccode']."');";
?>
<div id="tit">
	<h1><span class="big_t"><?=$wUser?>(백) VS <?=$bUser?>(흑)</span>의 예측내역</h1>
</div>
<div id="con_list">
	<table class="list1">
	<colgroup>
		<col width="20%" />
		<col width="30%" />
		<col width="50%" />
	</colgroup>
	<tr class="type3">
		<td class="bg_br">대국일시</td>
		<td class="bg_wh1"><?=$row['game_time']?></td>
		<td class="bg_wh2"><?=getServerName($serverNo)?> <?=$roomNo?>번방</td>
	</tr>  
	</table>
	<table class="list2">
	<colgroup>
		<col width="8%" />
		<col width="15%" />
		<col width="10%" />
		<col width="11%" />
		<col width="28%" />
		<col width="28%" />
	</colgroup>
<?php
			$subSql				= "SELECT L.bet_color, L.bet_section, L.bet_item1, L.bet_item2, L.bet_money, L.bet_ratio, L.bet_time FROM bet_game G, bet_list L WHERE G.game_seq = L.game_seq AND G.game_result = '0' AND L.user_num = ".$userNum." AND G.game_Seq = ".$gameSeq." AND G.server_no = ".$serverNo." AND G.room_no = ".$roomNo." ORDER BY L.idx";
			$subRs				= mysql_query($subSql, $connect);

			if(mysql_num_rows($subRs) > 0)
			{
?>
	<tr>
		<th>구간</th>
		<th>예측일시</th>
		<th>수익률</th>
		<th>아이템</th>
		<th>예측금액 (PT)</th>
		<th class="end">예상획득액 (PT)</th>
	</tr>
<?php
				$num	= 1;

				while($subRows = mysql_fetch_array($subRs))
				{
					$betColor	= "";
					$betRatio	= "-";

					if($subRows['bet_color'] == 'B')
						$betColor	= "흑";
					else if($subRows['bet_color'] == 'W')
						$betColor	= "백";

					if($subRows['bet_ratio'] > 0)
						$betRatio	= $subRows['bet_ratio']."배";

					$itemMoney	= 0;

					if($subRows['bet_item1'] == "98" or $subRows['bet_item2'] == "98")
						$itemMoney	= 2000000;
					
					if($subRows['bet_item1'] == "97" or $subRows['bet_item2'] == "97")
						$itemMoney	= 20000000;
					
					if($subRows['bet_item1'] == "100" or $subRows['bet_item2'] == "100")
						$itemMoney	= 5000000;
					
					if($subRows['bet_item1'] == "101" or $subRows['bet_item2'] == "101")
						$itemMoney	= 10000000;
					
					if($subRows['bet_item1'] == "103" or $subRows['bet_item2'] == "103")
						$itemMoney	= 100000;
					
					if($subRows['bet_item1'] == "104" or $subRows['bet_item2'] == "104")
						$itemMoney	= 30000000;
					
					if($subRows['bet_item1'] == "105" or $subRows['bet_item2'] == "105")
						$itemMoney	= 40000000;

					$betResul	= getBetPrice($subRows['bet_ratio'],$itemMoney, $subRows['bet_money']);
?>
	<tr>
		<td><?=$subRows["bet_section"]?><br />(<?=$num?>)</td>
		<td><?=substr($subRows['bet_time'], 2, 8)?><br /><?=substr($subRows['bet_time'], 11, 5)?></td>
		<td><?=$betRatio?></td>
		<td><?=getItemIcon($subRows['bet_item1'])?><?=getItemIcon($subRows['bet_item2'])?></td>
		<td><?=$betColor?> <?=number_format($subRows["bet_money"])?></td>
		<td class="end"><?=number_format($betResul)?></td>
	</tr>
<?php
					$num++;
				}
?>
	</table> 
</div>
<div id="con_btn">
	<p><span class="box mr10"><a href="<?=$roomGoLink?>">입장</a></span><span class="box"><a href="javascript:pageLoad();">닫기</a></span></p>
</div>
<?php
			}
		}

		mysql_free_result($rs);
		$sql				= "";

		mysql_close($connect);
	}
}
?>