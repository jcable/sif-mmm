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
<th class="menubutton" onclick="location.href='monitor.php';">Monitoring</th>
<th class="mymenubutton">Redundant Sources</th>
</tr><tr>
<td></table>
<h3>
SIF Project - Source Redundancy Pairs</h3>
<?php
	if (empty($_REQUEST["pairtab"]))
	{
		$pairtab=1;
	}
	else
	{
		$pairtab=$_REQUEST["pairtab"];
	}
	require 'connect.php';
?>

<table width=100% height=600 border-0><tr><tr><td valign=top>

<table width=100% border=0>
<tr>
<?
	print "\n<input type=\"hidden\" name=\"pairtab\" value=\"{$pairtab}\">";
	$sourcetabcount=0;
	$result=mysql_query("SELECT * FROM redundancy_tabs left join redundancy on redundancy_tabs.tab_index=redundancy.tab_index where enabled='1' and redundancy_type='SOURCE' order by redundancy.tab_index asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		if ($pairtab==$row[tab_index])
		{
			print "\n<th width=20% class=\"depressed\">{$row[tab_text]}</th>";
		}
		else
		{
			print "\n<th width=20% class=\"raised\" onclick=\"location.href='sourcepairs.php?pairtab={$row[tab_index]}';\">{$row[tab_text]}</th>";
		}
		$pairtabcount++;
		if ($pairtabcount % 5 == 0)
				{
					print "</tr><tr>";
		}
	}
	$emptyslotsinrow=(5-($pairtabcount % 5));
		// this will pad out any remaining slots so the table formats correctly
		if ($emptyslotsinrow < 5)
		{
			while($emptyslotsinrow > 0)
			{
				print "\n<th width=20%  class=\"unused\">&nbsp;</td>";
				$emptyslotsinrow--;
			}
	}
	print "</tr><tr>";
	$paircount=0;
	// now do the actual redundancy pairs
	$result=mysql_query("SELECT * FROM redundancy where tab_index='$pairtab' order by redundancy_text asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		$main=$row[main];
		$reserve=$row[reserve];
		if ($row[redundancy_type]=="LISTENER")
		// this shows listener pairs, so this should never be seen, but just in case it is ever needed...
		{
			print "\n<td width=20% class=\"unused\"><i><b>Listener Pair: {$row[redundancy_text]}</b></i><br>";
			print "\nMain: {$main}<br>";
			print "\nReserve: {$reserve}</td>";
		}
		else
		{
			print "\n<form method=\"post\" action=\"switchpair.php\" name=\"switchpair{$paircount}\">";
			print "\n<input type=\"hidden\" name=\"pair\" value=\"{$row[redundancy_text]}\">";
			print "\n<input type=\"hidden\" name=\"pairtab\" value=\"{$pairtab}\">";
			print "\n<td width=20% class=\"unused\"><table width=100%><tr><td><i><b><font color=blue>Source Pair: {$row[redundancy_text]}</font></b></i><br>";
			$mainactiveresult=mysql_query("SELECT * FROM source where source='$main' and active='1'", $connection);
			while($mainactiverow= mysql_fetch_array($mainactiveresult))
			{
				// echo $mainactiverow[source];
				if ($mainactiverow[source]==$main)
				{
					print "\nMain: <input type=\"radio\" name=\"active\" value=\"{$main}\" checked><b>{$main}</b><br>";
					print "\nReserve: <input type=\"radio\" name=\"active\" value=\"{$reserve}\">{$reserve}";
				}
			}
			$resactiveresult=mysql_query("SELECT * FROM source where source='$reserve'and active='1'", $connection);
			while($resactiverow= mysql_fetch_array($resactiveresult))
			{
				// echo $resactiverow[source];
				if ($resactiverow[source]==$reserve)
				{
					print "\nMain: <input type=\"radio\" name=\"active\" value=\"{$main}\">{$main}<br>";
					print "\nReserve: <input type=\"radio\" name=\"active\" value=\"{$reserve}\" checked><b>{$reserve}</b>";
				}
			}
			print "\n</td><td align=right>&nbsp;&nbsp;<input type=submit value=\"Switch\">";
			print "\n</form></td></tr></table>";
		}
		$paircount++;
		if ($paircount % 5 == 0)
		{
			print "\n</tr><tr>";
		}
	}
	$emptyslotsinrow=(5-($paircount % 10));
	// this will pad out any remaining slots so the table formats correctly
	if ($emptyslotsinrow < 5)
	{
		while($emptyslotsinrow > 0)
		{
			print "\n<td width=20% class=\"unused\">&nbsp;</td>";
			$emptyslotsinrow--;
		}
	}
	$paircount++;
	print "</tr>";
	?>
</table>
</td></tr></table>

<p>
<table width=100%>
<tr>
<th width=90%>&nbsp;</th>
<th class="raised" height=60 width=10% onClick="history.go()">Refresh</th>
</tr></table>
</form>
<hr>
<div id="footer">
&copy; 2009, Mark Patrick, BBC WS
</div>
</html>
