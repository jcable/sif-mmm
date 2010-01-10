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
	if($sed=="")
	{
		$stmt = $dbh->prepare("INSERT INTO service_active_schedule"
				." (service,source,first_date,last_date,days,start_time,duration)"
				." VALUES(?,?,?,?,?,?,SEC_TO_TIME(?))"
			);
		$stmt->bindValue(1, $service);
		$stmt->bindValue(2, $new_source);
		$stmt->bindValue(3, $times["today"]);
		$stmt->bindValue(4, $times["today"]);
		$stmt->bindValue(5, "SMTWTFS");
		$stmt->bindValue(6, $times["start"]);
		$stmt->bindValue(7, 86400 - $times["seconds"]);
		$stmt->execute();
	}
	else
	{
		$stmt = $dbh->prepare("SELECT s.*,"
				." TIME_TO_SEC(duration) AS duration_seconds,"
				." TIME_TO_SEC(start_time) AS start_seconds"
				." FROM service_active_schedule AS s WHERE service_event_id=?"
				);
		$stmt->bindParam(1, $sed);
		$stmt->execute();
		$prev = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($prev);

		try {
			$source = $prev["source"];
			$first_date = $prev["first_date"];
			$last_date = $prev["last_date"];
			$days = $prev["days"];
			$start_time = $prev["start_time"];
			$duration_seconds = $prev["duration_seconds"];

			$dbh->beginTransaction();

			$stmt = $dbh->prepare("UPDATE service_active_schedule"
				." SET first_date=?,"
				." last_date=?,"
				." duration=SEC_TO_TIME(?)"
				." WHERE service_event_id=?"
				);	
			$stmt->bindValue(1, $times["today"]);
			$stmt->bindValue(2, $times["today"]);
			$stmt->bindValue(3, $times["seconds"]-$prev["start_seconds"]);
			$stmt->bindValue(4, $sed);
			$stmt->execute();
			unset($stmt);
			
			$stmt = $dbh->prepare("INSERT INTO service_active_schedule"
					." (service,source,first_date,last_date,days,start_time,duration,"
					."start_mode,name,material_id,rot,ptt,ptt_time,owner)"
					." VALUES(?,?,?,?,?,?,SEC_TO_TIME(?),"
					."?,?,?,?,?,?,?)"
				);	
			$stmt->bindValue(1, $service);
			$stmt->bindParam(2, $source);
			$stmt->bindParam(3, $first_date);
			$stmt->bindParam(4, $last_date);
			$stmt->bindParam(5, $days);
			$stmt->bindParam(6, $start_time);
			$stmt->bindParam(7, $duration_seconds);
			$stmt->bindValue(8, $prev["start_mode"]);
			$stmt->bindValue(9, $prev["name"]);
			$stmt->bindValue(10, $prev["material_id"]);
			$stmt->bindValue(11, $prev["rot"]);
			$stmt->bindValue(12, $prev["ptt"]);
			$stmt->bindValue(13, $prev["ptt_time"]);
			$stmt->bindValue(14, $prev["owner"]);

			if($prev["first_date"] != $times["today"])
			{
				$last_date = $times["yesterday"];
				$stmt->execute();
			}

			if($prev["last_date"] != $times["today"])
			{
				$first_date = $times["tomorrow"];
				$last_date = $prev["last_date"];
				$stmt->execute();
			}

			$source = $new_source;
			$first_date = $times["today"];
			$last_date = $times["today"];
			$start_time = $times["start"];
			$duration_seconds = $prev["start_seconds"]+$prev["duration_seconds"]-$times["seconds"];
			$stmt->execute();

			$dbh->commit();

		} catch (Exception $e) {

			$dbh->rollBack();
			echo "Failed: " . $e->getMessage();

		}
	}
	
	$stmt = $dbh->prepare("SELECT value FROM configuration WHERE `key`='message_bus_host'");	
	$stmt->execute();
	$config = $stmt->fetch(PDO::FETCH_ASSOC);
	$sender = new Sender($config["value"]);
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
