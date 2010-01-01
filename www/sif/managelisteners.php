<?php
	require_once("header.php");
	$page = "Listener Maintenance";
	sif_header($page, "main.css");
	sif_buttons($page);
	require 'connect.php';

	$result=mysql_query("SELECT * FROM listener order by id asc", $connection);
	print "<form method=\"post\" action=\"addlistener.php\" name=\"addlistener\">";
	print "<table border=1 cellspacing=0 cellpadding=4>";
	print "<tr><th>Listener:</th><th>Enabled:</th><th colspan=2>Action:</th><tr>";
	print "<tr><td><input type=\"text\" name=\"listener\" style=\"background-color:#ffdab9\" size=20 maxlength=10 onKeyPress=\"return submitenter(this,event)\"></td>";
	print "\n<td><input type=\"checkbox\" name=\"enabled\" value=\"1\"></td>";
	print "<td colspan=2><input type=\"Submit\" value=\"Add Listener\"></td></tr>";
	print "</form>";
	while($row= mysql_fetch_array($result))
	{
		print "\n<tr><form method=\"post\" action=\"editlistener.php\">";
		print "\n<input type=\"hidden\" name=\"listener\" value=\"{$row["id"]}\">";
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
		print "\n</form><form method=\"post\" action=\"deletelistener.php\">";
		print "\n<input type=\"hidden\" name=\"listener\" value=\"{$row["id"]}\">";
		print "\n<td><input type=\"Submit\" value=\"Delete\" onClick=\"return confirmSubmit()\"></td>";
		print "\n</form></tr>";
	}
?>
</table>
<?php sif_footer(); ?>
