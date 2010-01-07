<?php
	require_once("messaging.inc");
	$dbh = new PDO(
	    'mysql:host=localhost;dbname=sif', 'sif', 'sif',
	    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
	); 

if (isset($_REQUEST["listener"]))
{
	header("location: listenercrashswitch.php?servicetab=".$_REQUEST["servicetab"]."&listenertab=".$_REQUEST["listenertab"]);
	$stmt = $dbh->prepare("INSERT INTO listener_active_schedule (first_date,start_time,service,listener) VALUES(?,?,?,?)");
	$d = strftime("%Y-%m-%d");
	$t = strftime("%T");
	$stmt->bindParam(1, $d);
	$stmt->bindParam(2, $t);
	$stmt->bindParam(3, $_REQUEST["service"]);
	$stmt->bindParam(4, $_REQUEST["listener"]);
	$stmt->execute();
	$stmt = $dbh->prepare("INSERT INTO event (event_time,service,listener) VALUES(?,?,?)");
	$dt = "$d $t";
	$stmt->bindParam(1, $dt);
	$stmt->bindParam(2, $_REQUEST["service"]);
	$stmt->bindParam(3, $_REQUEST["listener"]);
	$stmt->execute();

	$sender = new Sender();
	$sender->send($_REQUEST["service"], "refresh");
	$sender->send($_REQUEST["listener"], "refresh");
	$sender->close();

}
else
{
header("location: listenercrashswitch.php");
echo "Error - no listener defined";
}
?>
