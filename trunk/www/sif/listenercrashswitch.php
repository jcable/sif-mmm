<?php
	require_once("sif.inc");
	require_once("header.php");
	$page = "Crash Listeners";
	sif_header($page, "crashswitch.css");
?>
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
// set service variable
function setservice(service) {
	document.crashlistener.service.value = service;
}
// set listener variable
function setlistener(listener) {
	document.crashlistener.listener.value = listener;
}
// toggles hold button and variable
function togglehold(elementObj)
{

	if(document.crashlistener.hold.value ==0)
	 	{
			document.crashlistener.hold.value=1;
			elementObj.className='holddepressed';
		}
	else
		{
			document.crashlistener.hold.value=0;
			elementObj.className='raised';
		}
}
// toggles prime button and variable
function toggleprime(elementObj)
{

	if(document.crashlistener.prime.value ==0)
	 	{
			document.crashlistener.prime.value=1;
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
			document.crashlistener.prime.value=0;
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
	if(document.crashlistener.prime.value ==1)
	{
  		document.crashlistener.submit();
  	}
}
function servicepopup()
{

	if (document.crashlistener.service.value !="OFF")
	{
		servicepopurl='servicepopup.php?service='+document.crashlistener.service.value
		sourcewindow= window.open (servicepopurl, "servicepopup", "location=0,status=0,scrollbars=1,menubar=0,width=400,height=300");
		if (window.focus) {servicewindow.focus()}
	}
}

function reload_panel(sourcetab, desttab)
{
	location.href='listenercrashswitch.php?servicetab='+sourcetab+'&listenertab='+desttab;
}

//-->
</SCRIPT>
<?php
	sif_buttons($page);
	if (isset($_REQUEST["servicetab"]) && isset($_REQUEST["listenertab"]))
	{
		$servicetab=$_REQUEST["servicetab"];
		$listenertab=$_REQUEST["listenertab"];
	}
	else
	{
		$servicetab=1;
		$listenertab=1;
	}
	$dbh = connect();
?>
<form method="post" action="crashlistener.php" name="crashlistener">
<input type="hidden" name="service" value="OFF">
<input type="hidden" name="listener" value="NULL">
<input type="hidden" name="hold" value="0">
<input type="hidden" name="prime" value="0">
<div id="sourcebuttons">
<?php showservicebuttons($dbh, "source", $servicetab, $listenertab); ?>
</div>
<div id="destbuttons">
<table width=100% height=240 border=0>
<tr><td valign=top>
<table border=1 cellspacing=0 cellpadding=2 width=100%>
<table width=100% border=0><tr><th bgcolor="#CCCCFF" colspan=10>Listeners:</th></tr>

<tr>
<?php
	$listenertabcount=0;
	$stmt=$dbh->query("SELECT * FROM listener_tabs where enabled=1 order by tab_index asc");
	while($row=$stmt->fetch(PDO::FETCH_ASSOC))
	{
		if ($listenertab==$row[tab_index])
		{
				print "\n<th height=40 width=20% class=\"depressed\" colspan=2>{$row[tab_text]}</th>";
				}
				else
				{
					print "\n<th height=40 width=20% class=\"raised\" colspan=2 onclick=\"location.href='listenercrashswitch.php?listenertab={$row[tab_index]}&servicetab={$servicetab}';\">{$row[tab_text]}</th>";
				}
				$listenertabcount++;
				if ($listenertabcount % 5 == 0)
						{
							print "</tr><tr>";
		}
	}
	$emptyslotsinrow=(5-($listenertabcount % 5));
	// this will pad out any remaining slots so the table formats correctly
	if ($emptyslotsinrow < 5)
	{
		while($emptyslotsinrow > 0)
		{
			print "<th height=40 width=20% class=\"unused\" colspan=2>&nbsp;</td>";
			$emptyslotsinrow--;
		}
	}
	print "</tr><tr>";
	$listenercount=0;

	$rows = active_events_as_run($dbh, 'LISTENER');
	$events = array();
	foreach($rows as $event)
	{
		$events[$event["output"]] = $event;
	}

	$stmt=$dbh->prepare("SELECT id FROM edge WHERE kind='LISTENER' AND tab_index=? ORDER BY id ASC");
	$stmt->execute(array($listenertab));
	while($row=$stmt->fetch(PDO::FETCH_ASSOC))
	{
		$id = $row["id"];
		if(isset($events[$service]))
		{
			$event = $events[$service];
			$currentservice=$event["input"];
		}
		else
		{
			$currentservice="OFF";
		}

		print "\n<td height=40 width=10% id=\"listener{$listenercount}\" class=\"raised\" onclick=\"toggleButton(this, /listener/i);setlistener('$id');\">";
		print "<span class=\"servicelabel\">$id</span>";
		print "<br>";
		print "<span class=\"sourcelabel\">($currentservice)</span>";
		print "</td>";
		$listenercount++;
		if ($listenercount % 10 == 0)
		{
			print "</tr><tr>";
		}
	}
	$emptyslotsinrow=(10-($listenercount % 10));
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
<?php
	$servicecount++;
	print "<td align=center height=40 width=10% id=\"service{$servicecount}\" class=\"depressed\" onclick=\"toggleButton(this, /service/i);setservice('OFF');\"><b>OFF</b></td>";
?>
<th width=10%>&nbsp;</th>
<th class="raised" id="holdbutton" height=40 width=10% onclick="toggleprime(this);">Prime</th>
<th class="unprimed" id="take" height=40 width=10% onclick="crashswitch();">Take</th>
<th class="raised" id="holdbutton" height=40 width=10% onclick="togglehold(this);">Hold</th>
<th width=30%>&nbsp;</th>
<th class="raised" height=40 width=10% onClick="servicepopup();">Service Routing</th>
<th class="raised" height=40 width=10% onClick="history.go();">Refresh</th>
</tr></table>
</form>
</div>
<?php sif_footer(); ?>
