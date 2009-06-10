<html>
<head>
<title>SIF Project - Redundancy Maintenance</title>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">
<h3><img src="wslogo.jpg" alt=""><br>

<body>
<?php
	if (!empty($_REQUEST["text"]))
	{

		// Connect database.
		require 'connect.php';
		$gettext=$_REQUEST["text"];
		$result=mysql_query("select * from redundancy where redundancy_text='$gettext'", $connection);

		while($row= mysql_fetch_array($result))
		{
		$text=$row["redundancy_text"];
		$type=$row["redundancy_type"];
		$main=$row["main"];
		$reserve=$row["reserve"];
		$tabindex=$row["tab_index"];
		}
		print "SIF Project - Configure Details for ".ucfirst(strtolower($type))." Pairing '$text'</h3>";
	}
	else
	{
		print "Error - Pairing Not Defined</h3>";
	}

?>

<div>
<form method="post" action="updateredundancy.php" name="updatesource">
<?
	print "\n<input type=\"hidden\" name=\"text\" value=\"{$text}\">";
?>
Main:
<br />
<select name="main">
<?
	require 'connect.php';
	if ($type=="LISTENER")
	{
		$mresult=mysql_query("select * from listener order by listener asc", $connection);
		while($mrow= mysql_fetch_array($mresult))
		{
			if ($main == $mrow["listener"])
			{
				print "\n<option value=\"{$mrow["listener"]}\" selected>{$mrow["listener"]}</option>";
			}
			else
			{
				print "\n<option value=\"{$mrow["listener"]}\">{$mrow["listener"]}</option>";
			}
		}
	}
	else
	{
		$mresult=mysql_query("select * from source order by source asc", $connection);
		while($mrow= mysql_fetch_array($mresult))
		{
			if ($main == $mrow["source"])
			{
				print "\n<option value=\"{$mrow["source"]}\" selected>{$mrow["source"]}</option>";
			}
			else
			{
				print "\n<option value=\"{$mrow["source"]}\">{$mrow["source"]}</option>";
			}
		}
	}

?>
</select>
</div>
<div>
Reserve:
<br />
<select name="reserve">
<?
	require 'connect.php';
	if ($type=="LISTENER")
	{
		$rresult=mysql_query("select * from listener order by listener asc", $connection);
		while($rrow= mysql_fetch_array($rresult))
		{
			if ($reserve == $rrow["listener"])
			{
				print "\n<option value=\"{$rrow["listener"]}\" selected>{$rrow["listener"]}</option>";
			}
			else
			{
				print "\n<option value=\"{$rrow["listener"]}\">{$rrow["listener"]}</option>";
			}
		}
	}
	else
	{
		$mresult=mysql_query("select * from source order by source asc", $connection);
		while($rrow= mysql_fetch_array($mresult))
		{
			if ($reserve == $rrow["source"])
			{
				print "\n<option value=\"{$rrow["source"]}\" selected>{$rrow["source"]}</option>";
			}
			else
			{
				print "\n<option value=\"{$rrow["source"]}\">{$rrow["source"]}</option>";
			}
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
<hr>
<a href="manageredundancy.php">Manage Redundancy Pairs</a><p>
<div id="footer">
&copy; 2009, Mark Patrick, BBC WS
</div>
</html>