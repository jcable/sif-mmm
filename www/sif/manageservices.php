<?php
	require_once("header.php");
	require_once("sif.inc");
	$page = "Service Maintenance";
	sif_header($page, "main.css");
	sif_buttons($page);
	$dbh = connect();
	$stmt=$dbh->query("SELECT * FROM edge WHERE kind='SERVICE' ORDER BY id ASC");
	print "<form method=\"post\" action=\"addservice.php\" name=\"addservice\">";
	print "<table border=1 cellspacing=0 cellpadding=4>";
	print "<tr><th>Service:</th><th>Enabled:</th><th colspan=2>Action:</th><tr>";
	print "<tr><td><input type=\"text\" name=\"service\" style=\"background-color:#ffdab9\" size=20 maxlength=10 onKeyPress=\"return submitenter(this,event)\"></td>";
	print "\n<td><input type=\"checkbox\" name=\"enabled\" value=\"1\"></td>";
	print "<td colspan=2><input type=\"Submit\" value=\"Add Service\"></td></tr>";
	print "</form>";
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		print "\n<tr><form method=\"post\" action=\"editservice.php\">";
		print "\n<input type=\"hidden\" name=\"service\" value=\"{$row["id"]}\">";
		print "\n<td>{$row["id"]}</td>";
		if(intval($row["enabled"])==1)
		{
			print "\n<td>Yes</td>";
		}
		else
		{
			print "\n<td>No</td>";
		}
		print "\n<td><input type=\"Submit\" value=\"Edit\"></td>";
		print "\n</form><form method=\"post\" action=\"deleteservice.php\">";
		print "\n<input type=\"hidden\" name=\"service\" value=\"{$row["id"]}\">";
		print "\n<td><input type=\"Submit\" value=\"Delete\" onClick=\"return confirmSubmit()\"></td>";
		print "\n</form></tr>";
	}
?>
</table>
<?php sif_footer(); ?>
