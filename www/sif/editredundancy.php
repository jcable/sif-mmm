<html>
<head>
<title>SIF Project - Redundancy Maintenance</title>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">
<h3><img src="wslogo.jpg" alt=""><br>

<body>
<?php
	require 'connect.php';
	$devices = array();
	if (!empty($_REQUEST["text"]))
	{
		$text=$_REQUEST["text"];
		$result=mysql_query("select * from redundancy where id='$text'", $connection);

		while($row= mysql_fetch_array($result))
		{
			$type = $row["redundancy_type"];
			$devices[$row["device"]] = array( type => $type,
						active => $row["active"],
						tabindex => $row["tab_index"]);
		}
		print "SIF Project - Configure Details for ".ucfirst(strtolower($type))." Pairing '$text'</h3>";
	}
	else
	{
		print "Pairing Not Defined Yet</h3>";
		$type = "SOURCE";
	}

?>

<div>
<form method="post" action="updateredundancy.php" name="updatesource">
<?
	print "\n<input type=\"hidden\" name=\"text\" value=\"{$text}\">";
?>
Devices (2 or more):
<br />
<select multiple name="device">
<?
		$mresult=mysql_query("select id from logicaldevices where type='$type' order by id asc", $connection);
		while($mrow= mysql_fetch_array($mresult))
		{
			print "\n<option value=\"{$mrow["id"]}\"";
			if (isset($devices[$mrow["id"]]))
			{
				print " selected";
			}
			print ">{$mrow["listener"]}</option>";
		}
	}
?>
</select>
</div>
<div>
Tab:
<br />
<select name="tabindex">
<?
	require 'connect.php';
	$tabresult=mysql_query("select * from redundancy_tabs order by tab_index asc", $connection);
	while($tabrow= mysql_fetch_array($tabresult))
	{
		if ($tabindex == $tabrow["tab_index"])
		{
			print "\n<option value=\"{$tabrow["tab_index"]}\" selected>{$tabrow["tab_text"]}</option>";
		}
		else
		{
			print "\n<option value=\"{$tabrow["tab_index"]}\">{$tabrow["tab_text"]}</option>";
		}
	}
?>
</select>
</div>
<div>
<p>
<input type=submit value="Save Changes">&nbsp<input type=reset value="Clear Changes">&nbsp;
</form>
</div>
<?php sif_footer(); ?>
