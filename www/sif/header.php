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
	sif_button($titleword, "servicecrashswitch.php", "Crash Services");
	sif_button($titleword, "listenercrashswitch.php", "Crash Listeners");
	sif_button($titleword, "monitor.php", "Monitoring");
	sif_button($titleword, "sourcepairs.php", "Device Mapping &amp; Redundancy");
	print "</tr>";
	print "</table>\n";
	print "<h3>SIF Project - $titleword</h3>\n";
}

function sif_header($titleword, $stylesheet)
{
?>
<html>
<head>
<title>SIF Project <?php print $titleword; ?></title>
</head>
<link rel="stylesheet" type="text/css" href="<?php print $stylesheet; ?>" media="screen,print">
<link rel="stylesheet" type="text/css" href="table.css" media="all">
<script type="text/javascript" src="crashswitch.js"></script>
<script type="text/javascript" src="table.js"></script>
<script type="text/javascript" src="findonpage.js"></script>
<script LANGUAGE="JavaScript">
<!--
function confirmSubmit()
{
var agree=confirm("Are you sure you wish to delete this entry?");
if (agree)
	return true ;
else
	return false ;
}
// -->
</script>
<?php
}

function sif_footer()
{
	print "<hr>\n";
	print "<div id='footer'>\n";
	print "&copy; 2009, British Broadcasting Corporation\n";
	print "</div>\n";
	print "</html>\n";
}
?>
