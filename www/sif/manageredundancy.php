<?php
function add_form($type, $connection)
{
	print "\n<form method=\"post\" action=\"addredundancy.php\" name=\"addredundancy\">";
	print "<td>";
	$sql="SELECT id FROM ".strtolower($type)." order by id asc";
	$result=mysql_query($sql, $connection);
	print "<select name=\"text\">";
	while($row= mysql_fetch_array($result))
	{
		$id = $row["id"];
		print "<option value=\"$id\">$id</option>";
	}
	print "</select>";
	print "</td>";
	print "\n<td><input name=\"type\" value=\"".strtoupper($type)."\" disabled/></td>";
	print "<td>&nbsp;</td>";
	print "<td colspan=2><input type=\"Submit\" value=\"Add ".ucfirst(strtolower($type))."\"></td></tr>";
	print "</form>";
}
	require_once("header.php");
	$page = "Redundancy Maintenance";
	sif_header($page, "main.css");
	sif_buttons($page);
	require 'connect.php';

	print "\n<table border=1 cellspacing=0 cellpadding=4>";
	print "\n<tr><th>Source/Listener</th><th>Type</th><th>Device</th><th colspan=2>Action</th><tr>";
	add_form("source", $connection);
	add_form("listener", $connection);

	$result=mysql_query("SELECT * FROM redundancy order by redundancy_type,id asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		print "\n<tr><form method=\"post\" action=\"editredundancy.php\">";
		print "\n<input type=\"hidden\" name=\"text\" value=\"{$row["id"]}\">";
		print "\n<td>{$row["id"]}&nbsp;</td>";
		print "\n<td>{$row["redundancy_type"]}&nbsp;</td>";
		print "\n<td>{$row["device"]}&nbsp;</td>";
		print "\n<td><input type=\"Submit\" value=\"Edit\"></td>";
		print "\n</form><form method=\"post\" action=\"deleteredundancy.php\">";
		print "\n<input type=\"hidden\" name=\"text\" value=\"{$row["id"]}\">";
		print "\n<td><input type=\"Submit\" value=\"Delete\" onClick=\"return confirmSubmit()\"></td>";
		print "\n</form></tr>";
	}
?>
</table>
<?php sif_footer(); ?>
