<?php
$dbh = new PDO(
    'mysql:host=localhost;dbname=sif', 'sif', 'sif',
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
); 
$dow = strftime("%w");
$dayflags = array( "S______", "_M______", "__T____", "___W___", "____T__", "_____F_", "______S");
$daysmask = $dayflags[$dow];
$sql = "SELECT service_event_id, service, source,";
$sql .= " UNIX_TIMESTAMP(first_date) AS first_date,";
$sql .= " UNIX_TIMESTAMP(last_date) AS last_date,";
$sql .= " days, start_time, duration,";
$sql .= " ADDDATE(CURDATE(), INTERVAL TIME_TO_SEC(start_time) SECOND) AS start_datetime,";
$sql .= " UNIX_TIMESTAMP(CURDATE())+TIME_TO_SEC(start_time)+TIME_TO_SEC(duration) AS end_datetime,";
$sql .= " start_mode, name, material_id, rot, ptt, ptt_time, owner";
$sql .= " FROM service_active_schedule";
$sql .= " WHERE first_date <= DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
$sql .= " AND (last_date IS NULL OR last_date >= CURDATE())";
$sql .= " AND (days IS NULL OR days LIKE ?)";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(1, $daysmask);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sql = "INSERT INTO service_event (event_time, service_event_id) VALUES(?,?)";
$ins_stmt = $dbh->prepare($sql);
$count=0;
foreach ($rows as $rs)
{
	$ins_stmt->bindParam(1, $rs["start_datetime"]);
	$ins_stmt->bindParam(2, $rs["service_event_id"]);
	$ins_stmt->execute();
	$count++;
}
print "added $count events";
?>
