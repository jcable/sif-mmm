<?php
	require_once("sif.inc");
	require_once("header.php");
	$page = "Monitoring";
	sif_header($page, "crashswitch.css");
?>
<SCRIPT type="text/javascript" src="crashswitch.js"></SCRIPT>
<SCRIPT TYPE="text/javascript">
<!--

// submit form to do crash monitor
function crashswitchmon(mondest)
{
	// check if a service or a source
	var v = document.getElementsByTagName("TD");
	for(var i=0; i<v.length; i++)
	{
		if(v.item(i).className=="depressed button")
		{
			var id = v.item(i).id;
			if (id.match(/source/)) {
				document.crashpanel.service.value="";
			}
			if (id.match(/service/)) {
				document.crashpanel.source.value="";
			}
			if (id.match(/OFF/)) {
				document.crashpanel.service.value="OFF";
				document.crashpanel.source.value="OFF";
			}
		}
	}
	document.crashpanel.mon.value = mondest;
	document.crashpanel.submit();
}

function reload_panel(sourcetab, desttab)
{
	location.href='monitor.php?servicetab='+desttab+'&sourcetab='+sourcetab;
}

//-->
</SCRIPT>
<?php
	sif_buttons($page);
	if (isset($_REQUEST["sourcetab"]))
	{
		$sourcetab=$_REQUEST["sourcetab"];
	}
	else
	{
		$sourcetab=1;
	}
	if (isset($_REQUEST["servicetab"]))
	{
		$servicetab=$_REQUEST["servicetab"];
	}
	else
	{
		$servicetab=1;
	}
	$dbh = connect();
?>
<form method="post" action="crashmon.php" name="crashpanel">
<input type="hidden" name="sourcetab" value="<?php print $sourcetab; ?>">
<input type="hidden" name="servicetab" value="<?php print $servicetab; ?>">
<input type="hidden" name="source" value="OFF">
<input type="hidden" name="service" value="OFF">
<input type="hidden" name="previous_source" value="OFF">
<input type="hidden" name="previous_service" value="OFF">
<input type="hidden" name="mon" value="NULL">
</form>
<div id="sourcebuttons"><?php showselectionpanel($dbh, 'source', $sourcetab, $servicetab, 'SOURCE', 's'); ?></div>
<div id="servicebuttons"><?php showselectionpanel($dbh, 'source', $servicetab, $sourcetab, 'SERVICE', 's', active($dbh, 'SERVICE')); ?></div>
<div id="takebuttons">
<table width=100%>
<tr>
<td id="s_OFF" class="depressed button" onclick="click_button(this, /^s_/i, 'source', 'OFF');"><b>OFF</b></td><td colspan=2>&nbsp;</td>
<td height=40 width=10% height=60>&nbsp;</td>
<?php
	$moncount=0;
	$stmt=$dbh->prepare("SELECT * FROM edge JOIN panel_tabs USING(kind,tab_index) WHERE kind='LISTENER' and hidden=1 ORDER BY id asc");
	$stmt->execute();
	while($row= $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$id=$row["id"];
		$source="";
		print "<td id=\"mon{$moncount}\" class=\"raised button\" onclick=\"crashswitchmon('{$row["id"]}');\">";
		print "<span class=\"buttonlabel\">$id</span>";
		if($source!="")
		{
			print "<br>";
			print "<span class=\"buttondetail\">(".$source.")</span>";
		}
		print "</td>\n";
		$moncount++;
		if ($moncount % 10 == 0)
		{
			print "</tr><tr>";
		}
	}
	$emptyslotsinrow=(7-($moncount % 10));
	// this will pad out any remaining slots so the table formats correctly
	if ($emptyslotsinrow < 7)
	{
		while($emptyslotsinrow > 0)
		{
			print "<td height=40 width=10% height=60>&nbsp;</td>";
			$emptyslotsinrow--;
		}
	}
?>
<th class="raised" width=10% onClick="history.go();">Refresh</th>
</tr></table>
  <div style='height: 100%; width: 100%; text-align: center;'>
  <object type="application/x-shockwave-flash" data="http://flowplayer.sourceforge.net/video/FlowPlayer.swf" width="800px" height="600px" id="FlowPlayer" style="z-index: 0">
	  <param name="allowScriptAccess" value="sameDomain" />
	  <param name="movie" value="http://flowplayer.sourceforge.net/video/FlowPlayer.swf" />
	  <param name="quality" value="high" />
    <!--	  <param name="scale" value="noScale" />-->
	  <param name="wmode" value="transparent" />
	<!--  <param name="flashvars" value="config={ loop: false, initialScale: \'fit\', autoPlay: false, configInject: true}" />-->
  <script type="text/javascript">
// <![CDATA[
  var host = document.location.toString().replace( /http:\/\//, '' ).replace( /[:/].*/, '' );
  document.write( '' +
'	  <param name="flashvars" value="config={ loop: false, initialScale: \'fit\', autoPlay: false, playList: [{ url: \'http://' + host + ':8081/stream.flv\', controlEnabled: true}] }" />' );
// ]]>
</script>
  </object>
  <p style="font-size: small;">Uses the <a href="http://flowplayer.sourceforge.net/">Flow Player</a> free flash video player for playback (client side).</p>
  </div>
</div>
<?php
	sif_footer();
?>
