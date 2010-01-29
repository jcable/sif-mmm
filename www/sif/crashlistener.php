<?php
require_once("messaging.inc");
require_once "sif.inc";
$dbh = connect();
$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
if (isset($_REQUEST["service"])) && isset($_REQUEST["listener"]))
{
	$service = $_REQUEST["service"];
	$listener = $_REQUEST["listener"];

	if(isset($_REQUEST["emulate"]))
	{
		register_event_as_run($dbh, "ANY", $service, $listener, "ON");
	}
	else
	{
		$stmt = $dbh->prepare("SELECT access, dst FROM edge_output WHERE edge=?");
		$stmt->execute(array($service));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row["message"] = "oi";
		$row["service"] = $service;

		$stmt = $dbh->query("SELECT value FROM configuration WHERE `key`='message_bus_host'",  PDO::FETCH_COLUMN, 0);	
		$config = $stmt->fetch();
		$sender = new Sender($config);
		$sender->send($listener, json_encode($row));
		$sender->close();
	}
}
?>
