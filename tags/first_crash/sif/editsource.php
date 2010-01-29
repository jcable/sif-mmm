<?php
if (isset($_REQUEST["edit"]))
{
	require_once("header.php");
	$page = "Source Maintenance";
	sif_header($page, "main.css");
	if (!empty($_REQUEST["source"]))
	{

		// Connect database.
		require 'connect.php';
		$getsource=$_REQUEST["source"];
		$result=mysql_query("select * from source where id='$getsource'", $connection);

		while($row= mysql_fetch_array($result))
		{
		$source=$row["id"];
		$sourcelongname=$row["long_name"];
		$enabled=$row["enabled"];
		$role=$row["role"];
		$icon=$row["icon"];
		$tabindex=$row["tab_index"];
		$owner=$row["owner"];
		$notes=$row["notes"];
		$pharosindex=$row["pharos_index"];
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
<input type="hidden" name="originalsource" value="<?php print $source;?>"/>
<div>
Source:
<br />
<input type="text" size="100" maxlength="10" value="<?php print $source;?>" name="source"/>
</div>
<div>
Long Name:
<br />
<input type="text" size="100" maxlength="256" value="<?php print $sourcelongname?>" name="sourcelongname"/>
</div>
<div>
Enabled:<br/>
<input type="checkbox" name="enabled" value="1"
<?
	if(intval($enabled)==1)
	{
		print "checked";
	}
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
<?
	sif_footer();
}
if (isset($_REQUEST["delete"]))
{
	if (!empty($_REQUEST["source"]))
	{
		header("location: managesources.php");
		require 'connect.php';
		$source=$_REQUEST["source"];
		mysql_query("delete from source where id='$source'", $connection);
	}
	else
	{
		echo "Error - Missing source - please go back and check the data.";
	}
}
?>
