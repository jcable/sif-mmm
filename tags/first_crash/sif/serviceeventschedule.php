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
	$sql .= "DATE_FORMAT(event_time, '%Y-%m-%dT%H:%i:%sZ') AS start, ";
	$sql .= "duration, start_mode, name, material_id, rot, ptt, ptt_time, owner ";
	$sql .= "FROM service_events WHERE source=?";
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
