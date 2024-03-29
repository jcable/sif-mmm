<?php
require_once "sif.inc";
$dbh = connect();
if(isset($_REQUEST["service"]))
{
	$service = $_REQUEST["service"];
}
else
{
	$service = "%";
}
$events = active_schedule_records($dbh,$service);
$dom = new DOMDocument('1.0', 'utf-8');
$root = $dom->appendChild(new DOMElement('sif'));
foreach ($events as $rs)
{
	$element = $root->appendChild(new DOMElement('event'));
	foreach ($rs as $key => $value)
	{
		if($value!="")
		{
			$e = new DOMElement($key, $value);
			$element->appendChild($e);
		}
	}
}
header('Content-type: text/xml');
echo $dom->saveXML();
?>
