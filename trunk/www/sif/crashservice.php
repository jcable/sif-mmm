<?
$dbh = new PDO(
    'mysql:host=localhost;dbname=sif', 'sif', 'sif',
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
); 
if (isset($_REQUEST["service"]))
{
	header("location: servicecrashswitch.php?sourcetab=".$_REQUEST["sourcetab"]."&servicetab=".$_REQUEST["servicetab"]);

/*
   make a new schedule entry - but when should it end ?

how about - if "hold" is true then it ends at midnight unless we are after 23:00 in which case it ends at the next midnight

if hold is false then it ends at the next event

OR - we have events, not schedule periods and we don't need an end!

ALSO we should have a priority field in the schedule which would allow it to work like a stack.

	$stmt = $dbh->prepare("INSERT INTO service_active_schedule (first_date,start_time,service,source) VALUES(?,?,?,?)");
	$stmt->bindParam(1, strftime("%Y-%m-%d"));
	$stmt->bindParam(2, strftime("%T");
	$stmt->bindParam(3, $_REQUEST["service"]);
	$stmt->bindParam(4, $_REQUEST["source"]);
	$stmt->execute();
}
else
{
header("location: servicecrashswitch.php?sourcetab=1&servicetab=1");
echo "Error - no service defined";
}
?>
