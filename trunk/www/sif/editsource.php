<html>
<head>
<title>SIF Project - Source Maintenance</title>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">
<h3><img src="wslogo.jpg" alt=""><br>

<body>
<?php
	if (!empty($_REQUEST["source"]))
	{

		// Connect database.
		require 'connect.php';
		$getsource=$_REQUEST["source"];
		$result=mysql_query("select * from source where source='$getsource'", $connection);

		while($row= mysql_fetch_array($result))
		{
		$source=$row["source"];
		$sourcelongname=$row["source_long_name"];
		$enabled=$row["enabled"];
		$active=$row["active"];
		$role=$row["role"];
		$icon=$row["icon"];
		$tabindex=$row["tab_index"];
		$owner=$row["owner"];
		$notes=$row["notes"];
		$pharosindex=$row["pharos_index"];
		$vlchostname=$row["vlc_hostname"];
		$device=$row["device"];
		$port=$row["port"];
		}
		print "SIF Project - Configure Details for Source '$source'</h3>";
	}
	else
	{
		print "Error - Source Not Defined</h3>";
	}

?>

<div>
<form method="post" action="updatesource.php" name="updatesource">
<?
	print "\n<input type=\"hidden\" name=\"originalsource\" value=\"{$source}\">";
?>
<div>
Source:
<br />
<?
	print "\n<input type=\"text\" size=\"100\" maxlength=\"10\" value=\"{$source}\" name=\"source\"/>";
?>
</div>
<div>
Long Name:
<br />
<?
	print "\n<input type=\"text\" size=\"100\" maxlength=\"256\" value=\"{$sourcelongname}\" name=\"sourcelongname\"/>";
?>
</div>
<div>
Enabled:<br>
<?
	if(intval($enabled)==1)
	{
		print "\n<input type=\"checkbox\" name=\"enabled\" checked=\"checked\" value=\"1\">";
	}
	else
	{
		print "\n<input type=\"checkbox\" name=\"enabled\" value=\"1\">";
	}
?>
</div>
<div>
Active:<br>
<?
	if(intval($active)==1)
	{
		print "\n<input type=\"checkbox\" name=\"active\" checked=\"checked\" value=\"1\">";
	}
	else
	{
		print "\n<input type=\"checkbox\" name=\"active\" value=\"1\">";
	}
?>
</div>
<div>
Device:
<br />
<?
	print "\n<input type=\"text\" size=\"10\" maxlength=\"64\" value=\"{$device}\" name=\"device\"/>";
?>
</div>
<div>
<div>
Port:
<br />
<?
	print "\n<input type=\"text\" size=\"10\" maxlength=\"64\" value=\"{$port}\" name=\"port\"/>";
?>
</div>
<div>
Role:
<br />
<select name="role">
<?
	if ($role == "PLAYOUT")
		{
			print "\n<option value=\"PLAYOUT\" selected>PLAYOUT</option>";
			print "\n<option value=\"CAPTURE\">CAPTURE</option>";
		}
		else
		{
			print "\n<option value=\"PLAYOUT\">PLAYOUT</option>";
			print "\n<option value=\"CAPTURE\" selected>CAPTURE</option>";
		}
?>
</select>
</div>
<div>
Pharos Index:
<br />
<?
	print "\n<input type=\"text\" size=\"10\" maxlength=\"4\" value=\"{$pharosindex}\" name=\"pharosindex\"/>";
?>
</div>
<div>
VLC Hostname:
<br />
<?
	print "\n<input type=\"text\" size=\"100\" maxlength=\"256\" value=\"{$vlchostname}\" name=\"vlchostname\"/>";
?>
</div>

<div>
Icon:
<br />
<?
	print "\n<input type=\"text\" size=\"100\" maxlength=\"256\" value=\"{$icon}\" name=\"icon\"/>";
?>
</div>
<div>
Tab:
<br />
<select name="tabindex">
<?
	require 'connect.php';
	$tabresult=mysql_query("select * from source_tabs order by tab_index asc", $connection);
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
Owner:
<br />
<?
	print "\n<input type=\"text\" size=\"100\" maxlength=\"64\" value=\"{$owner}\" name=\"owner\"/>";
?>
</div>
<div>
Notes:
<br />
<?
	print "\n<input type=\"text\" size=\"100\" maxlength=\"256\" value=\"{$notes}\" name=\"notes\"/>";
?>
</div>


<div>
<p>
<input type=submit value="Save Changes">&nbsp<input type=reset value="Clear Changes">&nbsp;
</form>
</div>
<hr>
<a href="managesources.php">Manage sources</a><p>
<div id="footer">
&copy; 2009, Mark Patrick, BBC WS
</div>
</html>