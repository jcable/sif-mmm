<?php
	require_once("header.php");
	require 'sif.inc';
?>
<SCRIPT TYPE="text/javascript">
<!--
function submitenter(myfield,e)
{
var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return true;

if (keycode == 13)
   {
   myfield.form.submit();
   return false;
   }
else
   return true;
}
//-->
</SCRIPT>
<?php
	if (!empty($_REQUEST["type"]))
	{
	$dbh=connect();
	$type=$_REQUEST["type"];
	switch ($type)	{
		case "source":
			$page = "Manage Source Tabs";
			break;
		case "services":
			$page = "Manage Services Tabs";
			break;
		case "listener":
			$page = "Manage Listener Tabs";
			break;
		case "redundancy":
			$page = "Manage Redundancy Tabs";
			break;
		default:
			// default to source tabs if something stupid requested
			$page = "IP Media Router<br>Manage Source Tabs";
	}
	$stmt = $dbh->prepare("SELECT * FROM panel_tabs WHERE kind=? ORDER BY tab_index asc");
	$stmt->execute(array($type));
	sif_header($page, "main.css");
	sif_buttons($page);
	print "<form method=\"post\" action=\"addtab.php\" name=\"addtab\">";
	for($i=0; $i<$stmt->columnCount(); $i++)
	{
		$meta = $stmt->getColumnMeta($i);
		print_r($meta);
	}
		print "\n<input type=\"hidden\" name=\"type\" value=\"{$type}\">";
		print "<table border=1 cellspacing=0 cellpadding=4>";
		print "<tr><th>Index:</th><th>Tab Text:</th><th>Hidden:</th><th colspan=2>Action:</th><tr>";
		print "<tr><td>&nbsp;</td><td><input type=\"text\" name=\"tabtext\" style=\"background-color:#ffdab9\" size=40 maxlength=20 onKeyPress=\"return submitenter(this,event)\"></td>";
		print "\n<td><input type=\"checkbox\" name=\"hidden\" value=\"0\"></td>";
	print "<td colspan=2><input type=\"Submit\" value=\"Add Tab\"></td></tr>";
	print "</form>";
	while($row=$stmt->fetch(PDO::FETCH_ASSOC))
	{
		print "\n<tr><form method=\"post\" action=\"updatetab.php\">";
		print "\n<input type=\"hidden\" name=\"type\" value=\"{$type}\">";
		print "\n<input type=\"hidden\" name=\"id\" value=\"{$row["tab_index"]}\">";
		print "\n<td>{$row["tab_index"]}</td>";
		print "\n<td><input type=\"text\" name=\"tabtext\" size=40 maxlength=20 value=\"{$row["tab_text"]}\" onKeyPress=\"return submitenter(this,event)\"></td>";
		if(intval($row["hidden"])==1)
		{
			print "\n<td><input type=\"checkbox\" name=\"hidden\" checked=\"checked\" value=\"1\"></td>";
		}
		else
		{
			print "\n<td><input type=\"checkbox\" name=\"hidden\" value=\"1\"></td>";
		}
		print "\n<td><input type=\"Submit\" value=\"Update\"></td>";
		print "\n</form><form method=\"post\" action=\"deletetab.php\">";
		print "\n<input type=\"hidden\" name=\"type\" value=\"{$type}\">";
		print "\n<input type=\"hidden\" name=\"id\" value=\"{$row["tab_index"]}\">";
		print "\n<td><input type=\"Submit\" value=\"Delete\" onClick=\"return confirmSubmit()\"></td>";
		print "\n</form></tr>";
	}
}
?>
</table>
<?php sif_footer(); ?>
