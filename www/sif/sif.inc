<?php

function connect()
{
	return new PDO( 'mysql:host=localhost;dbname=sif', 'sif', 'sif',
    		array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
	); 
}

function buildnow($start, $dow, $sched, $e, $ref)
{
	$events = $e;
	foreach ($sched as $rs)
	{
		if($rs["days"][$dow]!="_")
		{
			$start_datetime = $start + intval($rs["seconds"]);
			if($start_datetime<$ref)
			{
				$r = $rs;
				$r["s"] = strftime("%S", $start_datetime);
				//$r["start_datetime"] = strftime("%Y-%m-%dT", $start_datetime).$rs["start_time"]."Z";
				$r["start_datetime"] = strftime("%Y-%m-%dT%H:%M:%SZ", $start_datetime);
				$r["start"] = $start_datetime;
				if(isset($events[$rs["service"]]))
				{
					$o = $events[$rs["service"]];
					if($o["start"]<$start_datetime)
						$events[$rs["service"]] = $r;
				}
				else
				{
					$events[$rs["service"]] = $r;
				}
			}
		}
	}
	return $events;
}

function active_schedule_records($dbh, $service)
{
	$dayflags = array( "S______", "_M______", "__T____", "___W___", "____T__", "_____F_", "______S");
	$dow = intval(strftime("%w"));
	$todaymask = $dayflags[$dow];
	$ydow = dow-1;
	if($ydow==-1)
		$ydow = 6;
	$yesterdaymask = $dayflags[$ydow];
	$daysmask=$todaymask;
	for($i=0; $i<7; $i++)
	{
		if($yesterdaymask[$i]!="_")
			$daysmask[$i]=$yesterdaymask[$i];
	}
	$sql = "SELECT service_event_id, service, source,";
	$sql .= " days, start_time, duration, time_to_sec(start_time) AS seconds,";
	//$sql .= " ADDDATE(CURDATE(), INTERVAL TIME_TO_SEC(start_time) SECOND) AS start_datetime,";
	$sql .= " start_mode, name, material_id, rot, ptt, ptt_time, owner";
	$sql .= " FROM service_active_schedule";
	$sql .= " WHERE first_date <= DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
	$sql .= " AND (last_date IS NULL OR last_date >= CURDATE())";
	$sql .= " AND (days IS NULL OR days LIKE ?)";
	$sql .= " AND service LIKE ?";
	$sql .= " ORDER BY service, start_time DESC";
	$stmt = $dbh->prepare($sql);
	$stmt->bindParam(1, $daysmask);
	$stmt->bindParam(2, $service);
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$stmt = $dbh->prepare("SELECT unix_timestamp(date(now())) AS today, unix_timestamp(now()) AS now");
	$stmt->execute();
	$d = $stmt->fetch(PDO::FETCH_ASSOC);
	$today = intval($d["today"]);
	$now = intval($d["now"]);
	$yesterday = $today - 86400;
	$dom = new DOMDocument('1.0', 'utf-8');
	$root = $dom->appendChild(new DOMElement('sif'));
	$events = buildnow($yesterday, $ydow, $rows, array(), $now);
	$events = buildnow($today, $dow, $rows, $events, $now);

	return $events;
}

function gettimes($dbh)
{
	$sql = "SELECT"
		." TIME_TO_SEC(TIME(NOW())) AS seconds,"
		." DATE(NOW()) AS today,"
		." TIME(NOW()) AS start,"
		." DATE_SUB(DATE(NOW()), INTERVAL 1 DAY) AS yesterday,"
		." DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AS tomorrow,"
		." DATE_SUB(DATE(NOW()), INTERVAL 1 DAY) AS yesterday";
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

function break_schedule($dbh, $service, $new_source, $sed, $times)
{
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
}

function register_event_as_run($dbh, $device, $input, $output, $eventtype)
{
	$stmt = $dbh->prepare("INSERT INTO as_run (device, input, output, event_type) VALUES(?,?,?,?)");	
	$r = $stmt->execute(array($device, $input, $output, $eventtype));
	if($r)
		return true;
	else
		return $stmt->errorInfo();
}

function active_events_as_run($dbh)
{
	$sql = <<<EOT
SELECT output,if(event_type='OFF','OFF',ifnull(input,'OFF')) as input 
FROM (select id AS output FROM edge WHERE kind='SERVICE') AS a
LEFT JOIN
(
SELECT output,input,event_type
FROM
(SELECT max( event_time ) AS event_time, output FROM as_run GROUP BY output) AS m
JOIN as_run AS r
USING ( event_time, output )
) AS b USING (output)
EOT;
	$stmt = $dbh->query($sql);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
