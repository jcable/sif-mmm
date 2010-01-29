<?php
	require_once("header.php");
	$page = "Source Maintenance";
	sif_header($page, "main.css");
	sif_buttons($page);
	require 'connect.php';
	$result=mysql_query("SELECT * FROM source order by id asc", $connection);
?>
	<table border=1 cellspacing=0 cellpadding=4>
	<form method="post" action="addsource.php" name="addsource">
	<tr><th>Source:</th><th>Enabled:</th><th colspan=2>Action:</th><tr>
	<tr><td><input type="text" name="source" style="background-color:#ffdab9" size=20 maxlength=10 onKeyPress="return submitenter(this,event)"></td>
	<td><input type="checkbox" name="enabled" value="1"></td>
	<td colspan=2><input type="Submit" value="Add Source"></td></tr>
	</form>
<?php
	while($row= mysql_fetch_array($result))
	{
?>
		<tr><form method="post" action="editsource.php">
		<td>
		<input type="hidden" name="source" value="<?php print $row["id"];?>">
		<?php print $row["id"];?>
		</td>
		<td> <?php print (intval($row["enabled"])==1)?"Yes":"No"; ?> </td>
		<td><input name="edit" type="Submit" value="Edit"></td>
		<td><input name="delete" type="Submit" value="Delete" onClick="return confirmSubmit()"></td>
		</form></tr>
<?php
	}
?>
</table>
<?php sif_footer(); ?>
