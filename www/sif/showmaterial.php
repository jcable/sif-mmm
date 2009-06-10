<html>
<head>
<title>SIF Project - Material</title>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">
<link rel="stylesheet" type="text/css" href="table.css" media="all">
<script LANGUAGE="JavaScript">
<!--
function confirmSubmit()
{
var agree=confirm("Are you sure you wish to delete this material?");
if (agree)
	return true ;
else
	return false ;
}
// -->
</script>
<body>
<table border=0 width=100%><tr><th width=80px><img src="wslogo.jpg" alt=""></th>
<th class="menubutton" onclick="location.href='index.html';">Main Menu</th>
<th class="menubutton" onclick="location.href='showserviceschedule.php';">Service Schedules</th>
<th class="menubutton" onclick="location.href='showlistenerschedule.php';">Listener Schedules</th>
<th class="mymenubutton">Material Info</th>
<th class="menubutton" onclick="location.href='servicecrashswitch.php?sourcetab=1&servicetab=1';">Crash Services</th>
<th class="menubutton"onclick="location.href='listenercrashswitch.php?servicetab=1&listenertab=1';">Crash Listeners</th>
<th class="menubutton" onclick="location.href='monitor.php';">Monitoring</th>
<th class="menubutton" onclick="location.href='sourcepairs.php';">Redundant Sources</th>
</tr><tr>
<td></table>
<h3>
SIF Project - Show Material</h3>
<script type="text/javascript" src="table.js"></script>
<script type="text/javascript" src="findonpage.js"></script>
<p><form name="form1" onSubmit="search(document.form1, frametosearch); return false"><input type="text" name="findthis" size="15" title="Press 'ALT s' after clicking find button to repeatedly search page"> <input type="submit" value="Find in Page" ACCESSKEY="s"></form>
&nbsp;
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
document.write('<form><input type=button value="Refresh" onClick="history.go()"></form>')
//  End -->
</script>
<p>
<div id="plaincontent">
<table class="example table-autosort:0 table-stripeclass:alternate" border=1 cellspacing=0 cellpadding=2>
<thead>
<tr>
<th class="table-sortable:default">Material ID:</th>
<th class="table-sortable:default">Title:</th>
<th class="table-sortable:default">Duration:</th>
<th class="table-sortable:default">Delete After:</th>
<th class="table-sortable:default">File:</th>
<th class="table-sortable:default">Type:</th>
<th class="table-sortable:default">Owner:</th>
<th class="table-sortable:default">Client Ref:</th>
<th class="table-sortable:default">TX Date:</th>
<th>&nbsp;</th>
</tr>
<tr>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th>&nbsp;</th>
</tr>
</thead>
<?php
require 'connect.php';
$result=mysql_query("select * from material order by material_id asc", $connection);
$count=0;
while($row= mysql_fetch_array($result))
{
	$count=$count+1;
	print "\n<tr>";
	print "\n<td>{$row["material_id"]}&nbsp;</td>";
	print "\n<td>{$row["title"]}&nbsp;</td>";
	print "\n<td>{$row["duration"]}&nbsp;</td>";
	print "\n<td>{$row["delete_after"]}&nbsp;</td>";
	print "\n<td>{$row["file"]}&nbsp;</td>";
	print "\n<td>{$row["material_type"]}&nbsp;</td>";
	print "\n<td>{$row["owner"]}&nbsp;</td>";
	print "\n<td>{$row["client_ref"]}&nbsp;</td>";
	print "\n<td>{$row["tx_date"]}&nbsp;</td>";

	print "\n<form method=\"post\" action=\"deletematerial.php\">";
	print "\n<input type=\"hidden\" name=\"matid\" value=\"{$row["material_id"]}\">";
	print "\n<td><input type=\"Submit\" value=\"Delete\" onClick=\"return confirmSubmit()\"></td>";
	print "\n</form>";
	print "\n</tr>";
}
print "\n</table></div><p>";
print "{$count} material entries in database";
?>
<hr>
<div id="footer">
&copy; 2009, Mark Patrick, BBC WS
</div>
</html>
