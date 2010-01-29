<?php
require_once("sif.inc");
require_once("messaging.inc");
$dbh=connect();
$stmt = $dbh->query("SELECT value FROM configuration WHERE `key`='message_bus_host'",  PDO::FETCH_COLUMN, 0);	
$config = $stmt->fetch();
print_r($config);
$sender = new Sender($config);
$sender->send($_REQUEST["key"], $_REQUEST["message"]);
$sender->close();
?>