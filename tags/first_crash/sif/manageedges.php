<?php
	require_once("header.php");
	require_once("sif.inc");
	if(isset($_REQUEST["kind"]))
	{
		$kind=strtoupper($_REQUEST["kind"]);
	}
	else
	{
		$kind="SOURCE";
	}
	$lckind=strtolower($kind);
	$ickind=ucwords($lckind);
	$page = "$ickind Maintenance";
	sif_header($page, "main.css");
	sif_buttons($page);
	$dbh=connect();

	print "<table border=1 cellspacing=0 cellpadding=4>";

	print "<tr><th>{$ickind}:</th><th>Tab Index:</th><th colspan=2>Action:</th><tr>";

	print "<form method=\"post\" action=\"add{$lckind}.php\" name=\"add{$lckind}\">";
	print "<tr><td><input type=\"text\" name=\"{$lckind}\" style=\"background-color:#ffdab9\" size=20 maxlength=10 onKeyPress=\"return submitenter(this,event)\"></td>";
	print "\n<td><input type=\"text\" size=\"6\" name=\"tab_index\" value=\"1\"></td>";
	print "<td colspan=2><input type=\"Submit\" value=\"Add {$ickind}\"></td></tr>";
	print "\n<input type=\"hidden\" name=\"action\" value=\"add\">";
	print "</form>";

	$stmt=$dbh->prepare("SELECT * FROM edge WHERE kind=? ORDER BY id ASC");
	$stmt->execute(array($kind));
	while($row=$stmt->fetch(PDO::FETCH_ASSOC))
	{
		print "\n<tr><form method=\"post\" action=\"editedge.php\">";
		print "\n<input type=\"hidden\" name=\"kind\" value=\"{$kind}\">";
		print "\n<input type=\"hidden\" name=\"action\" value=\"edit\">";
		print "\n<input type=\"hidden\" name=\"id\" value=\"{$row["id"]}\">";
		print "\n<td>{$row["id"]}</td>";
		print "\n<td><input type=\"text\" size=\"6\" name=\"tab_index\" value=\"{$row["tab_index"]}\"></td>";
		print "\n<td><input type=\"Submit\" value=\"Edit\"></td>";
		print "\n</form><form method=\"post\" action=\"editedge.php\">";
		print "\n<input type=\"hidden\" name=\"kind\" value=\"{$kind}\">";
		print "\n<input type=\"hidden\" name=\"action\" value=\"delete\">";
		print "\n<input type=\"hidden\" name=\"id\" value=\"{$row["id"]}\">";
		print "\n<td><input type=\"Submit\" value=\"Delete\" onClick=\"return confirmSubmit()\"></td>";
		print "\n</form></tr>";
	}
?>
</table>
<?php sif_footer(); ?>
