<?php
$dbh = new PDO(
    'mysql:host=localhost;dbname=sif', 'sif', 'sif',
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
); 
if(isset($_REQUEST["key"]))
{
	$sql = "SELECT * FROM configuration WHERE `key`=?";
	$stmt = $dbh->prepare($sql);
	$stmt->bindParam(1, $_REQUEST["key"]);
}
else
{
	$sql = "SELECT * FROM configuration";
	$stmt = $dbh->prepare($sql);
}
$stmt->execute();
print "<x>";
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $rs)
{
	if($rs["value"] != "")
	{
		print "<".$rs["key"].">".$rs["value"]."</".$rs["key"].">\n";
	}
}
print "</x>";
?>
