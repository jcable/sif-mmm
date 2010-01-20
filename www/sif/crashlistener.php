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
		header('Content-type: text/plain');
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
		$stmt = $dbh->prepare("SELECT access,dst FROM edge e JOIN edge_output o ON e.input = o.encoding WHERE e.id = ? AND o.edge = ?");
		$stmt->execute(array($listener,$service));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row["message"] = "oi";
		$row["service"] = $service;
		$msg = json_encode($row);

		$stmt = $dbh->query("SELECT value FROM configuration WHERE `key`='message_bus_host'",  PDO::FETCH_COLUMN, 0);	
		$config = $stmt->fetch();
		$sender = new Sender($config);
		$sender->send($listener, $msg);
		$sender->close();
	}

	if(isset($verbose))
	{
		print $config."\n$listener\n";
		print_r($sender);
	}
}
else
{
	header("location: listenercrashswitch.php");
	echo "Error - no listener defined";
}
?>