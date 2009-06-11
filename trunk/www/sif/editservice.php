<html>
<head>
<title>SIF Project - Service Maintenance</title>
</head>
<link rel="stylesheet" type="text/css" href="main.css" media="screen,print">
<h3><img src="wslogo.jpg" alt=""><br>

<body>
<?php
	if (!empty($_REQUEST["service"]))
	{

		// Connect database.
		require 'connect.php';
		$getservice=$_REQUEST["service"];
		$result=mysql_query("select * from service where service='$getservice'", $connection);

		while($row= mysql_fetch_array($result))
		{
		$service=$row["service"];
		$servicelongname=$row["service_long_name"];
		$enabled=$row["enabled"];
		$locked=$row["locked"];
		$icon=$row["icon"];
		$tabindex=$row["tab_index"];
		$owner=$row["owner"];
		$notes=$row["notes"];
		$pharosindex=$row["pharos_index"];
		$multicastid=$row["multicast_id"];
		}
		print "SIF Project - Configure Details for Service '$service'</h3>";
	}
	else
	{
		print "Error - Service Not Defined</h3>";
	}

?>

<div>
<form method="post" action="updateservice.php" name="updateservice">
<?
	print "\n<input type=\"hidden\" name=\"originalservice\" value=\"{$service}\">";
?>
<div>
Service:
<br />
<?
	print "\n<input type=\"text\" size=\"100\" maxlength=\"10\" value=\"{$service}\" name=\"service\"/>";
?>
</div>
<div>
Long Name:
<br />
<?
	print "\n<input type=\"text\" size=\"100\" maxlength=\"256\" value=\"{$servicelongname}\" name=\"servicelongname\"/>";
?>
</div>
<div>


<?
	print "\n<b>Multicast ID:&nbsp;{$multicastid}</b><br><br>";
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
Pharos Index:
<br />
<?
	print "\n<input type=\"text\" size=\"10\" maxlength=\"4\" value=\"{$pharosindex}\" name=\"pharosindex\"/>";
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
	$tabresult=mysql_query("select * from services_tabs order by tab_index asc", $connection);
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
<a href="manageservices.php">Manage Services</a><p>
<div id="footer">
&copy; 2009, Mark Patrick, BBC WS
</div>
</html>