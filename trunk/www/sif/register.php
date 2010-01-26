<?php
require_once("sif.inc");
if(isset($_REQUEST["mac"]))
	$mac = $_REQUEST["mac"];
else
	$mac = "002481351AB1";
$addr = $_SERVER["REMOTE_ADDR"];
$dbh = connect(); 
$dom = new DOMDocument('1.0', 'utf-8');
$root = $dom->appendChild(new DOMElement('sif'));
$stmt = $dbh->prepare("SELECT * FROM configuration WHERE `key`='message_bus_host'");
$stmt->execute();
$config = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($config as $rs)
{
	$root->appendChild(new DOMElement($rs["key"], $rs["value"]));
}
$root->appendChild(new DOMElement('physical'));
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
	$physical = $root->appendChild(new DOMElement('physical'));
    foreach ($rows as $rs)
    {
		foreach ($rs as $key => $value)
	    {
			$physical->appendChild(new DOMElement($key, $value));
		}
		$id = $rs["id"];
	}
	$stmt = $dbh->prepare("SELECT d.id,d.active,d.input,e.loop FROM source2device d JOIN edge e USING(id) WHERE device=?");
    $stmt->execute(array($id));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if(count($rows)>0)
	{
		foreach ($rows as $rs)
		{
			$source = $root->appendChild(new DOMElement('source'));
			foreach ($rs as $key => $value)
			{
				$source->appendChild(new DOMElement($key, $value));
			}
		}
	}
	$stmt = $dbh->prepare("SELECT d.id,d.active,e.input,e.loop FROM listener2device d JOIN edge e USING(id) WHERE device=?");
    $stmt->execute(array($id));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if(count($rows)>0)
	{
		foreach ($rows as $rs)
		{
			$listener = $root->appendChild(new DOMElement('listener'));
			foreach ($rs as $key => $value)
			{
				$listener->appendChild(new DOMElement($key, $value));
			}
		}
	}
}
$root->appendChild(new DOMElement('response', "OK + $mac"));
header('Content-type: text/xml');
echo $dom->saveXML();
?>