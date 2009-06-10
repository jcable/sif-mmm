<html>
<head>
<title>SIF Project - Redundancy Maintenance</title>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">

<script LANGUAGE="JavaScript">
<!--
function confirmSubmit()
{
var agree=confirm("Are you sure you wish to delete this redundant pair?");
if (agree)
	return true ;
else
	return false ;
}
// -->
</script>


<body>
<table border=0 width=100%><tr><th width=80px><img src="wslogo.jpg" alt=""></th>
<th class="menubutton" onclick="location.href='maintenance.htm';">Maintenance</th>
<th class="menubutton" onclick="location.href='showserviceschedule.php';">Service Schedules</th>
<th class="menubutton" onclick="location.href='showlistenerschedule.php';">Listener Schedules</th>
<th class="menubutton" onclick="location.href='showmaterial.php';">Material Info</th>
<th class="menubutton" onclick="location.href='servicecrashswitch.php?sourcetab=1&servicetab=1';">Crash Services</th>
<th class="menubutton"onclick="location.href='listenercrashswitch.php?servicetab=1&listenertab=1';">Crash Listeners</th>
<th class="menubutton" onclick="location.href='monitor.php';">Monitoring</th>
<th class="menubutton" onclick="location.href='sourcepairs.php';">Redundant Sources</th>
</tr><tr>
<td></table>
<h3>
SIF Project - Manage Redundancy Pairs</h3>
<?php
	require 'connect.php';

	$result=mysql_query("SELECT * FROM redundancy order by redundancy_type,redundancy_text asc", $connection);
	print "\n<form method=\"post\" action=\"addredundancy.php\" name=\"addredundancy\">";
	print "\n<table border=1 cellspacing=0 cellpadding=4>";
	print "\n<tr><th>Redundancy Pair:</th><th>Type:</th><th>Main:</th><th>Reserve:</th><th colspan=2>Action:</th><tr>";
	print "\n<tr><td><input type=\"text\" name=\"text\" style=\"background-color:#ffdab9\" size=20 maxlength=10 onKeyPress=\"return submitenter(this,event)\"></td>";
	print "\n<td><select name=\"type\">";
	print "\n<option value=\"SOURCE\">SOURCE</option>";
	print "\n<option value=\"LISTENER\" selected>LISTENER</option>";
	print "\n</select></td><td>&nbsp;</td><td>&nbsp;</td>";
	print "<td colspan=2><input type=\"Submit\" value=\"Add Pair\"></td></tr>";
	print "</form>";
	while($row= mysql_fetch_array($result))
	{
		print "\n<tr><form method=\"post\" action=\"editredundancy.php\">";
		print "\n<input type=\"hidden\" name=\"text\" value=\"{$row["redundancy_text"]}\">";
		print "\n<td>{$row["redundancy_text"]}&nbsp;</td>";
		print "\n<td>{$row["redundancy_type"]}&nbsp;</td>";
		print "\n<td>{$row["main"]}&nbsp;</td>";
		print "\n<td>{$row["reserve"]}&nbsp;</td>";
		print "\n<td><input type=\"Submit\" value=\"Edit\"></td>";
		print "\n</form><form method=\"post\" action=\"deleteredundancy.php\">";
		print "\n<input type=\"hidden\" name=\"text\" value=\"{$row["redundancy_text"]}\">";
		print "\n<td><input type=\"Submit\" value=\"Delete\" onClick=\"return confirmSubmit()\"></td>";
		print "\n</form></tr>";
	}
?>
</table>
<hr>
<div id="footer">
&copy; 2009, Mark Patrick, BBC WS
</div>
</html>
