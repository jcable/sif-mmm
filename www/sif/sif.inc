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
?>
