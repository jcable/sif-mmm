<?php
require_once("messaging.inc");
require_once "sif.inc";
$dbh = connect();
$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
if (isset($_REQUEST["service"]))
{
	if(isset($_REQUEST["sourcetab"]) && isset($_REQUEST["servicetab"]))
	{
		header("location: servicecrashswitch.php?sourcetab=".$_REQUEST["sourcetab"]."&servicetab=".$_REQUEST["servicetab"]);
	}
	else
	{
		$verbose=1;
	}

/*
take the current event and divide it into 4:
	1) update the current event, changing its lastdate to yesterday
	2) insert a new event like the current event, starting tomorrow
	3) insert a new event like the current event, for today only, ending now
	4) insert a new event for the crashed to source, starting now, for today only ending when the current event ends
*/
	$service = $_REQUEST["service"];
	$new_source = $_REQUEST["source"];
	
	if(isset($_REQUEST["sed"]))
	{
		$times = gettimes($dbh);
		break_schedule($dbh, $service, $new_source, $_REQUEST["sed"], $times);
	}

	if(isset($_REQUEST["emulate"]))
	{
		register_event_as_run($dbh, "ANY", $_REQUEST["previous_source"], $service, "OFF");
		register_event_as_run($dbh, "ANY", $new_source, $service, "ON");
	}
	else
	{
		$stmt = $dbh->query("SELECT value FROM configuration WHERE `key`='message_bus_host'",  PDO::FETCH_COLUMN, 0);	
		$config = $stmt->fetch();
		$sender = new Sender($config);
		$sender->send($service, json_encode(array("message"=>"oi", "service"=>"OFF")));
		$sender->send($new_source, json_encode(array("message"=>"oi", "service"=>$service)));
		$sender->close();
	}

	if(isset($verbose))
	{
		print $config["value"]."<br/>\n";
		print_r($sender);
		phpinfo();
	}
}
else
{
	header("location: servicecrashswitch.php");
	echo "Error - no service defined";
}
?>
