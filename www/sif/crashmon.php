<?php
require_once("messaging.inc");
require_once "sif.inc";
$dbh = connect();
$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
if(isset($_REQUEST["sourcetab"]) && isset($_REQUEST["servicetab"]))
{
	header("location: monitor.php?servicetab=".$_REQUEST["servicetab"]."&sourcetab=".$_REQUEST["sourcetab"]);
}
else
{
	$verbose=1;
}

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
		$sender->send($mon, json_encode(array("message"=>"oi", "action"=>"OFF")));
	}
	elseif($source=="")
	{
		$sender->send($mon, json_encode(array("message"=>"oi", "service"=>$service, "action"=>"ON")));
	}
	else
	{
		$sender->send($source, json_encode(array("message"=>"oi", "action"=>"ON", "service"=>"$mon'")));
		$sender->send($mon, json_encode(array("message"=>"oi", "service"=>$mon, "action"=>"ON")));
	}
	$sender->close();
}

if(isset($verbose))
{
	print $config["value"]."<br/>\n";
	print_r($sender);
	phpinfo();
}
?>
