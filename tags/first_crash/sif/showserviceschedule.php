<?php
	require_once("header.php");
	$page = "Service Schedules";
	sif_header($page, "main.css");
	sif_buttons($page);
?>
<p><form name="form1" onSubmit="search(document.form1, frametosearch); return false"><input type="text" name="findthis" size="15" title="Press 'ALT s' after clicking find button to repeatedly search page"> <input type="submit" value="Find in Page" ACCESSKEY="s"></form>
<p>
<div id="plaincontent">
<table class="example table-autosort:0 table-stripeclass:alternate" border=1 cellspacing=0 cellpadding=2>
<thead>
<tr>
<th class="table-sortable:default">Event ID:</th>
<th class="table-sortable:default">Service:</th>
<th class="table-sortable:default">Source:</th>
<th class="table-sortable:default">First Date:</th>
<th class="table-sortable:default">Last Date:</th>
<th class="table-sortable:default">Days:</th>
<th class="table-sortable:default">Start Time:</th>
<th class="table-sortable:default">Duration:</th>
<th class="table-sortable:default">Start Mode:</th>
<th class="table-sortable:default">Name:</th>
<th class="table-sortable:default">Material ID:</th>
<th class="table-sortable:default">ROT:</th>
<th class="table-sortable:default">PTT:</th>
<th class="table-sortable:default">PTT Time:</th>
<th class="table-sortable:default">Owner:</th>
<th>&nbsp;</th>
</tr>
<tr>
<th><input name="filter" size="6" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="6" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="6" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="6" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="6" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="6" onkeyup="Table.filter(this,this)"></th>
<th><input name="filter" size="6" onkeyup="Table.filter(this,this)"></th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
</tr>
</thead>
<?php
require 'connect.php';
$result=mysql_query("select * from service_active_schedule order by service_event_id asc", $connection);
$count=0;
while($row= mysql_fetch_array($result))
{
	$count=$count+1;
	print "\n<tr>";
	print "\n<td>".str_pad($row["service_event_id"],10,"0",STR_PAD_LEFT)."&nbsp;</td>";
	print "\n<td>{$row["service"]}&nbsp;</td>";
	print "\n<td>{$row["source"]}&nbsp;</td>";
	print "\n<td>{$row["first_date"]}&nbsp;</td>";
	print "\n<td>{$row["last_date"]}&nbsp;</td>";
	print "\n<td>{$row["days"]}&nbsp;</td>";
	print "\n<td>{$row["start_time"]}&nbsp;</td>";
	print "\n<td>{$row["duration"]}&nbsp;</td>";
	print "\n<td>{$row["start_mode"]}&nbsp;</td>";
	print "\n<td>{$row["name"]}&nbsp;</td>";
	print "\n<td>{$row["material_id"]}&nbsp;</td>";
	print "\n<td>{$row["rot"]}&nbsp;</td>";
	print "\n<td>{$row["ptt"]}&nbsp;</td>";
	print "\n<td>{$row["ptt_time"]}&nbsp;</td>";
	print "\n<td>{$row["owner"]}&nbsp;</td>";
	print "\n<form method=\"post\" action=\"deleteserviceline.php\">";
	print "\n<input type=\"hidden\" name=\"eventid\" value=\"{$row["service_event_id"]}\">";
	print "\n<td><input type=\"Submit\" value=\"Delete\" onClick=\"return confirmSubmit()\"></td>";
	print "\n</form>";
	print "\n</tr>";
}
print "\n</table></div>";
print "\n<p>{$count} service event entries in database";
?>
<?php sif_footer(); ?>
