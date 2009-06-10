<html>
<head>
<title>SIF Project - Service Maintenance</title>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">

<script LANGUAGE="JavaScript">
<!--
function confirmSubmit()
{
var agree=confirm("Are you sure you wish to delete this service?");
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
SIF Project - Manage Services</h3>
<?php
	require 'connect.php';

	$result=mysql_query("SELECT * FROM service order by service asc", $connection);
	print "<form method=\"post\" action=\"addservice.php\" name=\"addservice\">";
	print "<table border=1 cellspacing=0 cellpadding=4>";
	print "<tr><th>Service:</th><th>Enabled:</th><th colspan=2>Action:</th><tr>";
	print "<tr><td><input type=\"text\" name=\"service\" style=\"background-color:#ffdab9\" size=20 maxlength=10 onKeyPress=\"return submitenter(this,event)\"></td>";
	print "\n<td><input type=\"checkbox\" name=\"enabled\" value=\"1\"></td>";
	print "<td colspan=2><input type=\"Submit\" value=\"Add Service\"></td></tr>";
	print "</form>";
	while($row= mysql_fetch_array($result))
	{
		print "\n<tr><form method=\"post\" action=\"editservice.php\">";
		print "\n<input type=\"hidden\" name=\"service\" value=\"{$row["service"]}\">";
		print "\n<td>{$row["service"]}</td>";
		if(intval($row["enabled"])==1)
		{
			print "\n<td>Yes</td>";
		}
		else
		{
			print "\n<td>No</td>";
		}
		print "\n<td><input type=\"Submit\" value=\"Edit\"></td>";
		print "\n</form><form method=\"post\" action=\"deleteservice.php\">";
		print "\n<input type=\"hidden\" name=\"service\" value=\"{$row["service"]}\">";
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
