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
		<script type="text/javascript" language="javascript">
		$(document).ready(function() {
			$("#tabs").tabs();
			$("#scs_source").tabs();
			$("#scs_dest").tabs();
			$("#lcs_source").tabs();
			$("#lcs_dest").tabs();
			$("#mcs_sourcebuttons").tabs();
			$("#mcs_servicebuttons").tabs();
			$(".sif-buttonset").buttonset();
			$(".sif-sabutton").button();
			$('.sif-prime').each(function (i) {
				$(this).button();
				$(this).bind('click', function(event, ui) {
				  var me = event.target;
				  var x = me.id.replace("prime","take");
				  if(me.checked)
				  {
					$("#"+x).button('enable');
				  }
				  else
				  {
					$("#"+x).button('disable');
				  }
				});
			});
			$(".sif-take").button();
			$(".sif-take").button('disable');
			$(".sif-take").bind('click', take_event);
			$(".sif-hold").button();
			});

			setInterval ( 
				function()
				{
					$.getJSON("active.php?kind=SERVICE",update_sinks);
					$.getJSON("active.php?kind=LISTENER",update_sinks);
				}
			,2000 );
		</script>
</head>
<body>
<div id="tabs">
    <ul>
        <li><a href="#intro"><span>Introduction</span></a></li>
        <li><a href="maintenance.html"><span>Maintenance</span></a></li>
        <li><a href="showserviceschedule.php"><span>Service Schedules</span></a></li>
        <li><a href="showlistenerschedule.php"><span>Listener Schedules</span></a></li>
        <li><a href="#showmaterial"><span>Material Info</span></a></li>
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
	SIF was a Norwegian Goddess, some might say we have named the project after her as it stands for 'Spend Innovation Funds' but we couldn't possibly comment!
	<p/>
	<img src="sif.jpg" alt="" border=2>
	<p/>
	<a href="http://code.google.com/p/sif-mmm/" target="_new">Project Documentation (Google Code Page)</a>
	</div>
	<div id="showmaterial"></div>
	<div id="servicecrashswitch">
		<?php 
			panel_tab($dbh, "scs_source", "source", true, false);
			panel_tab($dbh, "scs_dest", "service", false);
			takebuttons("service", "listener", "scs")
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
			panel_tab($dbh, "mcs", "source", true, false);
			panel_tab($dbh, "mcs", "service", false);
		?>
	</div>
</div>
<hr>
<div id='footer'>&copy; 2008-2010, British Broadcasting Corporation</div>
</body>
</html>