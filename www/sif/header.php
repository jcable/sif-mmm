<?php

function sif_button($page, $url, $titleword)
{
	print "<th class='";
	if($page == $titleword)
		print "mymenubutton";
	else
		print "menubutton";
	print "'";
	if($page != $titleword)
		print "onclick=\"location.href='$url';\"";
	print ">";
	print $titleword;
	print "</th>\n";
}

function sif_buttons($titleword)
{
	print "<body>\n";
	print "<table border=0 width=100%>";
	print "<tr>";
	print "<th width=80px><img src='wslogo.jpg' alt=''></th>\n";
	if($titleword=="Maintenance")
		sif_button($titleword, "index.php", "Main Menu");
	else
		sif_button($titleword, "maintenance.php", "Maintenance");
	sif_button($titleword, "showserviceschedule.php", "Service Schedules");
	sif_button($titleword, "showlistenerschedule.php", "Listener Schedules");
	sif_button($titleword, "showmaterial.php", "Material Info");
	sif_button($titleword, "servicecrashswitch.php?sourcetab=1&servicetab=1", "Crash Services");
	sif_button($titleword, "listenercrashswitch.php?listenertab=1&servicetab=1", "Crash Listeners");
	sif_button($titleword, "monitor.php", "Monitoring");
	sif_button($titleword, "sourcepairs.php", "Redundant Sources");
	print "</tr>";
	print "</table>\n";
	print "<h3>SIF Project - $titleword</h3>\n";
}

function sif_header($titleword)
{
?>
<html>
<head>
<title>SIF Project <?php print $titleword; ?></title>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">
<link rel="stylesheet" type="text/css" href="table.css" media="all">
<link rel="stylesheet" type="text/css" href="crashswitch.css" media="screen,print">
<script type="text/javascript" src="crashswitch.js"></script>
<script type="text/javascript" src="table.js"></script>
<script type="text/javascript" src="findonpage.js"></script>
<script LANGUAGE="JavaScript">
<!--
function confirmSubmit()
{
var agree=confirm("Are you sure you wish to delete this line of schedule?");
if (agree)
	return true ;
else
	return false ;
}
// -->
</script>
<?php
	sif_buttons($titleword);
}
?>
