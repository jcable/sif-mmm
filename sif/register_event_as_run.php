<?php
require_once "sif.inc";
$dbh = connect();
$r = register_event_as_run($dbh, $_REQUEST["device"], $_REQUEST["input"], $_REQUEST["output"], $_REQUEST["action"]);
$dom = new DOMDocument('1.0', 'utf-8');
$root = $dom->appendChild(new DOMElement('sif'));
if(is_array($r))
{
	$root->appendChild(new DOMElement("result", $r[0]));
	$root->appendChild(new DOMElement("driver_error", $r[1]));
	$root->appendChild(new DOMElement("msg", $r[2]));
}
else
{
	$root->appendChild(new DOMElement("result", "0"));
}
header('Content-type: text/xml');
echo $dom->saveXML();
?>
