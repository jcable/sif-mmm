<?php
	require_once("header.php");
	$page = "Physical Device Maintenance";
	sif_header($page, "main.css");
	sif_buttons($page);
	require 'connect.php';
	$result=mysql_query("SELECT * FROM physicaldevices order by macaddress asc", $connection);
?>
	<table border=1 cellspacing=0 cellpadding=4>
	<form method="post" action="addphysicaldevice.php" name="addphysicaldevice">
	<tr><th>MAC</th><th>Location</th><th>Type</th><th colspan=2>Action</th><tr>
	<tr><td><input type="text" name="macaddress" style="background-color:#ffdab9" size=20 maxlength=10 onKeyPress="return submitenter(this,event)"></td>
	<td><input name="location"/></td>
	<td><select name="type">
	<option value="SOURCE">SOURCE</option>
	<option value="LISTENER">LISTENER</option>
	</select></td>
	<td colspan=2 align="center"><input type="Submit" value="Add"></td></tr>
	</form>
<?php
	while($row= mysql_fetch_array($result))
	{
?>
		<tr><form method="post" action="editphysicaldevice.php">
		<td>
		<input type="hidden" name="macaddress" value="<?php print $row["macaddress"];?>">
		<?php print $row["macaddress"];?>
		</td>
		<td> <?php print $row["location"]; ?> </td>
		<td> <?php print $row["type"]; ?> </td>
		<td><input name="edit" type="Submit" value="Edit"></td>
		<td><input name="delete" type="Submit" value="Delete" onClick="return confirmSubmit()"></td>
		</form></tr>
<?php
	}
?>
</table>
<?php sif_footer(); ?>
