// toggle a button to simulate radio type buttons
function toggleButton(elementObj, idRegex) {
	var arraySpans = document.body.getElementsByTagName("td");

	for(var i = 0; i < arraySpans.length; i++)
	{
		if(arraySpans[i].id.match(idRegex))
		{
			arraySpans[i].className = 'raised button';
		}
	}
	elementObj.className = 'depressed button';
}

// toggles prime button and variable
function toggleprime(elementObj)
{
	var takestate='unprimed';
	if(elementObj.className='raised button')
	{
		elementObj.className='primedepressed button';
		var arraySpans = document.body.getElementsByTagName("th");
		takestate='raised';
	}
	else
	{
		elementObj.className='raised';
	}
	var arraySpans = document.body.getElementsByTagName("th");
	for(var i = 0; i < arraySpans.length; i++)
	{
		if(arraySpans[i].id.match('take'))
		{
			arraySpans[i].className = takestate+" button";
		}
	}
}
// submit form to do crash switch
function crashswitch()
{
	if(document.getElementById("primebutton").className=='primedepressed button')
	{
  		document.crashpanel.submit();
  	}
}

function click_button(elementObj, re, field, newvalue)
{
	toggleButton(elementObj, re);
	var f = document.crashpanel[field];
	var p = document.crashpanel['previous_'+field];
	if(p!=undefined)
		p.value = f.value;
	f.value = newvalue;
}

function sourcepopup()
{
	sourcepopurl='sourcepopup.php?source='+document.crashpanel.source.value
	sourcewindow=window.open (sourcepopurl, "sourcepopup", "location=0,status=0,scrollbars=1,menubar=0,width=400,height=300");
	if (window.focus) {sourcewindow.focus()}
}

function servicepopup()
{
	var value = document.crashpanel.service.value;
	if ((value != "NULL") && (value != "OFF"))
	{
		servicepopurl='servicepopup.php?service='+value;
		sourcewindow=window.open (servicepopurl, "servicepopup", "location=0,status=0,scrollbars=1,menubar=0,width=400,height=300");
		if (window.focus) {servicewindow.focus()}
	}
}
