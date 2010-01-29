<?php
require_once("messaging.inc");
require_once "sif.inc";
$dbh = connect();
$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
if (isset($_REQUEST["service"]) && isset($_REQUEST["source"]))
{
	$service = $_REQUEST["service"];
	$new_source = $_REQUEST["source"];

	if(isset($_REQUEST["emulate"]))
	{
		register_event_as_run($dbh, "ANY", $new_source, $service, "ON");
	}
	else
	{
		$stmt = $dbh->query("SELECT value FROM configuration WHERE `key`='message_bus_host'",  PDO::FETCH_COLUMN, 0);	
		$config = $stmt->fetch();
		$t1 = microtime(true);
		$sender = new Sender($config);
		$t2 = microtime(true);
		$sender->send($new_source, json_encode(array("message"=>"oi", "service"=>$service)));
		$sender->send($service, json_encode(array("message"=>"oi", "service"=>"OFF")));
		$sender->close();
	}
}
?>
