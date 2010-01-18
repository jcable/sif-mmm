<?php
	require_once("header.php");
	require_once("sif.inc");
	$page = "Crash Services";
	sif_header($page, "crashswitch.css");
	$dbh = connect();
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
// set source variable
function setsource(source) {
	document.crashservice.source.value = source;
}
// set service variable
function setservice(service,currentsource) {
	document.crashservice.service.value = service;
	document.crashservice.previous_source.value = currentsource;
}
// toggles hold button and variable
function togglehold(elementObj)
{

	if(document.crashservice.hold.value ==0)
	 	{
			document.crashservice.hold.value=1;
			elementObj.className='holddepressed';
		}
	else
		{
			document.crashservice.hold.value=0;
			elementObj.className='raised';
		}
}
// toggles prime button and variable
function toggleprime(elementObj)
{

	if(document.crashservice.prime.value ==0)
	 	{
			document.crashservice.prime.value=1;
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
			document.crashservice.prime.value=0;
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
	if(document.crashservice.prime.value ==1)
	{
  		document.crashservice.submit();
  	}
}
function sourcepopup()
{
	sourcepopurl='sourcepopup.php?source='+document.crashservice.source.value
	sourcewindow= window.open (sourcepopurl, "sourcepopup", "location=0,status=0,scrollbars=1,menubar=0,width=400,height=300");
	if (window.focus) {sourcewindow.focus()}
}
function servicepopup()
{
	if (document.crashservice.service.value !="NULL")
	{
		servicepopurl='servicepopup.php?service='+document.crashservice.service.value
		sourcewindow= window.open (servicepopurl, "servicepopup", "location=0,status=0,scrollbars=1,menubar=0,width=400,height=300");
		if (window.focus) {servicewindow.focus()}
	}
}

function reload_panel(sourcetab, desttab)
{
	location.href='servicecrashswitch.php?servicetab='+desttab+'&sourcetab='+sourcetab;
}

//-->
</SCRIPT>
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
<form method="post" action="crashservice.php" name="crashservice">
<input type="hidden" name="previous_source" value="OFF">
<input type="hidden" name="source" value="OFF">
<input type="hidden" name="service" value="NULL">
<input type="hidden" name="hold" value="0">
<input type="hidden" name="prime" value="0">
<div id="sourcebuttons"><?php showsourcebuttons($dbh, "source", $sourcetab, $servicetab); ?></div>
<div id="destbuttons"><?php showservicebuttons($dbh, "dest", $servicetab, $sourcetab); ?></div>
<div id="takebuttons">
<table width=100%>
<tr>
<td id="sourceOFF" align=center height=40 width=10% class="depressed" onclick="toggleButton(this, /source/i);setsource('OFF');"><b>OFF</b></td>
<th width=10%>&nbsp;</th>
<th class="raised" id="holdbutton" height=40 width=10% onclick="toggleprime(this);">Prime</th>
<th class="unprimed" id="take" height=40 width=10% onclick="crashswitch();">Take</th>
<th class="raised" id="holdbutton" height=40 width=10% onclick="togglehold(this);">Hold</th>
<th width=20%>&nbsp;</th>
<th class="raised" height=40 width=10% onClick="sourcepopup();">Source Routing</th>
<th class="raised" height=40 width=10% onClick="servicepopup();">Service Routing</th>
<th class="raised" height=40 width=10% onClick="history.go();">Refresh</th>
</tr></table>
</form>
</div>
<?php
	sif_footer();
?>
