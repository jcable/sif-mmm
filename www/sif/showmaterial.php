<?php
	require_once("header.php");
	$page = "Material Info";
	sif_header($page, "main.css");
	sif_buttons($page);
?>
<script type="text/javascript" src="table.js"></script>
<script type="text/javascript" src="findonpage.js"></script>
<p><form name="form1" onSubmit="search(document.form1, frametosearch); return false"><input type="text" name="findthis" size="15" title="Press 'ALT s' after clicking find button to repeatedly search page"> <input type="submit" value="Find in Page" ACCESSKEY="s"></form>
&nbsp;
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
document.write('<form><input type=button value="Refresh" onClick="history.go()"></form>')
//  End -->
</script>
<p>
<div id="plaincontent">
<table class="example table-autosort:0 table-stripeclass:alternate" border=1 cellspacing=0 cellpadding=2>
<thead>
<tr>
<th class="table-sortable:default">Material ID:</th>
<th class="table-sortable:default">Title:</th>
<th class="table-sortable:default">Duration:</th>
<th class="table-sortable:default">Delete After:</th>
<th class="table-sortable:default">File:</th>
<th class="table-sortable:default">Type:</th>
<th class="table-sortable:default">Owner:</th>
<th class="table-sortable:default">Client Ref:</th>
<th class="table-sortable:default">TX Date:</th>
<th>&nbsp;</th>
</tr>
<tr>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
<th>&nbsp;</th>
</tr>
</thead>
<?php
require 'connect.php';
$result=mysql_query("select * from material order by material_id asc", $connection);
$count=0;
while($row= mysql_fetch_array($result))
{
	$count=$count+1;
	print "\n<tr>";
	print "\n<td>{$row["material_id"]}&nbsp;</td>";
	print "\n<td>{$row["title"]}&nbsp;</td>";
	print "\n<td>{$row["duration"]}&nbsp;</td>";
	print "\n<td>{$row["delete_after"]}&nbsp;</td>";
	print "\n<td>{$row["file"]}&nbsp;</td>";
	print "\n<td>{$row["material_type"]}&nbsp;</td>";
	print "\n<td>{$row["owner"]}&nbsp;</td>";
	print "\n<td>{$row["client_ref"]}&nbsp;</td>";
	print "\n<td>{$row["tx_date"]}&nbsp;</td>";

	print "\n<form method=\"post\" action=\"deletematerial.php\">";
	print "\n<input type=\"hidden\" name=\"matid\" value=\"{$row["material_id"]}\">";
	print "\n<td><input type=\"Submit\" value=\"Delete\" onClick=\"return confirmSubmit()\"></td>";
	print "\n</form>";
	print "\n</tr>";
}
print "\n</table></div><p>";
print "{$count} material entries in database";
?>
<hr>
<div id="footer">
&copy; 2009, Mark Patrick, BBC WS
</div>
</html>
