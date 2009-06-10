<html>
<head>
<title>SIF Project - Listener Maintenance</title>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">
<h3><img src="wslogo.jpg" alt=""><br>

<body>
<?php
	if (!empty($_REQUEST["listener"]))
	{

		// Connect database.
		require 'connect.php';
		$getlistener=$_REQUEST["listener"];
		$result=mysql_query("select * from listener where listener='$getlistener'", $connection);

		while($row= mysql_fetch_array($result))
		{
		$listener=$row["listener"];
		$listenerlongname=$row["listener_long_name"];
		$enabled=$row["enabled"];
		$locked=$row["locked"];
		$defaultservice=$row["default_service"];
		$autoservice=$row["auto_service"];
		$role=$row["role"];
		$icon=$row["icon"];
		$tabindex=$row["tab_index"];
		$owner=$row["owner"];
		$notes=$row["notes"];
		$pharosindex=$row["pharos_index"];
		$vlchostname=$row["vlc_hostname"];

		}
		print "SIF Project - Configure Details for listener '$listener'</h3>";
	}
	else
	{
		print "Error - listener Not Defined</h3>";
	}

?>

<div>
<form method="post" action="updatelistener.php" name="updatelistener">
<?
	print "\n<input type=\"hidden\" name=\"originallistener\" value=\"{$listener}\">";
?>
<div>
listener:
<br />
<?
	print "\n<input type=\"text\" size=\"100\" maxlength=\"10\" value=\"{$listener}\" name=\"listener\"/>";
?>
</div>
<div>
Long Name:
<br />
<?
	print "\n<input type=\"text\" size=\"100\" maxlength=\"256\" value=\"{$listenerlongname}\" name=\"listenerlongname\"/>";
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
Locked:<br>
<?
	if(intval($locked)==1)
	{
		print "\n<input type=\"checkbox\" name=\"locked\" checked=\"checked\" value=\"1\">";
	}
	else
	{
		print "\n<input type=\"checkbox\" name=\"locked\" value=\"1\">";
	}
?>
</div>
<div>
Automatic Service:<br>
<?
	if(intval($autoservice)==1)
	{
		print "\n<input type=\"checkbox\" name=\"autoservice\" checked=\"checked\" value=\"1\">";
	}
	else
	{
		print "\n<input type=\"checkbox\" name=\"autoservice\" value=\"1\">";
	}
?>
</div>
<div>
Role:
<br />
<select name="role">
<?

	switch ($role)
	{
		case "RECORD":
			print "\n<option value=\"OUTPUT\">OUTPUT</option>";
			print "\n<option value=\"RECORD\" selected>RECORD</option>";
			print "\n<option value=\"MONITOR\">MONITOR</option>";
			break;
		case "MONITOR":
			print "\n<option value=\"OUTPUT\">OUTPUT</option>";
			print "\n<option value=\"RECORD\">RECORD</option>";
			print "\n<option value=\"MONITOR\" selected>MONITOR</option>";
			break;
		default:
			print "\n<option value=\"OUTPUT\" selected>OUTPUT</option>";
			print "\n<option value=\"RECORD\">RECORD</option>";
			print "\n<option value=\"MONITOR\">MONITOR</option>";
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
Default Service:
<br />
<select name="defaultservice">
<?
	require 'connect.php';
	$serviceresult=mysql_query("select * from service order by service asc", $connection);
	while($servicerow= mysql_fetch_array($serviceresult))
	{
		if ($defaultservice == $servicerow["service"])
		{
			print "\n<option value=\"{$servicerow["service"]}\" selected>{$servicerow["service"]}</option>";
		}
		else
		{
			print "\n<option value=\"{$servicerow["service"]}\">{$servicerow["service"]}</option>";
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
	$tabresult=mysql_query("select * from listener_tabs order by tab_index asc", $connection);
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
<a href="managelisteners.php">Manage listeners</a><p>
<div id="footer">
&copy; 2009, Mark Patrick, BBC WS
</div>
</html>