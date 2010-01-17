<?php
require_once("messaging.inc");
require_once "sif.inc";
$dbh = connect();
$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
if (isset($_REQUEST["service"]))
{
	if(isset($_REQUEST["listenertab"]) && isset($_REQUEST["servicetab"]))
	{
		header("location: listenercrashswitch.php?listenertab=".$_REQUEST["listenertab"]."&servicetab=".$_REQUEST["servicetab"]);
	}
	else
	{
		$verbose=1;
	}

	$service = $_REQUEST["service"];
	$listener = $_REQUEST["listener"];
	
	if(isset($_REQUEST["sed"]))
	{
		$times = gettimes($dbh);
		break_schedule($dbh, $listener, $service, $_REQUEST["sed"], $times);
	}

	if(isset($_REQUEST["emulate"]))
	{
		register_event_as_run($dbh, "ANY", $_REQUEST["previous_service"], $listener, "OFF");
		register_event_as_run($dbh, "ANY", $service, $listener, "ON");
	}
	else
	{
		$stmt = $dbh->query("SELECT value FROM configuration WHERE `key`='message_bus_host'",  PDO::FETCH_COLUMN, 0);	
		$config = $stmt->fetch();
		$sender = new Sender($config);
		$sender->send($listener, "oi=$service");
		$sender->close();
	}

	if(isset($verbose))
	{
		print $config["value"]."<br>$listener";
		print_r($sender);
	}
}
else
{
	header("location: listenercrashswitch.php");
	echo "Error - no listener defined";
}
?>