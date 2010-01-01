<?php
	require_once("header.php");
	$page = "Maintenance";
	sif_header($page, "main.css");
	sif_buttons($page);
?>
<ul>
<li><a href="managephysicaldevices.php">Manage Physical Devices</a>
<li><a href="managesources.php">Manage Sources</a>
<li><a href="manageservices.php">Manage Services</a>
<li><a href="managelisteners.php">Manage Listeners</a>
<li><a href="manageredundancy.php">Manage Logical to Physical Device Mapping &amp; Redundancy</a>
<p>
<li><a href="managetabs.php?type=source">Manage Source Tabs</a>
<li><a href="managetabs.php?type=services">Manage Services Tabs</a>
<li><a href="managetabs.php?type=listener">Manage Listener Tabs</a>
<li><a href="managetabs.php?type=redundancy">Manage Redundancy Tabs</a>
<p>
<li><a href="taskmanager.php">Task Manager</a>
<p>
<li><a href="/phpmyadmin" target="_new">MySQL Administration</a>
<li><a href="phpinfo.php" target="_new">PHP Server Info</a>
</ul>
<?php
	sif_footer();
?>
