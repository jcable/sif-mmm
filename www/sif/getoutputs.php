<?php
require_once("sif.inc");
$dbh = connect();
$dom = new DOMDocument('1.0', 'utf-8');
$root = $dom->appendChild(new DOMElement('sif'));
$stmt = $dbh->prepare("SELECT vlc, access, mux, dst FROM edge_output o JOIN encoding e ON o.encoding=e.id WHERE edge=?");
$stmt->execute(array($_REQUEST["id"]));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(count($rows)>0)
{
	foreach ($rows as $rs)
	{
		$value = $rs["vlc"].":std{access=".$rs["access"].",mux=".$rs["mux"].",dst=".$rs["dst"]."}";
		$root->appendChild(new DOMElement("output", $value));
	}
}
header('Content-type: text/xml');
echo $dom->saveXML();
?>
