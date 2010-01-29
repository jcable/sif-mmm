<?php
require_once("messaging.inc");
require_once "sif.inc";
$dbh = connect();
$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

$source=$_REQUEST["source"];
$service=$_REQUEST["service"];
$mon=$_REQUEST["mon"];

if(isset($_REQUEST["emulate"]))
{
	if ($source=="OFF")
	{
		register_event_as_run($dbh, "ANY", "$mon'", $mon, "OFF");
		register_event_as_run($dbh, "ANY", "ANY", $mon, "OFF");
	}
	elseif($source=="")
	{
		register_event_as_run($dbh, "ANY", $service, $mon, "ON");
	}
	else
	{
		register_event_as_run($dbh, "ANY", $source, $mon, "ON");
	}
}
else
{
	// for every monitor listener there is a service with the same name suffixed by a single quote
	$stmt = $dbh->query("SELECT value FROM configuration WHERE `key`='message_bus_host'",  PDO::FETCH_COLUMN, 0);	
	$config = $stmt->fetch();
	$sender = new Sender($config);
	if ($source=="OFF")
	{
		// send to service
		$sender->send("$mon'", json_encode(array("message"=>"oi", "action"=>"OFF")));
		// send to listener
		$sender->send("$mon", json_encode(array("access"=>"", "dst"=>"", "message"=>"oi", "service"=>"OFF")));
	}
	else
	{
		if($service=="")
		{
			$service = "$mon'";
			$sender->send($source, json_encode(array("message"=>"oi", "service"=>$service)));
		}
		$stmt = $dbh->prepare("SELECT access, dst FROM edge_output WHERE edge=?");
		$stmt->execute(array($service));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row["message"] = "oi";
		$row["service"] = $service;

		$sender->send($mon, json_encode($row));
	}
	$sender->close();
}
?>
