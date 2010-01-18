<?php
	require_once("sif.inc");
	require_once("header.php");
	$page = "Crash Listeners";
	sif_header($page, "crashswitch.css");
?>
<SCRIPT TYPE="text/javascript">
<!--
// toggle a button to simulate radio type buttons
function toggleButton(elementObj, idRegex) {
	var arraySpans = document.body.getElementsByTagName("td");

	for(var i = 0; i < arraySpans.length; i++)
	{
		if(arraySpans[i].id.match(idRegex))
		{
			arraySpans[i].className = 'raised';
		}
	}
	elementObj.className = 'depressed';
}
// set service variable
function setservice(service) {
	document.crashlistener.service.value = service;
}
// set listener variable
function setlistener(listener) {
	document.crashlistener.listener.value = listener;
}
// toggles hold button and variable
function togglehold(elementObj)
{

	if(document.crashlistener.hold.value ==0)
	 	{
			document.crashlistener.hold.value=1;
			elementObj.className='holddepressed';
		}
	else
		{
			document.crashlistener.hold.value=0;
			elementObj.className='raised';
		}
}
// toggles prime button and variable
function toggleprime(elementObj)
{

	if(document.crashlistener.prime.value ==0)
	 	{
			document.crashlistener.prime.value=1;
			elementObj.className='primedepressed';
			var arraySpans = document.body.getElementsByTagName("th");
			for(var i = 0; i < arraySpans.length; i++)
			{
				if(arraySpans[i].id.match('take'))
				{
					arraySpans[i].className = 'raised';
				}
			}
		}
	else
		{
			document.crashlistener.prime.value=0;
			elementObj.className='raised';
			var arraySpans = document.body.getElementsByTagName("th");
			for(var i = 0; i < arraySpans.length; i++)
			{
				if(arraySpans[i].id.match('take'))
				{
					arraySpans[i].className = 'unprimed';
				}
			}
		}
}
// submit form to do crash switch
function crashswitch()
{
	if(document.crashlistener.prime.value ==1)
	{
  		document.crashlistener.submit();
  	}
}
function servicepopup()
{

	if (document.crashlistener.service.value !="OFF")
	{
		servicepopurl='servicepopup.php?service='+document.crashlistener.service.value
		sourcewindow= window.open (servicepopurl, "servicepopup", "location=0,status=0,scrollbars=1,menubar=0,width=400,height=300");
		if (window.focus) {servicewindow.focus()}
	}
}

function reload_panel(sourcetab, desttab)
{
	location.href='listenercrashswitch.php?servicetab='+sourcetab+'&listenertab='+desttab;
}

//-->
</SCRIPT>
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
	$rows = active_events_as_run($dbh, 'LISTENER');
	$events = array();
	foreach($rows as $event)
	{
		$events[$event["output"]] = $event;
	}
?>
<form method="post" action="crashlistener.php" name="crashlistener">
<input type="hidden" name="service" value="OFF">
<input type="hidden" name="listener" value="NULL">
<input type="hidden" name="hold" value="0">
<input type="hidden" name="prime" value="0">
<input type="hidden" name="listenertab" value="<?php print $listenertab;?>">
<input type="hidden" name="servicetab" value="<?php print $servicetab;?>">
<div id="sourcebuttons"><?php showservicebuttons($dbh, "source", $servicetab, $listenertab); ?></div>
<div id="destbuttons"><?php showselectionpanel($dbh, "dest", $listenertab, $servicetab, "LISTENER", $events); ?></div>
<div id="takebuttons"><?php takebuttons("service", "listener");?></div>
</form>
<?php sif_footer(); ?>
