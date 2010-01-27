<?php
	require_once("header.php");
	require_once("sif.inc");
	$page = "Crash Services";
	sif_header($page, "crashswitch.css");
	$dbh = connect();
?>
<SCRIPT TYPE="text/javascript">
<!--
function reload_panel(sourcetab, desttab)
{
	location.href='servicecrashswitch.php?servicetab='+desttab+'&sourcetab='+sourcetab;
}
//-->
</SCRIPT>
<SCRIPT type="text/javascript" src="crashswitch.js"></SCRIPT>
<?php
	sif_buttons($page);
	if (isset($_REQUEST["sourcetab"]))
		$sourcetab=$_REQUEST["sourcetab"];
	else
		$sourcetab=1;
	
	if(isset($_REQUEST["servicetab"]))
		$servicetab=$_REQUEST["servicetab"];
	else
		$servicetab=1;
?>
<form method="post" action="crashservice.php" name="crashpanel">
<input type="hidden" name="previous_source" value="OFF">
<input type="hidden" name="source" value="OFF">
<input type="hidden" name="service" value="NULL">
<input type="hidden" name="hold" value="0">
<input type="hidden" name="sourcetab" value="<?php print $sourcetab;?>">
<input type="hidden" name="servicetab" value="<?php print $servicetab;?>">
<div id="sourcebuttons"><?php showselectionpanel($dbh, "source", $sourcetab, $servicetab, 'SOURCE', 's'); ?></div>
<div id="destbuttons"><?php showselectionpanel($dbh, 'dest', $servicetab, $sourcetab, 'SERVICE', 'v', active($dbh, 'SERVICE')); ?></div>
<div id="takebuttons"><?php takebuttons('source', 'service', 's');?></div>
</form>
<?php
	sif_footer();
?>
