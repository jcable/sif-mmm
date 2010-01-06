<?
$dbh = new PDO(
    'mysql:host=localhost;dbname=sif', 'sif', 'sif',
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
); 
if (isset($_REQUEST["service"]))
{
	header("location: servicecrashswitch.php?sourcetab=".$_REQUEST["sourcetab"]."&servicetab=".$_REQUEST["servicetab"]);
	//mysql_query("update service set current_source='$source' where service='$service'", $connection);
	$stmt = $dbh->prepare("INSERT INTO service_active_schedule (first_date,start_time,service,source) VALUES(?,?,?,?)");
	$stmt->bindParam(1, $today);
	$stmt->bindParam(2, $now);
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
