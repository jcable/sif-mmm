<?php
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
				$r["start_datetime"] = strftime("%Y-%m-%dT", $start_datetime).$rs["start_time"]."Z";
				//$r["start_datetime"] = strftime("%Y-%m-%dT%H:%M:%SZ", $start_datetime);
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

$dbh = new PDO(
    'mysql:host=localhost;dbname=sif', 'sif', 'sif',
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
); 
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
//print $sql;
if(isset($_REQUEST["service"]))
{
	$service = $_REQUEST["service"];
}
else
{
	$service = "%";
}
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
//$root->appendChild(new DOMElement('query', $sql));
if(false)
{
	foreach ($rows as $rs)
	{
		$element = $root->appendChild(new DOMElement('service'));
		foreach ($rs as $key => $value)
		{
			$e = new DOMElement($key, $value);
			$element->appendChild($e);
		}
	}
}
$events = buildnow($yesterday, $ydow, $rows, array(), $now);
$events = buildnow($today, $dow, $rows, $events, $now);

foreach ($events as $rs)
{
	$element = $root->appendChild(new DOMElement('event'));
	foreach ($rs as $key => $value)
	{
		if($value!="")
		{
			$e = new DOMElement($key, $value);
			$element->appendChild($e);
		}
	}
}
header('Content-type: text/xml');
echo $dom->saveXML();
?>
