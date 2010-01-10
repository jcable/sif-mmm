<?php
require_once("messaging.inc");
require_once "sif.inc";
$dbh = connect();
$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
if (isset($_REQUEST["service"]))
{
	if(isset($_REQUEST["sourcetab"]) && isset($_REQUEST["servicetab"]))
	{
		//header("location: servicecrashswitch.php?sourcetab=".$_REQUEST["sourcetab"]."&servicetab=".$_REQUEST["servicetab"]);
	}
	else
	{
		//header('Content-type: text/plain');
	}

/*
take the current event and divide it into 4:
	1) update the current event, changing its lastdate to yesterday
	2) insert a new event like the current event, starting tomorrow
	3) insert a new event like the current event, for today only, ending now
	4) insert a new event for the crashed to source, starting now, for today only ending when the current event ends
*/
	$service = $_REQUEST["service"];
	$previous_source = $_REQUEST["previous_source"];
	$new_source = $_REQUEST["source"];
	$sed = $_REQUEST["sed"];
	
	$sql = "SELECT"
		." TIME_TO_SEC(TIME(NOW())) AS seconds,"
		." DATE(NOW()) AS today,"
		." TIME(NOW()) AS start,"
		." DATE_SUB(DATE(NOW()), INTERVAL 1 DAY) AS yesterday,"
		." DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AS tomorrow,"
		." DATE_SUB(DATE(NOW()), INTERVAL 1 DAY) AS yesterday";
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	$times = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($times);
	if($sed=="")
	{
		$duration = 86400 - $times["seconds"];
		$days = "SMTWTFS";
		$stmt = $dbh->prepare("INSERT INTO service_active_schedule"
								." (service,source,first_date,last_date,days,start_time,duration)"
								." VALUES(?,?,?,?,?,?,SEC_TO_TIME(?))"
			);
		$stmt->bindParam(1, $service);
		$stmt->bindParam(2, $new_source);
		$stmt->bindParam(3, $times["today"]);
		$stmt->bindParam(4, $times["today"]);
		$stmt->bindParam(5, $days);
		$stmt->bindParam(6, $times["start"]);
		$stmt->bindParam(7, $duration);
		$stmt->execute();
	}
	else
	{
		$stmt = $dbh->prepare("SELECT s.*,"
								." TIME_TO_SEC(duration) AS duration_seconds"
								." TIME_TO_SEC(start_time) AS start_seconds"
								." FROM service_active_schedule AS s WHERE service_event_id=?"
								);
		$stmt->bindParam(1, $sed);
		$stmt->execute();
		$prev = $stmt->fetch(PDO::FETCH_ASSOC);

		$dbh->beginTransaction();
		$stmt = $dbh->prepare("UPDATE service_active_schedule SET last_date=? WHERE service_event_id=?");	
		$stmt->bindParam(1, $times["yesterday"]);
		$stmt->bindParam(2, $sed);
		$stmt->execute();
		
		$r = $prev;
		$stmt = $dbh->prepare("INSERT INTO service_active_schedule"
								." (service,source,first_date,last_date,days,start_time,duration)"
								." VALUES(?,?,?,?,?,?,SEC_TO_TIME(?))"
			);	
		$stmt->bindParam(1, $r->service);
		$stmt->bindParam(2, $r->source);
		$stmt->bindParam(3, $r->first_date);
		$stmt->bindParam(4, $r->last_date);
		$stmt->bindParam(5, $r->days);
		$stmt->bindParam(6, $r->start_time);
		$stmt->bindParam(7, $r->duration);
print_r($r);
		$r->first_date = $times["tomorrow"];
		$r->duration = $prev["duration_seconds"];
print_r($r);
		$stmt->execute();

		$r->first_date = $times["today"];
		$r->last_date = $times["today"];
		$r->duration = $times["seconds"]-$prev["start_seconds"];
		$stmt->execute();

		$r->source = $service;
		$r->start_time = $times["start"];
		$r->duration = $prev["start_seconds"]+$prev["duration_seconds"]-$times["seconds"];
		$stmt->execute();

		$dbh->commit();
	}
	
	$stmt = $dbh->prepare("SELECT value FROM configuration WHERE `key`='message_bus_host'");	
	$stmt->execute();
	$config = $stmt->fetch(PDO::FETCH_ASSOC);
	$sender = new Sender($config["message_bus_host"]);
	$sender->send($service, "refresh");
	$sender->send($previous_source, "refresh");
	$sender->send($source, "refresh");
	$sender->close();

}
else
{
	header("location: servicecrashswitch.php");
	echo "Error - no service defined";
}
?>