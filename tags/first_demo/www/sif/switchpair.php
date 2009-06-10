<?
if (!empty($_REQUEST["pair"]))
{
	header("location: sourcepairs.php?pairtab=".$_REQUEST["pairtab"]);
	require 'connect.php';
	$pair=$_REQUEST["pair"];
	$active=$_REQUEST["active"];
	$pairtab=$_REQUEST["pairtab"];
	// echo $pair;
	// echo "<br>";
	// echo $active;
	// echo "<br>";
	// echo $pairtab;
	// echo "<br>";
	$result=mysql_query("SELECT * FROM redundancy where redundancy_text='$pair'", $connection);
	while($row= mysql_fetch_array($result))
	{
		$main=$row[main];
		$reserve=$row[reserve];
	}
	if ($active==$main)
	{
		mysql_query("update source set active='1' where source='$main'", $connection);
		mysql_query("update source set active='0' where source='$reserve'", $connection);
		mysql_query("update service set current_source='$main' where current_source='$reserve'", $connection);
	}
	else
	{
		mysql_query("update source set active='0' where source='$main'", $connection);
		mysql_query("update source set active='1' where source='$reserve'", $connection);
		mysql_query("update service set current_source='$reserve' where current_source='$main'", $connection);

	}
}
else
{
header("location: pairtab.php?pairtab=1");
echo "Error - no pair defined";
}
?>