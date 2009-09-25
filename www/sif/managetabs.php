<html>
<head>
<title>SIF Project - Tab Maintenance</title>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">


<script LANGUAGE="JavaScript">
<!--
function confirmSubmit()
{
var agree=confirm("Are you sure you wish to delete this tab?");
if (agree)
	return true ;
else
	return false ;
}
// -->
</script>
<SCRIPT TYPE="text/javascript">
<!--
function submitenter(myfield,e)
{
var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return true;

if (keycode == 13)
   {
   myfield.form.submit();
   return false;
   }
else
   return true;
}
//-->
</SCRIPT>

<body>
<table border=0 width=100%><tr><th width=80px><img src="wslogo.jpg" alt=""></th>
<th class="menubutton" onclick="location.href='maintenance.php';">Maintenance</th>
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
<?php
	if (!empty($_REQUEST["type"]))
	{
	$type=$_REQUEST["type"];
	require 'connect.php';
	switch ($type)	{
		case "source":
			$result=mysql_query("SELECT * FROM source_tabs order by tab_index asc", $connection);
			print "SIF Project - Manage Source Tabs</h3>";
			break;
		case "services":
			$result=mysql_query("SELECT * FROM services_tabs order by tab_index asc", $connection);
			print "SIF Project - Manage Services Tabs</h3>";
			break;
		case "listener":
			$result=mysql_query("SELECT * FROM listener_tabs order by tab_index asc", $connection);
			print "SIF Project - Manage Listener Tabs</h3>";
			break;
		case "redundancy":
			$result=mysql_query("SELECT * FROM redundancy_tabs order by tab_index asc", $connection);
			print "SIF Project - Manage Redundancy Tabs</h3>";
			break;
		default:
			// default to source tabs if something stupid requested
			$result=mysql_query("SELECT * FROM source_tabs order by tab_index asc", $connection);
			print "SIF Project - IP Media Router<br>Manage Source Tabs</h3>";
	}
	print "<form method=\"post\" action=\"addtab.php\" name=\"addtab\">";
	print "\n<input type=\"hidden\" name=\"type\" value=\"{$type}\">";
	print "<table border=1 cellspacing=0 cellpadding=4>";
	print "<tr><th>Index:</th><th>Tab Text:</th><th>Enabled:</th><th colspan=2>Action:</th><tr>";
	print "<tr><td>&nbsp;</td><td><input type=\"text\" name=\"tabtext\" style=\"background-color:#ffdab9\" size=40 maxlength=20 onKeyPress=\"return submitenter(this,event)\"></td>";
	print "\n<td><input type=\"checkbox\" name=\"enabled\" checked=\"checked\" value=\"1\"></td>";
	print "<td colspan=2><input type=\"Submit\" value=\"Add Tab\"></td></tr>";
	print "</form>";
	while($row= mysql_fetch_array($result))
	{
		print "\n<tr><form method=\"post\" action=\"updatetab.php\">";
		print "\n<input type=\"hidden\" name=\"type\" value=\"{$type}\">";
		print "\n<input type=\"hidden\" name=\"id\" value=\"{$row["tab_index"]}\">";
		print "\n<td>{$row["tab_index"]}</td>";
		print "\n<td><input type=\"text\" name=\"tabtext\" size=40 maxlength=20 value=\"{$row["tab_text"]}\" onKeyPress=\"return submitenter(this,event)\"></td>";
		if(intval($row["enabled"])==1)
		{
			print "\n<td><input type=\"checkbox\" name=\"enabled\" checked=\"checked\" value=\"1\"></td>";
		}
		else
		{
			print "\n<td><input type=\"checkbox\" name=\"enabled\" value=\"1\"></td>";
		}
		print "\n<td><input type=\"Submit\" value=\"Update\"></td>";
		print "\n</form><form method=\"post\" action=\"deletetab.php\">";
		print "\n<input type=\"hidden\" name=\"type\" value=\"{$type}\">";
		print "\n<input type=\"hidden\" name=\"id\" value=\"{$row["tab_index"]}\">";
		print "\n<td><input type=\"Submit\" value=\"Delete\" onClick=\"return confirmSubmit()\"></td>";
		print "\n</form></tr>";
	}
}
?>
</table>
<hr>

<div id="footer">
&copy; 2009, Mark Patrick, BBC WS
</div>
</html>
