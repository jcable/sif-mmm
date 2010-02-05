function update_sinks(data){
	var g = $(".sif-current-source");
	g.each(function(i){
		var p = this.parentNode;
		var q=p.firstChild;
		var n = q.textContent;
		for(var i=0; i<data.length; i++)
		{
			if(data[i].output==n)
			{
				this.textContent = data[i].input;
			}
		}
	});
};

// find the selected tab
function selected_tab()
{
	var tabs = $("#tabs");
	var selected = tabs.tabs('option', 'selected');
	return $("#tabs li").get(selected);
}

// find the selected button in the selected tab of the requested panel
function selected_button(panel_id)
{
	var id = "#"+panel_id;
	var selected = $(id).tabs('option', 'selected');
	var bg = id+(selected+1); // selected is 0 based, tab_index is currently 1 based
	var sb = $(bg+" .ui-state-active");
	if(sb.length>0)
		return sb[0].firstChild.firstChild.textContent;
	else
		return "";
}

function prime_event(event, ui)
{
	  var me = event.target;
	  var x = me.id.replace("prime","take");
	  var l = me.nextSibling;
	  if($(l).hasClass("ui-state-active"))
	  {
		$("#"+x).button('enable');
		setTimeout(function(){ $("#"+x).button('disable'); $(l).removeClass("ui-state-active"); }, 10000);
	  }
	  else
	  {
		$("#"+x).button('disable');
	  }
	  return false;
}

function take_event(event, ui) {
	var me = event.target;
	var myid = me.id;
	var panel = myid.replace("take","");
	var s = selected_button(panel+"_source");
	var d = selected_button(panel+"_dest");
	$('#'+myid.replace("take","prime")).removeClass("ui-state-active");
	switch(panel)
	{
	case 'scs':
		$.post('crashservice.php', {'source':s,'service':d});
		break;
	case 'lcs':
		$.post('crashlistener.php', {'service':s,'listener':d});
		break;
	}
	$(me).button('disable');
	return false;
}

function initialise_monitor_crash_panel()
{

	$("#mcs_src_").tabs();
	$("#mcs_svc_").tabs();
	$("#mcs").button();

	$(".sif-button").each(function (i) {
	if(this.id.match(/^mcs_src/))
	{
		$(this).bind('click',
			function(){
				$('label[for*=mcs_svc]').each(function(){
					$(this).removeClass('ui-state-active');
				});
	});
	}
	if(this.id.match(/^mcs_svc/))
	{
		$(this).bind('click',
			function(){
				$('label[for*=mcs_src]').each(function(){
					$(this).removeClass('ui-state-active');
				});
			}
		);
	}
	});

	// initialise with a service enabled and no source
	$('label[for*=mcs_src]').each(function(){
		$(this).removeClass('ui-state-active');
	});

	$(".sif-monbutton").each(function (i) {
		$(this).button();
		$(this).bind('click', 
		function (event, ui) {
			var me = event.target;
			var myid = me.id;
			var s1 = selected_button("mcs_src_");
			var s2 = selected_button("mcs_svc_");
			//$(me).effect('bounce');
			var label = $('label[for='+myid+']');
			$.post('crashmon.php', {'source':s1,'service':s2,'mon':myid},
				function(data){
					label.removeClass('ui-state-active');
					if(myid.match(/Browser/))
					{
						show_player(s1, s2);
					}
			});
			return false;
		});
	});

}

function show_player(source, service)
{
	var player = $("#player");
	player[0].innerHTML = '<audio autoplay controls src="http://sif.dyndns.ws:8081/audio.ogg">Your browser doesn\'t play audio</audio>';
}

function routingpopup(event, ui)
{
	var me = event.target;
	var title = me.firstChild.textContent;
	var dlg = $('#routing');
	dlg.dialog('option', 'title', title);
	var dlge = dlg.get(0);
	$.getJSON("active.php", function(json){
		var text = "";
		var tabs = $("#tabs");
		var selected = tabs.tabs('option', 'selected');
		var tab = $("#tabs li").get(selected);
		var panel = "";
		if(tab.innerHTML.match(/service/i))
		{
			panel = "scs";
		}
		if(tab.innerHTML.match(/listener/i))
		{
			panel = "lcs";
		}
		if(title.match(/source/i))
		{
			var s = selected_button(panel+"_source");
			var dests = "";
			for(var i=0; i<json.length; i++)
			{
				if(json[i].input==s)
					dests += "<li>"+json[i].output+"</li>";
			}
			if(dests == "")
			{
				text += "Source "+s+" is currently not routed.";
			}
			else
			{
				text += "Source "+s+" is currently routed to:<ul>";
				text += "<ul>"+dests+"</ul>";
			}
		}
		if(title.match(/service/i))
		{
			var s = selected_button(panel+"_dest");
			var source = "?";
			for(var i=0; i<json.length; i++)
			{
				if(json[i].output==s)
					source = json[i].input;
			}
			text = "The source for "+s+" is "+source+"<p>";
			var dests = "";
			for(var i=0; i<json.length; i++)
			{
				if(json[i].input==s)
					dests += "<li>"+json[i].output+"</li>";
			}
			if(dests == "")
			{
				text += "Service "+s+" is currently not routed.";
			}
			else
			{
				text += "Service "+s+" is currently routed to:<ul>";
				text += "<ul>"+dests+"</ul>";
			}
		}
		if(title.match(/listener/i))
		{
			var s = selected_button(panel+"_dest");
			var source = "?";
			for(var i=0; i<json.length; i++)
			{
				if(json[i].output==s)
					source = json[i].input;
			}
			if(source!="OFF")
			{
				text = "The source for "+s+" is "+source;
				for(var i=0; i<json.length; i++)
				{
					if(json[i].output==source)
						ssource = json[i].input;
				}
				text += "<p>The source for "+source+" is "+ssource;
			}
			else
			{
				text = s+" is OFF";
			}
		}
		dlge.innerHTML = text;
		dlg.dialog('open');
	});
	return false;
}
