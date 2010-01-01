<?php
if(isset($_REQUEST["mac"]))
	$mac = $_REQUEST["mac"];
else
	$mac = "002481351AB1";
$addr = $_SERVER["REMOTE_ADDR"];
$dbh = new PDO(
    'mysql:host=localhost;dbname=sif', 'sif', 'sif',
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
); 
print "<x>";
$stmt = $dbh->prepare("SELECT id,location,type FROM physicaldevices WHERE macaddress=?");

	$stmt->bindParam(1, $mac);
        $stmt->execute();
       $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(count($rows)==0)
{
unset($stmt);
$stmt = $dbh->prepare("INSERT INTO physicaldevices (macaddress,id) VALUES(?,?)");
	$stmt->bindParam(1, $mac);
	$stmt->bindParam(2, $addr);
        $stmt->execute();
}
else
{
print "<physical>";
       foreach ($rows as $rs)
    {
       foreach ($rs as $k => $v) { print "<$k>$v</$k>\n"; }
	$id = $rs["id"];
         }
print "</physical>";
}
$stmt = $dbh->prepare("SELECT * FROM redundancy WHERE device=?");

	$stmt->bindParam(1, $id);
        $stmt->execute();
       $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(count($rows)>0)
{
print "<source>";
       foreach ($rows as $rs)
    {
       foreach ($rs as $k => $v) { print "<$k>$v</$k>\n"; }
         }
print "</source>";
}
print "<response>OK + $mac</response>";
print "</x>";
?>
