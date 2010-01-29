<?php
require_once("sif.inc");
$dbh = connect();
$dom = new DOMDocument('1.0', 'utf-8');
$root = $dom->appendChild(new DOMElement('sif'));
$stmt = $dbh->prepare("SELECT active, output FROM listener2device WHERE device=? AND id=?");
$stmt->execute(array($_REQUEST["device"],$_REQUEST["id"]));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(count($rows)>0)
{
	foreach ($rows as $row)
	{
		foreach ($row as $key => $value)
		{
			$root->appendChild(new DOMElement($key, $value));
		}
	}
}
header('Content-type: text/xml');
echo $dom->saveXML();
?>
