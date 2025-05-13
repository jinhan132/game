<?php
error_reporting(0);

header("Content-Type:text/html; charset=euc-kr");

require_once("/home/www/inc/define.h");
require_once("$docPath/inc/session.inc");
require_once("$docPath/mobile/fun/fun_betting.php");

$betInfo			= trim($_POST['bet_info']);
$page				= trim($_POST['page']);

if($page == "" or $page <= 0)
	$page		= 1;

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
	require_once("$docPath/mobile/fun/fun_betting.php");

	$userDir				= getUserDir($userNum);
	$userBetFile			= $userDir."/history/bet.history";

	$userReadBetFile	= null;

	if(file_exists($userBetFile))
		$userReadBetFile	= file($userBetFile);

	$userBetArray		= array();

	if(count($userReadBetFile) > 0)
	{
		while(list($lineNum, $line) = each($userReadBetFile))
		{

		$line_check = explode(" ",$line);

			if(strpos($line, "LOSS") && $line_check[27] == 1)
				array_push($userBetArray, $line);
			else if(strpos($line, "EARN") && $line_check[27] == 1)
				array_push($userBetArray, $line);
		}
	}
	rsort($userBetArray);

	$totalUserBet		= count($userBetArray);
	$pageSize			= 5;
	
	$pageStartNum		= ($page - 1) * $pageSize;
	$pageEndNum		= $page * $pageSize;

	$viewCount			= 5;
	$nowPage			= $page - ( ($page-1) % $viewCount );

	$backPage			= $page - 1;
	$nextPage			= $page + 1;
	$totalPage			= ceil($totalUserBet/$pageSize);
?>
<div id="tabmenu">
	<ul class="betmenu"> 
		<li id="bettabmenulive" class="bmenu_off" onclick="pageLoad('live');" style='cursor:pointer;width:33%'>실시간 내역</li>
		<li id="bettabmenuresult" class="bmenu_off" onclick="pageLoadPage('1');" style='cursor:pointer;width:33%'>완료내역</li>
		<li class="bmenu_on" style="padding-top:20px;width:34%;height:69px">사전승부<br>예측내역</li>
	</ul>
</div>
<div id="con_list">
<?php
	for($i = $pageStartNum; $i < $pageEndNum; $i++)
	{
		$userBetDetail	= explode(" ", $userBetArray[$i]);

		if($i < $totalUserBet)
		{
			$userBetMent		= "예측성공";
			$userBetMoney		= "<span class=\"fontor\">+".number_format($userBetDetail[10])."</span>";
			$userBetDay			= $userBetDetail[0]." ".$userBetDetail[1];
			$userBetDay			= date("y-m-d H:i", strtotime($userBetDay));
			$userBetOrgMoney	= number_format($userBetDetail[16]);
			$userBetRatio		= number_format($userBetDetail[19], 1);
			$userBetItem1		= getItemIcon($userBetDetail[21]);
			$userBetItem2		= getItemIcon($userBetDetail[23]);
			
			$userBetWinner		= "";
			$userBetWin			= explode(",", $userBetDetail[12]);

			if(count($userBetWin) > 1)
			{
				if($userBetWin[2] == 0)
					$userBetWinner	= $userBetWin[0];
				else
					$userBetWinner	= $userBetWin[1];
			}
			else
				$userBetWinner	= $userBetWin[0];

			$userBetLoser		= "";
			$userBetLose		= explode(",", $userBetDetail[14]);

			if(count($userBetLose) > 1)
			{
				if($userBetLose[2] == 0)
					$userBetLoser	= $userBetLose[0];
				else
					$userBetLoser	= $userBetLose[1];
			}
			else
				$userBetLoser	= $userBetLose[0];

			if($userBetDetail[6] == "LOSS")
			{
				$userBetMent	= "<span class=\"fail\">예측실패</span>";
				$userBetMoney	= number_format($userBetDetail[10]);
			}
?>
	<table class="list1">
	<colgroup>
		<col width="20%" />
		<col width="80%" />
	</colgroup>
	<tr class="type4">
		<td class="bg_br"><?=$userBetMent?></td>
		<td><span class="fontor">(승)<?=$userBetWinner?></span> VS <?=$userBetLoser?>(패)</td>
	</tr>
	</table>
	<table class="list2">
	<colgroup>
		<col width="15%" />
		<col width="10%" />
		<col width="11%" />
		<col width="32%" />
		<col width="32%" />
	</colgroup>
	<tr>
		<th>예측일시</th>
		<th>수익률</th>
		<th>아이템</th>
		<th>예측금액 (PT)</th>
		<th class="end">획득액 (PT)</th>
	</tr>
	<tr>
		<td><?=substr($userBetDay, 0, 8)?><br/><?=substr($userBetDay, 9)?></td>
		<td><?=$userBetRatio?>배</td>
		<td><?=$userBetItem1?><?=$userBetItem2?></td>
		<td><?=$userBetOrgMoney?></td>
		<td class="end"><?=$userBetMoney?></td>
	</tr>
	</table>
<?php
		}
	}
?>
	<div class="page">
<?php	if($backPage >= 1){ ?>
		<a href="javascript:pagePrePage('<?=$backPage?>');"><span class="box1">이 전</span></a>
<?php	}else{ ?>
		<a href="#"><span class="box1">이 전</span></a>
<?php	} ?>
<?php
	$i					= $nowPage;
	while($i <= ($nowPage+$viewCount-1))
	{
		if($i == $page)
		{
?>
			<a href='#'><span class="f_orange"><?=$i?></span></a>
<?php
		}
		else
		{
			if($page<=0)
			{
			}
			else
			{
?>
			<a href="javascript:pagePrePage('<?=$i?>');"><?=$i?></a>
<?php
			}
		}

		if($i >= $totalPage)
			break;

		$i++;
	}
?>
<?php	if($nextPage <= $totalPage){ ?>
		<a href="javascript:pagePrePage('<?=$nextPage?>');"><span class="box1">다 음</span></a>
<?php	}else{ ?>
		<a href="#"><span class="box1">다 음</span></a>
<?php	} ?>
	</div>
</div>
<?php
}
?>