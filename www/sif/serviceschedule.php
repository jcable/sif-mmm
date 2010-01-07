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
	$stmt = $dbh->prepare("SELECT * FROM service_active_schedule WHERE source=?");
	$stmt->bindParam(1, $source);
        $stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rows as $rs)
	{
		print "<row>";
		foreach ($rs as $k => $v) { print "<$k>$v</$k>\n"; }
		print "</row>";
	}
print "</x>";
?>
