<?php
	require_once("header.php");
	$page = "Redundancy Switching";
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
// submit form to do crash monitor
function crashswitchmon(mondest)
{
	document.crashmon.mon.value = mondest;
	document.crashmon.submit();
}
//-->
</SCRIPT>
<?php
	sif_buttons($page);
	if (isset($_REQUEST["tab"]))
	{
		$tab=$_REQUEST["tab"];
	}
	else
	{
		$tab=1;
	}
	require 'connect.php';
?>

<table width=100% height=600 border-0><tr><tr><td valign=top>
<table width=100% border=0>
<tr>
<?php
	$sourcetabcount=0;
	$result=mysql_query("SELECT DISTINCT tab_text, r.tab_index, id FROM redundancy_tabs t left join redundancy r on t.tab_index=r.tab_index where enabled=1 and type='SOURCE' order by t.tab_index asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		print "\n<th width=20% ";
		if ($tab==$row[tab_index])
		{
			print "class=\"depressed\"";
		}
		else
		{
			print "class=\"raised\" ";
			print "onclick=\"location.href='sourcepairs.php?tab=".$row["tab_index"]."\"";
		}
		print ">".$row["tab_text"]."</th>";
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
	print "</tr>";
	// now do the actual redundancy pairs
	$result=mysql_query("SELECT * FROM redundancy where tab_index='$tab' order by id asc", $connection);
	$sources = array();
	$listeners = array();
	while($row = mysql_fetch_array($result))
	{
		$id = $row["id"];
		if ($row["type"]=="LISTENER")
		{
			if(!isset($listeners[$id]))
				$listeners[$id] = array();
			$listeners[$id][] = $row;
		}
		else
		{
			if(!isset($sources[$id]))
				$sources[$id] = array();
			$sources[$id][] = $row;
		}
	}
	// this shows listeners - we expect listeners to be always both active so we omit them
	/*
	foreach($listeners as $p)
	{
		print "\n<td width=20% class=\"unused\"><i><b>Listener Pair: $id</b></i><br>";
		print "\nMain: $main<br>";
		print "\nReserve: $reserve</td>";
	}
	*/
	print "\n<tr>";
	$i = 0;
	foreach($sources as $id => $devices)
	{
		print "\n<td width=20% class=\"unused\">";

		print "\n<form method=\"post\" action=\"setactive.php\" name=\"source$i\">";
		print "\n<input type=\"hidden\" name=\"id\" value=\"$id\">";
		print "\n<input type=\"hidden\" name=\"tab\" value=\"$tab\">";

		print "<table border=0 width=100%><tr>";
		print "<td><i><b><font color=\"blue\">Source: $id</font></b></i><br>";
		foreach($devices as $d)
		{
			$device = $d["device"];
			$idx = $d["idx"];
			$active = $d["active"];
			print "\n<input type=\"radio\" name=\"idx\" value=\"$idx\"";
			if ($active)
			{
				print " checked";
			}
			print ">$device<br/>";
		}
		print "</td>";
		print "<td align=right width=60 valign=center>&nbsp;&nbsp;<input type=submit value=\"Update\">";
		print "</td></tr>";
		print "</table>";
		print "</form>";

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
<table width=100%><tr>
<th width=90%>&nbsp;</th>
<th class="raised" height=60 width=10% onClick="history.go()">Refresh</th>
</tr></table>
<?php sif_footer(); ?>
