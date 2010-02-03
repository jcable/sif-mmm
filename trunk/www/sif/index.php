<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>SIF Control Panel</title>
		<?php
			require_once("sif.inc");
			$dbh = connect(); 
		?>
		<link rel="stylesheet" type="text/css" href="sif.css" />
		<link rel="stylesheet" type="text/css" href="css/start/jquery-ui-1.8rc1.custom.css" />
		<script type="text/javascript" language="javascript" src="js/jquery-1.4.1.min.js"></script>
		<script type="text/javascript" language="javascript" src="js/jquery-ui-1.8rc1.custom.min.js"></script>
		<script type="text/javascript" language="javascript" src="sif.js"></script>
		<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
		<script type="text/javascript" language="javascript">
		$(document).ready(function() {
			$("#tabs").tabs();
			$("#scs_source").tabs();
			$("#scs_dest").tabs();
			$("#lcs_source").tabs();
			$("#lcs_dest").tabs();

			$(".sif-buttonset").buttonset();
			$('.sif-prime').each(function (i) {
				$(this).button();
				$(this).bind('click', prime_event);
			});
			$(".sif-take").button();
			$(".sif-take").button('disable');
			$(".sif-take").bind('click', take_event);
			$(".sif-hold").button();

			$(".sif-routing").button();
			$(".sif-routing").bind('click',routingpopup);

			initialise_monitor_crash_panel();

			setInterval ( 
				function()
				{
					$.getJSON("active.php",update_sinks);
				}
			,2000 );

			$("#routing").dialog({ autoOpen: false });
			$("#service_active_schedule").dataTable({
				"bServerSide": true,
				"sAjaxSource": "serviceschedule.php",
				"bJQueryUI": true,
				"bAutoWidth": false
				});
			$("#listener_active_schedule").dataTable({
				"bServerSide": true,
				"sAjaxSource": "listenerschedule.php",
				"bJQueryUI": true,
				"bAutoWidth": false
				});
			$("#material").dataTable({
				"bServerSide": true,
				"sAjaxSource": "material.php",
				"bJQueryUI": true,
				"bAutoWidth": false
				});
			//$("#service_active_schedule").dataTable({"bJQueryUI": true});
			//$("#listener_active_schedule").dataTable({"bJQueryUI": true});
		});
		</script>
</head>
<body>
<div id="tabs">
    <ul>
        <li><a href="#intro"><span>Introduction</span></a></li>
        <li><a href="maintenance.html"><span>Maintenance</span></a></li>
        <li><a href="#serviceschedule"><span>Service Schedules</span></a></li>
        <li><a href="#listenerschedule"><span>Listener Schedules</span></a></li>
        <li><a href="#materialinfo"><span>Material Info</span></a></li>
        <li><a href="#servicecrashswitch"><span>Crash Services</span></a></li>
        <li><a href="#listenercrashswitch"><span>Crash Listeners</span></a></li>
        <li><a href="#monitor"><span>Monitoring</span></a></li>
        <li><a href="redundancy.php"><span>Redundancy</span></a></li>
    </ul>
	<div id="intro">
	Please use the buttons at the top to access the various parts of the SIF control system.
	<p/>
	The SIF project is an innovation project being run by BBC World Service Transmission &amp; Distribution to investigate an entirely IP based routeing
	system for audio and video. Given the realisation that the final output from our current systems is an IP stream, why not try to encode to IP at the
	source and do away with linear routers and coding and multiplex systems? SIF is an experiment towards this goal.
	<p/>
	The SIF project is being worked on by <a href="mailto:julian.cable@bbc.co.uk">Julian Cable</a>,
	<a href="mailto:jonathan.robertshaw@bbc.co.uk">Jonathan Robertshaw</a>,
	<a href="mailto:mark.patrick@bbc.co.uk">Mark Patrick</a> and
	<a href="mailto:hild.myklebust@bbc.co.uk">Hild Myklebust</a>.	
	<p/>
	SIF was the wife of the Norse god Thor
	<p/>
	<img src="sif.jpg" alt="" border=2>
	<p/>
	<a href="http://code.google.com/p/sif-mmm/" target="_new">Project Documentation (Google Code Page)</a>
	</div>
	<div id="serviceschedule">
	<?php markup_jquery_datatable($dbh, "service_active_schedule", "service_active_schedule", true); ?>
	</div>
	<div id="listenerschedule">
	<?php markup_jquery_datatable($dbh, "listener_active_schedule", "listener_active_schedule", true); ?>
	</div>
	<div id="materialinfo">
	<?php markup_jquery_datatable($dbh, "material", "material", true); ?>
	</div>
	<div id="servicecrashswitch">
		<?php 
			panel_tab($dbh, "scs_source", "source", true, false);
			panel_tab($dbh, "scs_dest", "service", false);
			takebuttons("source", "service", "scs")
		?>
	</div>
	<div id="listenercrashswitch">
		<?php 
			panel_tab($dbh, "lcs_source", "service", true);
			panel_tab($dbh, "lcs_dest", "listener", false);
			takebuttons("service", "listener", "lcs")
		?>
	</div>
	<div id="monitor">
		<?php
			panel_tab($dbh, "mcs_src_", "source", true, false);
			panel_tab($dbh, "mcs_svc_", "service", true, false);
			monitorbuttons($dbh, "mcs");
		?>
	</div>
</div>
<hr>
<div id='footer'>&copy; 2008-2010, British Broadcasting Corporation</div>
<div id='routing'></div>
</body>
</html>
