<?
require_once("messaging.inc");


$dbh = new PDO(
    'mysql:host=localhost;dbname=sif', 'sif', 'sif',
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
); 
  //$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
if (isset($_REQUEST["service"]))
{
	if(isset($_REQUEST["sourcetab"]) && isset($_REQUEST["servicetab"]))
		header("location: servicecrashswitch.php?sourcetab=".$_REQUEST["sourcetab"]."&servicetab=".$_REQUEST["servicetab"]);
	else
		header('Content-type: text/plain');

/*
   make a new schedule entry - but when should it end ?

how about - if "hold" is true then it ends at midnight unless we are after 23:00 in which case it ends at the next midnight

if hold is false then it ends at the next event

OR - we have events, not schedule periods and we don't need an end!

ALSO we should have a priority field in the schedule which would allow it to work like a stack.
*/

	$stmt = $dbh->prepare("INSERT INTO service_active_schedule (first_date,start_time,service,source) VALUES(?,?,?,?)");
	$d = strftime("%Y-%m-%d");
	$t = strftime("%T");
	$stmt->bindParam(1, $d);
	$stmt->bindParam(2, $t);
	$stmt->bindParam(3, $_REQUEST["service"]);
	$stmt->bindParam(4, $_REQUEST["source"]);
	$stmt->execute();
	$stmt = $dbh->prepare("INSERT INTO event (event_time,service,source) VALUES(?,?,?)");
	$dt = "$d $t";
	$stmt->bindParam(1, $dt);
	$stmt->bindParam(2, $_REQUEST["service"]);
	$stmt->bindParam(3, $_REQUEST["source"]);
	$stmt->execute();

	$sender = new Sender();
	$sender->send($_REQUEST["service"], "refresh");
	$sender->send($_REQUEST["source"], "refresh");
	$sender->close();

}
else
{
header("location: servicecrashswitch.php");
echo "Error - no service defined";
}
?>
