<?php
	require_once("header.php");
	$page = "Logical Device Maintenance";
	sif_header($page, "main.css");
	sif_buttons($page);
	require 'connect.php';
?>
	<table border=1 cellspacing=0 cellpadding=4>
	<form method="post" action="addlogicaldevice.php" name="addlogicaldevice">
	<tr><th>Device</th><th>MAC</th><th>Type</th><th>PCM</th><th colspan=2>Action</th><tr>
	<tr><td><input type="text" name="id" style="background-color:#ffdab9" size=20 maxlength=10 onKeyPress="return submitenter(this,event)"></td>
	<td><select name="macaddress">
	<option selected="selected" value=""></option>
<?php
	$result=mysql_query("SELECT macaddress FROM physicaldevices order by macaddress asc", $connection);
	while($row= mysql_fetch_array($result))
	{
		$id = $row["macaddress"];
		print "<option value=\"$id\">$id</option>";
	}
?>
	</select></td>
	<td><select name="type">
	<option value="SOURCE">SOURCE</option>
	<option value="LISTENER">LISTENER</option>
	</select></td>
	<td><input name="pcm"/></td>
	<td colspan=2 align="center"><input type="Submit" value="Add"></td></tr>
	</form>
<?php
	$result=mysql_query("SELECT * FROM logicaldevices order by macaddress asc", $connection);
	while($row= mysql_fetch_array($result))
	{
?>
		<tr><form method="post" action="editlogicaldevice.php">
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
