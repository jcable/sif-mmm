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

// find the selected button in the selected tab of the requested panel
function selected_button(panel_id)
{
	var id = "#"+panel_id;
	var selected = $(id).tabs('option', 'selected');
	var bg = id+(selected+1); // selected is 0 based, tab_index is currently 1 based
	var sb = $(bg+" .ui-state-active");
	return sb[0].firstChild.firstChild.textContent;
}

function take_event(event, ui) {
	var me = event.target;
	var myid = me.id;
	var panel = myid.replace("take","");
	var s = selected_button(panel+"_source");
	var d = selected_button(panel+"_dest");
	switch(panel)
	{
	case 'scs':
		$.post('crashservice.php', {'source':s,'service':d});
		break;
	case 'lcs':
		$.post('crashlistener.php', {'service':s,'listener':d});
		break;
	}
}