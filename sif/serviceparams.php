<?php
if(isset($_REQUEST["service"]))
	$service = $_REQUEST["service"];
else
	$service = "ENNWS";
$dbh = new PDO(
    'mysql:host=localhost;dbname=sif', 'sif', 'sif',
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
); 
print "<x>";
	$sql = "SELECT ipv4_group_address, type, port FROM service JOIN service_components USING(service) WHERE service=?";
	$stmt = $dbh->prepare($sql);
	$stmt->bindParam(1, $service);
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
