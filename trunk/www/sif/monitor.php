<?php
	require_once("sif.inc");
	require_once("header.php");
	$page = "Monitoring";
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
// set monsource variable
function setmonsource(source) {
	document.crashmon.monsource.value = source;
}
function setsourcemon(yesno) {
	document.crashmon.sourcemon.value = yesno;
}
// submit form to do crash monitor
function crashswitchmon(mondest)
{
	document.crashmon.mon.value = mondest;
	document.crashmon.submit();
}

function reload_panel(sourcetab, desttab)
{
	location.href='monitor.php?servicetab='+desttab+'&sourcetab='+sourcetab;
}

//-->
</SCRIPT>
<?php
	sif_buttons($page);
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
	$dbh = connect();
?>
<form method="post" action="crashmon.php" name="crashmon">
<input type="hidden" name="monsource" value="OFF">
<input type="hidden" name="mon" value="NULL">
<input type="hidden" name="sourcemon" value="yes">
<div id="sourcebuttons">
<?php showsourcebuttons($dbh, "source", $sourcetab, $servicetab); ?>
</div>
<div id="destbuttons">
<?php showservicebuttons($dbh, "dest", $servicetab, $sourcetab); ?>
</div>
<div id="takebuttons">
<table width=100%>
<tr>
<?php
	print "<td align=center height=40 width=10% id=\"source{$sourcecount}\" class=\"depressed\" onclick=\"toggleButton(this, /source/i);setmonsource('{$row[source]}');\"><b>OFF</b></td><td colspan=2>&nbsp;</td>";
	print "<td height=40 width=10% height=60>&nbsp;</td>";
	$moncount=0;
	$stmt=$dbh->prepare("SELECT * FROM edge l LEFT JOIN edge s ON e.id=s.id WHERE l.kind='LISTENER' ORDER BY l.id asc");
	$stmt->execute();
	while($row= $stmt->fetch(PDO::FETCH_ASSOC))
	{

		if ($row[current_service]==$row[listener])
		{
			//monitoring its own autoservice, so show actual source to that service
			$currentservice="<font color=blue>".$row["current_source"]."</font>";
		}
		else
		{
			$currentservice="<font color=blue>".$row["current_service"]."</font>";
		}
		if ($currentservice == "<font color=blue>()</font>")
		{
			$currentservice="<font color=blue>(OFF)</font>";
		}


		print "\n<td height=40 width=10%  height=60 id=\"mon{$moncount}\" class=\"raised\" onclick=\"crashswitchmon('{$row[listener]}');\"><b>{$row[listener]}</b><br><i>{$currentservice}</i></td>";
		$moncount++;
		if ($moncount % 10 == 0)
		{
			print "</tr><tr>";
		}
	}
	$emptyslotsinrow=(7-($moncount % 10));
	// this will pad out any remaining slots so the table formats correctly
	if ($emptyslotsinrow < 7)
	{
		while($emptyslotsinrow > 0)
		{
			print "<td height=40 width=10% height=60>&nbsp;</td>";
			$emptyslotsinrow--;
		}
	}
?>
<th class="raised" width=10% onClick="history.go();">Refresh</th>
</tr></table>
</form>
</div>
<?php
	sif_footer();
?>
