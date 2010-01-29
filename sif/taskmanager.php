<?php
	require_once("header.php");
	$page = "Task Manager";
	sif_header($page, "main.css");
	sif_buttons($page);
?>
<p>
<div id="plaincontent">
<table class="example table-autosort:0 table-stripeclass:alternate" border=1 cellspacing=0 cellpadding=2>
<thead>
<tr>
<th class="table-sortable:default">Id</th>
<th class="table-sortable:default">Task</th>
<th class="table-sortable:default">Created</th>
</tr>
</thead>
<?php
	require 'connect.php';
	$result=mysql_query("select seq,description,url,created from adminqueue order by created asc", $connection);
	$count=0;
	while($row= mysql_fetch_array($result))
	{
		$count=$count+1;
		print "\n<tr>";
		print "\n<td>{$row["seq"]}&nbsp;</td>";
		print "\n<td>{$row["description"]}&nbsp;</td>";
		print "\n<td>{$row["created"]}&nbsp;</td>";
		print "\n</tr>";
	}
	print "\n</table></div><p>";
	print "{$count} tasks";
	sif_footer();
?>
