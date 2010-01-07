<?php
if(isset($_REQUEST["source"]))
	$source = $_REQUEST["source"];
else
	$source = "Player 1";
$dbh = new PDO(
    'mysql:host=localhost;dbname=sif', 'sif', 'sif',
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
); 
print "<x>";
	$sql = "SELECT service_event_id, service, source, ";
	$sql .= "DATE_FORMAT(first_date, '%Y-%m-%d') AS first_date, ";
	$sql .= "DATE_FORMAT(last_date, '%Y-%m-%d') AS last_date, ";
	$sql .= "days, TIME_FORMAT(start_time, '%H:%i:%s') AS start_time, duration, ";
	$sql .= "start_mode, name, material_id, rot, ptt, ptt_time, owner ";
	$sql .= "FROM service_active_schedule WHERE source=?";
	$stmt = $dbh->prepare($sql);
	$stmt->bindParam(1, $source);
        $stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rows as $rs)
	{
		print "<row>";
		foreach ($rs as $k => $v)
		{
			if($v != "")
				print "<$k>$v</$k>\n";
		}
		print "</row>";
	}
print "</x>";
?>
