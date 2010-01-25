<?php
	function choice($sql, $args, $name, $dbh)
	{
		$stmt=$dbh->prepare($sql);
		$stmt->execute($args);
		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
		print "<select name=\"$name\">";
		foreach($rows as $row)
		{
			$id = $row[$name];
			print "<option value=\"$id\">$id</option>";
		}
		print "</select>";
	}

	function add_insert_form($type, $dbh)
	{
		print "\n<form method=\"post\" action=\"addredundancy.php\" name=\"addredundancy\">";
		print "<td>";
		choice("SELECT id FROM edge WHERE kind=?", array(strtoupper($type)), "id", $dbh);
		print "</td>";
		print "\n<td><input name=\"type\" value=\"".strtoupper($type)."\" disabled/></td>";
		print "<td>";
		choice("SELECT id AS device FROM physicaldevices", array(), "device", $dbh);
		print "</td>";
		print "<td colspan=2><input type=\"Submit\" value=\"Add ".ucfirst(strtolower($type))." mapping\"></td></tr>";
		print "</form>";
	}

	function add_update_form($type, $dbh)
	{
		$table = strtolower($type)."2device";
		$sql = "SELECT * FROM $table order by id asc";
		$stmt=$dbh->query($sql);
		while($row=$stmt->fetch(PDO::FETCH_ASSOC))
		{
			print "\n<tr><form method=\"post\" action=\"editredundancy.php\">";
			print "\n<input type=\"hidden\" name=\"id\" value=\"".$row["id"]."\">";
			print "\n<input type=\"hidden\" name=\"idx\" value=\"".$row["idx"]."\">";
			print "\n<input type=\"hidden\" name=\"type\" value=\"$type\">";
			print "\n<td>{$row["id"]}&nbsp;</td>";
			print "\n<td>{$row["type"]}&nbsp;</td>";
			print "\n<td>{$row["device"]}&nbsp;</td>";
			print "\n<td><input type=\"Submit\" value=\"Edit\"></td>";
			print "\n</form><form method=\"post\" action=\"deleteredundancy.php\">";
			print "\n<input type=\"hidden\" name=\"id\" value=\"{$row["id"]}\">";
			print "\n<input type=\"hidden\" name=\"idx\" value=\"{$row["idx"]}\">";
			print "\n<td><input type=\"Submit\" value=\"Delete\" onClick=\"return confirmSubmit()\"></td>";
			print "\n</form></tr>";
		}
	}

	require_once("header.php");
	require_once("sif.inc");
	$page = "Logical to Physical mapping and redundancy";
	sif_header($page, "main.css");
	sif_buttons($page);
	$dbh=connect();

	print "\n<table border=1 cellspacing=0 cellpadding=4>";
	print "\n<tr><th>Source/Listener</th><th>Type</th><th>Device</th><th colspan=2>Action</th><tr>";
	add_insert_form("source", $dbh);
	add_insert_form("listener", $dbh);
	add_update_form("source", $dbh);
	add_update_form("listener", $dbh);
?>
</table>
<?php sif_footer(); ?>
