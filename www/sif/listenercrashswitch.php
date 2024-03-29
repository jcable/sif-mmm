<?php
	require_once("sif.inc");
	require_once("header.php");
	$page = "Crash Listeners";
	sif_header($page, "crashswitch.css");
?>
<SCRIPT TYPE="text/javascript">
<!--
function reload_panel(sourcetab, desttab)
{
	location.href='listenercrashswitch.php?servicetab='+sourcetab+'&listenertab='+desttab;
}
//-->
</SCRIPT>
<SCRIPT type="text/javascript" src="crashswitch.js"></SCRIPT>
<?php
	sif_buttons($page);
	if (isset($_REQUEST["servicetab"]) && isset($_REQUEST["listenertab"]))
	{
		$servicetab=$_REQUEST["servicetab"];
		$listenertab=$_REQUEST["listenertab"];
	}
	else
	{
		$servicetab=1;
		$listenertab=1;
	}
	$dbh = connect();
?>
<form method="post" action="crashlistener.php" name="crashpanel">
<input type="hidden" name="previous_service" value="OFF">
<input type="hidden" name="service" value="OFF">
<input type="hidden" name="listener" value="NULL">
<input type="hidden" name="hold" value="0">
<input type="hidden" name="listenertab" value="<?php print $listenertab;?>">
<input type="hidden" name="servicetab" value="<?php print $servicetab;?>">
<div id="sourcebuttons"><?php showselectionpanel($dbh, 'source', $servicetab, $listenertab, 'SERVICE', 'v', active($dbh, 'SERVICE')); ?></div>
<div id="destbuttons"><?php showselectionpanel($dbh, 'dest', $listenertab, $servicetab, 'LISTENER', 'l', active($dbh, 'LISTENER')); ?></div>
<div id="takebuttons"><?php takebuttons('service', 'listener', 'v');?></div>
</form>
<?php sif_footer(); ?>
