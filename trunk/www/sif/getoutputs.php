<?php
require_once("sif.inc");
$dbh = connect();
$dom = new DOMDocument('1.0', 'utf-8');
$root = $dom->appendChild(new DOMElement('sif'));
$stmt = $dbh->prepare("SELECT output FROM edge_output WHERE edge=?");
$stmt->execute(array($_REQUEST["id"]));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(count($rows)>0)
{
	foreach ($rows as $rs)
	{
		foreach ($rs as $key => $value)
		{
			$root->appendChild(new DOMElement($key, $value));
		}
	}
}
header('Content-type: text/xml');
echo $dom->saveXML();
?>
