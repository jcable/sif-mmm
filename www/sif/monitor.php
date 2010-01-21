<?php
	require_once("sif.inc");
	require_once("header.php");
	$page = "Monitoring";
	sif_header($page, "crashswitch.css");
?>
<SCRIPT TYPE="text/javascript">
<!--

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
	if (isset($_REQUEST["sourcetab"]))
	{
		$sourcetab=$_REQUEST["sourcetab"];
	}
	else
	{
		$sourcetab=1;
	}
	if (isset($_REQUEST["servicetab"]))
	{
		$servicetab=$_REQUEST["servicetab"];
	}
	else
	{
		$servicetab=1;
	}
	$dbh = connect();
?>
<form method="post" action="crashmon.php" name="crashmon">
<input type="hidden" name="source" value="OFF">
<input type="hidden" name="service" value="OFF">
<input type="hidden" name="mon" value="NULL">
<div id="sourcebuttons"><?php showselectionpanel($dbh, "source", $sourcetab, $servicetab, "SOURCE"); ?></div>
<div id="destbuttons"><?php showservicebuttons($dbh, "dest", $servicetab, $sourcetab); ?></div>
<div id="takebuttons">
<table width=100%>
<tr>
<td id="sourceOFF" class="depressed button" onclick="toggleButton(this, /source/i);setsource('OFF');"><b>OFF</b></td><td colspan=2>&nbsp;</td>
<td height=40 width=10% height=60>&nbsp;</td>
<?php
	$moncount=0;
	$stmt=$dbh->prepare("SELECT * FROM edge JOIN panel_tabs USING(kind,tab_index) WHERE kind='LISTENER' and hidden=1 ORDER BY id asc");
	$stmt->execute();
	while($row= $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$id=$row["id"];
		$source="";
		print "<td id=\"mon{$moncount}\" class=\"raised button\" onclick=\"crashswitchmon('{$row[listener]}');\">";
		print "<span class=\"buttonlabel\">$id</span>";
		if($source!="")
		{
			print "<br>";
			print "<span class=\"buttondetail\">(".$source.")</span>";
		}
		print "</td>\n";
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
