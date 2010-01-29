<?php
	function addform($dbh)
	{
?>
		<form method="post" action="managephysicaldevices.php" name="addphysicaldevice">
		<tr>
		<td><input type="text" name="macaddress" size=20 maxlength=10 onKeyPress="return submitenter(this,event)"></td>
		<td><input name="location"/></td>
		<td><input name="id"/></td>
		<td>
		<select name="id">
<?php
		$stmt = $dbh->prepare("SELECT * FROM physicaldevicetypes");
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($rows as $row)
		{
			print "<option value=\"$id\">{$row["type"]}</option>";
		}
?>
		</select>
		</td>
		<td colspan=2 align="center"><input name="add" type="Submit" value="Add"></td>
		</tr>
		</form>
<?php
	}

	$show_all = true;
	$dbh = new PDO(
	    'mysql:host=localhost;dbname=sif', 'sif', 'sif',
	    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
	); 
	if(isset($_REQUEST["macaddress"]))
	{
		if(isset($_REQUEST["add"]))
		{
			$stmt = $dbh->prepare("INSERT INTO physicaldevices (id,macaddress,location,type) VALUES(?,?,?,?)");
			$stmt->bindParam(1, $_REQUEST["id"]);
			$stmt->bindParam(2, $_REQUEST["macaddress"]);
			$stmt->bindParam(3, $_REQUEST["location"]);
			$stmt->bindParam(4, $_REQUEST["type"]);
			$stmt->execute();
		}
		if(isset($_REQUEST["delete"]))
		{
			$stmt = $dbh->prepare("DELETE FROM physicaldevices WHERE macaddress = ?");
			$stmt->bindParam(1, $_REQUEST["macaddress"]);
			$stmt->execute();
		}
		if(isset($_REQUEST["edit"]))
		{
			$stmt = $dbh->prepare("SELECT * FROM physicaldevices WHERE macaddress=?");
			$stmt->bindParam(1, $_REQUEST["macaddress"]);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<?php
			$show_all = false;
		}
		if(isset($_REQUEST["update"]))
		{
			$stmt = $dbh->prepare("UPDATE physicaldevices SET id=?,location=?,type=? WHERE macaddress=?");
			$stmt->bindParam(1, $_REQUEST["id"]);
			$stmt->bindParam(2, $_REQUEST["location"]);
			$stmt->bindParam(3, $_REQUEST["type"]);
			$stmt->bindParam(4, $_REQUEST["macaddress"]);
			$stmt->execute();
		}
	}
	require_once("header.php");
	$page = "Physical Device Maintenance";
	sif_header($page, "main.css");
	sif_buttons($page);

	if($show_all)
	{
		print "\n<table border=1 cellspacing=0 cellpadding=4>";
		print "\n<tr><th>MAC</th><th>Location</th><th>Id</th><th>Type</th><th colspan=2>Action</th><tr>";
		addform($dbh);
		$stmt = $dbh->prepare("SELECT * FROM physicaldevices ORDER BY macaddress");
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($rows as $row)
		{
?>
			<tr><form method="post" action="managephysicaldevices.php">
			<td>
			<input type="hidden" name="macaddress" value="<?php print $row["macaddress"];?>">
			<?php print $row["macaddress"];?>
			</td>
			<td> <?php print $row["location"]; ?> </td>
			<td> <?php print $row["id"]; ?> </td>
			<td> <?php print $row["type"]; ?> </td>
			<td><input name="edit" type="Submit" value="Edit"></td>
			<td><input name="delete" type="Submit" value="Delete" onClick="return confirmSubmit()"></td>
			</form></tr>
<?php
		}
?>
		</table>
<?php
	}
	else
	{
?>
		<form method="post" action="managephysicaldevices.php">
		<input type="hidden" name="macaddress" value="<?php print $row["macaddress"];?>">
		<table>
<?php
		foreach($row as $key => $value)
		{
			print "<tr><td>$key</td><td><input name=\"$key\" value=\"$value\"/></td></tr>";
		}
?>
		</table>
		<input name="update" type="Submit" value="Update">
		</form>
<?php
	}
?>`
<?php sif_footer(); ?>
