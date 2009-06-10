<html>
<head>
<title>SIF Project - Monitoring</title>
</head>
<link rel="stylesheet" type="text/css" href="crashswitch.css" media="screen,print">
<SCRIPT TYPE="text/javascript">
<!--
// toggle a button to simulate radio type buttons
function toggleButton(elementObj, idRegex) {
	var arraySpans = document.body.getElementsByTagName("td");

	for(var i = 0; i < arraySpans.length; i++)
	{
		if(arraySpans[i].id.match(idRegex))
		{
			arraySpans[i].className = 'raised';
		}
	}
	elementObj.className = 'depressed';
}
// set monsource variable
function setmonsource(source) {
	document.crashmon.monsource.value = source;
}
// submit form to do crash monitor
function crashswitchmon(mondest)
{
	document.crashmon.mon.value = mondest;
	document.crashmon.submit();
}
//-->
</SCRIPT>
<body>
<table border=0 width=100%><tr><th width=80px><img src="wslogo.jpg" alt=""></th>
<th class="menubutton" onclick="location.href='index.html';">Main Menu</th>
<th class="menubutton" onclick="location.href='showserviceschedule.php';">Service Schedules</th>
<th class="menubutton" onclick="location.href='showlistenerschedule.php';">Listener Schedules</th>
<th class="menubutton" onclick="location.href='showmaterial.php';">Material Info</th>
<th class="menubutton" onclick="location.href='servicecrashswitch.php?servicetab=1&sourcetab=1';">Crash Services</th>
<th class="menubutton" onclick="location.href='listenercrashswitch.php?servicetab=1&listenertab=1';">Crash Listeners</th>
<th class="mymenubutton">Monitoring</th>
<th class="menubutton" onclick="location.href='sourcepairs.php';">Redundant Sources</th>
</tr><tr>
<td></table>
<h3>
SIF Project - Monitoring</h3>
<?php
	if (empty($_REQUEST["sourcetab"]))
	{
		$sourcetab=1;
	}
	else
	{
		$sourcetab=$_REQUEST["sourcetab"];
	}
	if (empty($_REQUEST["servicetab"]))
	{
		$servicetab=1;
	}
	else
	{
		$servicetab=$_REQUEST["servicetab"];
	}
	require 'connect.php';
?>
<form method="post" action="crashmon.php" name="crashmon">
<input type="hidden" name="monsource" value="OFF">
<input type="hidden" name="mon" value="NULL">
<table width=100% height=240 border-0><tr><tr><td valign=top>

<table width=100% border=0><tr><th bgcolor="#CCCCFF" colspan=10>Sources:</th></tr>
<tr>
<?
	print "\n<input type=\"hidden\" name=\"sourcetab\" value=\"{$sourcetab}\">";
	print "\n<input type=\"hidden\" name=\"servicetab\" value=\"{$servicetab}\">";
	$sourcetabcount=0;
	$result=mysql_query("SELECT * FROM source_tabs where enabled=1 order by tab_index asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		if ($sourcetab==$row[tab_index])
		{
			print "\n<th width=20% class=\"depressed\" colspan=2>{$row[tab_text]}</th>";
		}
		else
		{
			print "\n<th width=20% class=\"raised\" colspan=2 onclick=\"location.href='monitor.php?sourcetab={$row[tab_index]}&servicetab={$servicetab}';\">{$row[tab_text]}</th>";
		}
		$sourcetabcount++;
		if ($sourcetabcount % 5 == 0)
				{
					print "</tr><tr>";
		}
	}
	$emptyslotsinrow=(5-($sourcetabcount % 5));
		// this will pad out any remaining slots so the table formats correctly
		if ($emptyslotsinrow < 5)
		{
			while($emptyslotsinrow > 0)
			{
				print "<th width=20%  class=\"unused\" colspan=2>&nbsp;</td>";
				$emptyslotsinrow--;
			}
	}
	print "</tr><tr>";
	$sourcecount=0;

	$result=mysql_query("SELECT * FROM source where tab_index='$sourcetab' order by source asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		print "\n<td width=10% id=\"source{$sourcecount}\" class=\"raised\" onclick=\"toggleButton(this, /source/i);setmonsource('{$row[source]}');\"><b>{$row[source]}</b></td>";
		$sourcecount++;
		if ($sourcecount % 10 == 0)
		{
			print "\n</tr><tr>";
		}
	}
	$emptyslotsinrow=(10-($sourcecount % 10));
	// this will pad out any remaining slots so the table formats correctly
	if ($emptyslotsinrow < 10)
	{
		while($emptyslotsinrow > 0)
		{
			print "\n<td width=10% class=\"unused\">&nbsp;</td>";
			$emptyslotsinrow--;
		}
	}
	$sourcecount++;
	print "</tr>";
	print "<tr><td width=10% id=\"source{$sourcecount}\" class=\"depressed\" onclick=\"toggleButton(this, /source/i);setmonsource('{$row[source]}');\"><b>OFF</b></td><td colspan=2>&nbsp;</td></tr>";
?>
</table>
</td></tr></table>
<table width=100% height=240 border=0>
<tr><td valign=top>
<table border=1 cellspacing=0 cellpadding=2 width=100%>
<table width=100% border=0><tr><th bgcolor="#CCCCFF" colspan=10>Services:</th></tr>

<tr>
<?
	$servicetabcount=0;
	$result=mysql_query("SELECT * FROM services_tabs where enabled=1 order by tab_index asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		if ($servicetab==$row[tab_index])
		{
				print "\n<th width=20% class=\"depressed\" colspan=2>{$row[tab_text]}</th>";
				}
				else
				{
					print "\n<th width=20% class=\"raised\" colspan=2 onclick=\"location.href='monitor.php?servicetab={$row[tab_index]}&sourcetab={$sourcetab}';\">{$row[tab_text]}</th>";
				}
				$servicetabcount++;
				if ($servicetabcount % 5 == 0)
						{
							print "</tr><tr>";
		}
	}
	$emptyslotsinrow=(5-($servicetabcount % 5));
	// this will pad out any remaining slots so the table formats correctly
	if ($emptyslotsinrow < 5)
	{
		while($emptyslotsinrow > 0)
		{
			print "<th width=20% class=\"unused\" colspan=2>&nbsp;</td>";
			$emptyslotsinrow--;
		}
	}
	print "</tr><tr>";
	$servicecount=0;
	$sourcecount++;
	$result=mysql_query("SELECT * FROM service where tab_index='$servicetab' order by service asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		print "\n<td width=10% id=\"source{$sourcecount}\" class=\"raised\" onclick=\"toggleButton(this, /source/i);setmonsource('{$row[service]}');\"><b>{$row[service]}</b></td>";
		$servicecount++;
		$sourcecount++;
		if ($servicecount % 10 == 0)
		{
			print "</tr><tr>";
		}
	}
	$emptyslotsinrow=(10-($servicecount % 10));
	// this will pad out any remaining slots so the table formats correctly
	if ($emptyslotsinrow < 10)
	{
		while($emptyslotsinrow > 0)
		{
			print "<td width=10%>&nbsp;</td>";
			$emptyslotsinrow--;
		}
	}
?>
</tr></table>
</td></tr></table>
<p>
<table width=100%>
<tr>
<?
	$moncount=0;
	$result=mysql_query("SELECT * FROM listener where role='MONITOR' order by listener asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		$currentservice="<font color=blue>(".$row[current_service].")</font>";
		if ($currentservice == "<font color=blue>()</font>")
		{
			$currentservice="<font color=blue>(OFF)</font>";
		}
		if ($row[locked] ==1)
		{
			$currentservice= $currentservice."&nbsp;<b><font color=red>*H*</font></b>";
		}

		print "\n<td width=10%  height=60 id=\"mon{$moncount}\" class=\"raised\" onclick=\"crashswitchmon('{$row[listener]}');\"><b>{$row[listener]}</b><br><i>{$currentservice}</i></td>";
		$moncount++;
		if ($moncount % 10 == 0)
		{
			print "</tr><tr>";
		}
	}
	$emptyslotsinrow=(9-($moncount % 10));
	// this will pad out any remaining slots so the table formats correctly
	if ($emptyslotsinrow < 9)
	{
		while($emptyslotsinrow > 0)
		{
			print "<td width=10% height=60>&nbsp;</td>";
			$emptyslotsinrow--;
		}
	}
?>
<th class="raised" width=10% onClick="history.go()">Refresh</th>
</tr></table>
</form>
<hr>
<div id="footer">
&copy; 2009, Mark Patrick, BBC WS
</div>
</html>
