<html>
<head>
<title>SIF Project - Service Switching</title>
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
// set source variable
function setsource(source) {
	document.crashservice.source.value = source;
}
// set service variable
function setservice(service) {
	document.crashservice.service.value = service;
}
// toggles hold button and variable
function togglehold(elementObj)
{

	if(document.crashservice.hold.value ==0)
	 	{
			document.crashservice.hold.value=1;
			elementObj.className='holddepressed';
		}
	else
		{
			document.crashservice.hold.value=0;
			elementObj.className='raised';
		}
}
// toggles prime button and variable
function toggleprime(elementObj)
{

	if(document.crashservice.prime.value ==0)
	 	{
			document.crashservice.prime.value=1;
			elementObj.className='primedepressed';
			var arraySpans = document.body.getElementsByTagName("th");
			for(var i = 0; i < arraySpans.length; i++)
			{
				if(arraySpans[i].id.match('take'))
				{
					arraySpans[i].className = 'raised';
				}
			}
		}
	else
		{
			document.crashservice.prime.value=0;
			elementObj.className='raised';
			var arraySpans = document.body.getElementsByTagName("th");
			for(var i = 0; i < arraySpans.length; i++)
			{
				if(arraySpans[i].id.match('take'))
				{
					arraySpans[i].className = 'unprimed';
				}
			}
		}
}
// submit form to do crash switch
function crashswitch()
{
	if(document.crashservice.prime.value ==1)
	{
  		document.crashservice.submit();
  	}
}
function sourcepopup()
{
	sourcepopurl='sourcepopup.php?source='+document.crashservice.source.value
	sourcewindow= window.open (sourcepopurl, "sourcepopup", "location=0,status=0,scrollbars=1,menubar=0,width=400,height=300");
	if (window.focus) {sourcewindow.focus()}
}
function servicepopup()
{
	if (document.crashservice.service.value !="NULL")
	{
		servicepopurl='servicepopup.php?service='+document.crashservice.service.value
		sourcewindow= window.open (servicepopurl, "servicepopup", "location=0,status=0,scrollbars=1,menubar=0,width=400,height=300");
		if (window.focus) {servicewindow.focus()}
	}
}
//-->
</SCRIPT>
<body>
<table border=0 width=100%><tr><th width=80px><img src="wslogo.jpg" alt=""></th>
<th height=40 class="menubutton" onclick="location.href='index.php';">Main Menu</th>
<th height=40 class="menubutton" onclick="location.href='showserviceschedule.php';">Service Schedules</th>
<th height=40 class="menubutton" onclick="location.href='showlistenerschedule.php';">Listener Schedules</th>
<th height=40 class="menubutton" onclick="location.href='showmaterial.php';">Material Info</th>
<th height=40 class="mymenubutton">Crash Services</th>
<th height=40 class="menubutton" onclick="location.href='listenercrashswitch.php?servicetab=1&listenertab=1';">Crash Listeners</th>
<th height=40 class="menubutton" onclick="location.href='monitor.php';">Monitoring</th>
<th height=40 class="menubutton" onclick="location.href='sourcepairs.php';">Redundant Sources</th>
</tr><tr>
<td></table>
<h3>
SIF Project - Service Crash Switching</h3>
<?php
	if (!empty($_REQUEST["sourcetab"]) &&
	!empty($_REQUEST["servicetab"]))
	{
	$sourcetab=$_REQUEST["sourcetab"];
	$servicetab=$_REQUEST["servicetab"];
	require 'connect.php';
?>
<form method="post" action="crashservice.php" name="crashservice">
<input type="hidden" name="source" value="OFF">
<input type="hidden" name="service" value="NULL">
<input type="hidden" name="hold" value="0">
<input type="hidden" name="prime" value="0">
<div id="sourcebuttons">
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
			print "\n<th height=40 width=20% class=\"depressed\" colspan=2>{$row[tab_text]}</th>";
		}
		else
		{
			print "\n<th height=40 width=20% class=\"raised\" colspan=2 onclick=\"location.href='servicecrashswitch.php?sourcetab={$row[tab_index]}&servicetab={$servicetab}';\">{$row[tab_text]}</th>";
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
				print "<th height=40 width=20%  class=\"unused\" colspan=2>&nbsp;</td>";
				$emptyslotsinrow--;
			}
	}
	print "</tr><tr>";
	$sourcecount=0;

	$result=mysql_query("SELECT * FROM source where tab_index='$sourcetab' and enabled=1 and active=1 order by source asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		print "\n<td height=40 width=10% id=\"source{$sourcecount}\" class=\"raised\" onclick=\"toggleButton(this, /source/i);setsource('{$row[source]}');\"><b>{$row[source]}</b></td>";
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
			print "\n<td height=40 width=10% class=\"unused\">&nbsp;</td>";
			$emptyslotsinrow--;
		}
	}
?>
</table>
</td></tr></table>
</div>
<div id="destbuttons">
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
				print "\n<th height=40 width=20% class=\"depressed\" colspan=2>{$row[tab_text]}</th>";
				}
				else
				{
					print "\n<th height=40 width=20% class=\"raised\" colspan=2 onclick=\"location.href='servicecrashswitch.php?servicetab={$row[tab_index]}&sourcetab={$sourcetab}';\">{$row[tab_text]}</th>";
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
	$result=mysql_query("SELECT * FROM service where tab_index='$servicetab' and enabled=1 order by service asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		$currentsource="<font color=blue>(".$row[current_source].")</font>";
		if ($currentsource == "<font color=blue>()</font>")
		{
			$currentsource="<font color=blue>(OFF)</font>";
		}
		if ($row[locked] ==1)
		{
			$currentsource= $currentsource."&nbsp;<b><font color=red>*H*</font></b>";
		}

		print "\n<td height=40 width=10% id=\"service{$servicecount}\" class=\"raised\" onclick=\"toggleButton(this, /service/i);setservice('{$row[service]}');\"><b>{$row[service]}</b><br><i>{$currentsource}</i></td>";
		$servicecount++;
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
			print "<td height=40 width=10%>&nbsp;</td>";
			$emptyslotsinrow--;
		}
	}
?>
</tr></table>
</td></tr></table>
</div>
<div id="takebuttons">
<table width=100%>
<tr>
<?
	$sourcecount++;
	print "<td align=center height=40 width=10% id=\"source{$sourcecount}\" class=\"depressed\" onclick=\"toggleButton(this, /source/i);setsource('OFF');\"><b>OFF</b></td>";
?>
<th width=10%>&nbsp;</th>
<th class="raised" id="holdbutton" height=40 width=10% onclick="toggleprime(this);">Prime</th>
<th class="unprimed" id="take" height=40 width=10% onclick="crashswitch();">Take</th>
<th class="raised" id="holdbutton" height=40 width=10% onclick="togglehold(this);">Hold</th>
<th width=20%>&nbsp;</th>
<th class="raised" height=40 width=10% onClick="sourcepopup();">Source Routing</th>
<th class="raised" height=40 width=10% onClick="servicepopup();">Service Routing</th>
<th class="raised" height=40 width=10% onClick="history.go();">Refresh</th>
</tr></table>
</form>
</div>
<?
}
else
{
	echo "Error - Required tabs not defined<br>";
	echo $sourcetab;
	echo $servicetab;
}
?>

<div id="footer">
<hr>
&copy; 2009, Mark Patrick, BBC WS
</div>
</html>
