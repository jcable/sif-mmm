<?php

function register_event_as_run($dbh, $device, $input, $output, $eventtype)
{
        $stmt = $dbh->prepare("INSERT INTO as_run (device, input, output, event_type) VALUES(?,?,?,?)");
        $r = $stmt->execute(array($device, $input, $output, $eventtype));
        if($r)
                return true;
        else
                return $stmt->errorInfo();
}

function connect()
{
	return new PDO( 'mysql:host=localhost;dbname=sif', 'sif', 'sif',
    		array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
	); 
}

function panel_tab($dbh, $panel, $kind, $has_off, $has_source=true)
{
	$idpref=$panel;
	$stmt = $dbh->prepare("SELECT tab_index,tab_text FROM panel_tabs WHERE kind=? AND hidden=0 ORDER BY tab_index asc");
	$stmt->execute(array(strtoupper($kind)));
	$tabs = $stmt->fetchAll(PDO::FETCH_ASSOC);
	print "<br/><div style='text-align:center;background-color:#CCCCFF;' class='panelhead'>".ucwords($kind)."s:</div>";
	print "<div id=\"{$idpref}\"><ul>\n";
	foreach($tabs as $tab)
	{
		print "<li><a href=\"#{$idpref}{$tab['tab_index']}\"><span>{$tab['tab_text']}</span></a></li>\n";
	}
	print "</ul>\n";
	foreach($tabs as $tab)
	{
		$bg = $idpref.$tab['tab_index'];
		$stmt = $dbh->prepare("SELECT id FROM edge WHERE kind=? AND tab_index=? ORDER BY id ASC");
		$stmt->execute(array(strtoupper($kind), $tab['tab_index']));

		print "<div id='{$bg}' class='sif-buttonset'>";
		$i=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$bid = $bg.($i++);
			print "<input class=\"sif-button\" type=\"radio\" name=\"{$bg}\" id=\"{$bid}\"";
			if($has_off == false && $i==1)
			{
				print " checked=\"checked\"";
			}
			print "/>";
			print "<label for=\"{$bid}\">";
			print "{$row['id']}";
			if($has_source)
				print "<span><br/>(</span><span class=\"sif-current-source\">?</span>)";
			print "</label>\n";
		}
		if($has_off)
		{
			$bid = $bg.($i++);
			print "<input class=\"sif-button\" type=\"radio\" name=\"{$bg}\" id=\"{$bid}\" checked=\"checked\"/>";
			print "<label for=\"{$bid}\">OFF";
			if($has_source)
				print "<br/>&nbsp;";
			print "</label>\n";
		}
		print "</div>\n";
	}
	print "</div>\n";
}


function takebuttons($source, $dest, $tbid)
{
?>
<br/>
<div width="100%">
<span id="<?php print $tbid;?>" class="ui-widget-header ui-corner-all sif-toolbar">
	<input type="checkbox" id="<?php print $tbid;?>prime" class="sif-prime"/><label for="<?php print $tbid;?>prime">Prime</label>	
	<input type="submit" id="<?php print $tbid;?>take" class="sif-take" value="Take"/>
	<button class="sif-hold">Hold</button>	
</span>
<span class="ui-widget-header ui-corner-all sif-toolbar sif-button">
	<button class="sif-routing"><?php print ucwords($source);?> Routing</button>	
	<button class="sif-routing"><?php print ucwords($dest);?> Routing</button>	
</span>
</div>
<?php
}

function monitorbuttons($dbh, $bid)
{
	print "<br/>\n<div width=\"100%\">\n";
	$stmt=$dbh->prepare("SELECT id FROM edge JOIN panel_tabs USING(kind,tab_index) WHERE kind='LISTENER' and hidden=1 ORDER BY id asc");
        $stmt->execute();
        while($row= $stmt->fetch(PDO::FETCH_ASSOC))
        {
		print '<input id="'.$row["id"].'" class="sif-monbutton" type="radio"/><label for="'.$row["id"].'">';
		print $row["id"];
		print "<span><br/>(</span><span class=\"sif-current-source\">?</span>)";
		print '</label>';
        }
	print "</div>\n";
}

function markup_jquery_datatable($dbh, $id, $table, $server_side=false)
{
	if($server_side)
	{
		$stmt = $dbh->query("SELECT * FROM $table LIMIT 1");
	}
	else
	{
		$stmt = $dbh->query("SELECT * FROM $table");
	}
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	print "<table id=\"$id\"><thead><tr>\n";
	foreach(array_keys($row) as $field) print "<th>".ucwords(str_replace("_"," ",strtolower($field)))."</th>";
	print "</tr></thead><tbody><tr>\n";
	if($server_side)
	{
		print "<td colspan=\"".count($row)."\" class=\"dataTables_empty\">Loading data from server</td>\n";
	}
	else
	{
		print "<tr>";
		foreach($row as $val)
		{
			print "<td>$val</td>";
		}
		print "</tr>\n";
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			print "<tr>";
			foreach($row as $val)
			{
				print "<td>$val</td>";
			}
			print "</tr>\n";
		}
	}
	print "</tr></tbody><tfoot></tfoot></table>\n";
}

/*
In:
    * int: iDisplayStart - Display start point
    * int: iDisplayLength - Number of records to display
    * string: string: sSearch - Global search field
    * boolean: bEscapeRegex - Global search is regex or not
    * int: iColumns - Number of columns being displayed (useful for getting individual column search info)
    * string: sSortable_(int) - Indicator for if a column is flagged as sortable or not on the client-side
    * string: sSearchable_(int) - Indicator for if a column is flagged as searchable or not on the client-side
    * string: sSearch_(int) - Individual column filter
    * boolean: bEscapeRegex_(int) - Individual column filter is regex or not
    * int: iSortingCols - Number of columns to sort on
    * int: iSortCol_(int) - Column being sorted on (you will need to decode this number for your database)
    * string: sSortDir_(int) - Direction to be sorted - "desc" or "asc". Note that the prefix for this variable is wrong in 1.5.x, but left for backward compatibility)
    * string: sEcho - Information for DataTables to use for rendering

Out:
    * int: iTotalRecords - Total records, before filtering (i.e. the number of records in the database)
    * int: iTotalDisplayRecords - Total records, after filtering (i.e. the number of records after filtering has been applied)
    * string: sEcho - An unaltered copy of sEcho sent from the client side. Note that it strongly recommended for security reasons that you 'cast' this parameter to an integer in order to prevent Cross Site Scripting (XSS) attacks
    * string: sColumns - Optional - this is a string of column names, comma separated (used in combination with sName) which will allow DataTables to reorder data on the client-side if required for display
    * array array mixed: aaData - The data in a 2D array

*/
function serve_jquery_datatable($dbh, $table, $_REQUEST)
{
	$stmt = $dbh->query("select count(*) as iTotalRecords from $table");
	$n = $stmt->fetch(PDO::FETCH_ASSOC);
	$from = $_REQUEST["iDisplayStart"];
	$num = $_REQUEST["iDisplayLength"];
	$stmt = $dbh->prepare("select * from $table LIMIT 1");
	$stmt->execute();
	$fields = array_keys($stmt->fetch(PDO::FETCH_ASSOC));
	$sort = array();
	for($i=0; $i<$_REQUEST["iSortingCols"]; $i++)
	{
		$col = intval($_REQUEST["iSortCol_".$i]);
		$dir = $_REQUEST["sSortDir_".$i];
		$sort[] = $fields[$col]." ".$dir;
	}
	if(count($sort)>0)
	{
		$sortspec = "ORDER BY ".implode(", ", $sort);
	}
	$fieldspec = array();
	foreach($fields as $field)
	{
		$fieldspec[] = "IFNULL($field,'')";
	}
	$sql ="SELECT ".implode(", ",$fieldspec)." FROM $table $sortspec LIMIT $from, $num";
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	$r = array();
	$r["query"] = $sql;
	$r["aaData"] = $stmt->fetchAll(PDO::FETCH_NUM);
	$r["iTotalDisplayRecords"] = count($r["aaData"]);
	$r["iTotalRecords"] = $n["iTotalRecords"];
	$r["sEcho"] = intval($_REQUEST["sEcho"]);
	return json_encode($r);
}
?>
