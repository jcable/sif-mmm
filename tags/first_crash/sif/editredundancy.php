<?php
	require_once("header.php");
	$page = "Redundancy Maintenance";
	sif_header($page, "main.css");
	sif_buttons($page);
	require 'connect.php';
	if (isset($_REQUEST["id"]) && isset($_REQUEST["idx"]))
	{
		$id=$_REQUEST["id"];
		$idx=$_REQUEST["idx"];
		$type=$_REQUEST["type"];
		if($type=="SOURCE")
			$table = "source2device";
		else
			$table = "listener2device";
		$result=mysql_query("select * from $table where id='$id' and idx=$idx", $connection);

		while($row= mysql_fetch_array($result))
		{
			$device = $row;
		}
		print "SIF Project - Configure Details for ".ucfirst(strtolower($type))." '$id'</h3>";
	}
	else
	{
		$type = "SOURCE";
		$table = "source2device";
		$device = array(pcm => "", active => 0, tabindex => 1);
	}

?>

<div>
<form method="post" action="updateredundancy.php" name="updatesource">
<?php
	print "\n<input type=\"hidden\" name=\"id\" value=\"$id\">";
	print "\n<input type=\"hidden\" name=\"idx\" value=\"$idx\">";
	$result=mysql_query("select id from physicaldevices order by id asc", $connection);
	print "\n<br/>Device ";
	print "<select name=\"device\">";
	$sel = false;
	while($row = mysql_fetch_array($result))
	{
		$id = $row["id"];
		print "\n<option value=\"$id\"";
		if ($id == $device["device"])
		{
			print " selected=\"selected\"";
			$sel = true;
		}
		print ">$id</option>";
	}
	print '<option';
	if (!$sel)
	{
		print " selected=\"selected\"";
	}
	print ' value="">None</option>';
	print '</select>';
	print " PCM ";
	print "<input name=\"pcm$i\"/>";
?>
</div>
<div>
Tab:
<br />
<select name="tabindex">
<?
	$tabresult=mysql_query("select * from ${table}_tabs order by tab_index asc", $connection);
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
