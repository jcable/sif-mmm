<?php
if (isset($_REQUEST["id"]))
{
	header("location: manageredundancy.php");
	require 'connect.php';
	$id=$_REQUEST["id"];
	$idx=$_REQUEST["idx"];
	$tabindex=$_REQUEST["tabindex"];
	$device = $_REQUEST["device"];
	$pcm = $_REQUEST["pcm"];
	mysql_query(<<<EOT
		insert into source2device
			(id, idx, device, pcm, tab_index)
			values('$id', $idx, '$device', '$pcm', $tabindex)
		on duplicate key update
		device = '$device', pcm = '$pcm', tab_index = $tabindex
EOT
, $connection);
}
else
{
echo "Error - no redundancy pair defined";
}
?>
