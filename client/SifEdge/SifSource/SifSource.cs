/*
Sif source

classes to handle one logical source

can be an active or a standby instance

fetches a schedule from the server and translates to VLC/VLM syntax

listens for update messages
go active/standby
re-read schedule
crash
publishes a dns-sd service for itself (not really needed)

controls a vlc instance

NB because a source can have multiple destinations and #duplicate doesn't do what we want
we need to name broadcast elements in vlm so we can have a one-many relation between
sif sources (or edges) and broadcast elements. and we will need a dictionary or something
to keep this straight.


source X is currently routed to services A and B

If we crash A to Y then X must stop A but not B
If we crash C to X then this is an additional output.

So the OFF oi needs to indicate the service thats going off!

but it does, coz its sent to the service!!! 

How do we handle multiple bit-rates/encodings ?

currently the encoding is part of the service definition which doesn't work - its kind
of part of the listener definition.

One possibility is for listeners to signal via the message bus that they want a particular
encoding. If we do this on separate ports in the group then all listeners will get all encodings
so we need separate groups for each one. How do we manage these groups ?

How do we manage source specific encodings like:

1) fill on SMDS should be lower bit-rate than other content
2) Music on DRM should be stereo, everything else mono

These can be considered source specific or schedule event specific.

TODO use as_run on startup to recover existing events

*/
using System;
using System.Collections.Generic;
using System.IO;
using System.Text;
using System.Xml;
using System.Net;
using System.Runtime.Serialization.Json;

namespace Sif
{    
    class ServiceInstance
    {
    	public VLM.Broadcast[] destination;

    	public ServiceInstance(string id, string input, bool loop,
    	                   string service, VLM.VLM vlm, XmlNodeList xn)
    	{
	    	destination = new VLM.Broadcast[xn.Count];
            for(int i=0; i<destination.Length; i++)
            {
		    	string instance_id = id+"_"+service+"_"+i;
            	VLM.Broadcast bc = new VLM.Broadcast(instance_id,vlm);
            	bc.addinput(input);
            	string o = xn[i].InnerText;
            	bc.output = o;
            	bc.enabled=true;
            	bc.loop = loop;
            	destination[i] = bc;
            }
    	}

    	public void playAll()
    	{
    		foreach(VLM.Broadcast bc in destination)
    		{
    			bc.play();
    		}
    	}

    	public void stopAll()
    	{
    		foreach(VLM.Broadcast bc in destination)
    		{
    			bc.stop();
    		}
    	}

    	public void delete()
    	{
    		foreach(VLM.Broadcast bc in destination)
    		{
    			bc.delete();
    		}
    	}
    }
    
	[Serializable]
	class SourceMessage
	{
		public string message="", service="";
	}

	public class Source : Edge
	{
		private Dictionary<string, ServiceInstance> service;

		public Source(string url, MessageConnection conn, string device, XmlNode node)
			:base(url, conn, device, node)
		{
            service = new Dictionary<string,ServiceInstance>();
 		}
		
		public override void run()
		{
            vlm = new VLM.VLM();
            refresh();
            subscribe();
        }

        private void refresh()
        {
        }

        protected override void DoMessage(object sender, string key, byte[] message)
        {
            PrintMessage(sender, key, message);
    		DataContractJsonSerializer ser = new DataContractJsonSerializer(typeof(SourceMessage));
    		MemoryStream ms = new MemoryStream(message);
    		SourceMessage data = ser.ReadObject(ms) as SourceMessage;
            if(key==id) // its a message for us as a source
            {
	            switch (data.message)
	            {
	                case "oi":
	            		add_instance(data.service);
	            		service[data.service].playAll();
                		register_event_as_run(device, id, data.service, "ON");
                		listener.listenFor(data.service);
	                    break;
	            }            	
            }
            else // assume its a message for a service we are sourcing
            {
	            switch (data.message)
	            {
	                case "oi":
	                    if(data.service=="OFF")
	                    {
		            		service[key].stopAll();
		            		service[key].delete();
		            		service.Remove(key);
							register_event_as_run(device, id, key, "OFF");
							listener.ignore(key);
	                    }
	                    break;
	            }
            	
            }
        }
        
		// add a source with input and output and enable it
	    private void add_instance(string service)
	    {
	    	XmlDocument xd = Web.fetch(url+"/getoutputs.php?id="+service);
	    	XmlNodeList outputs = xd.GetElementsByTagName("output");
	    	this.service.Add(service, new ServiceInstance(id, input, loop, service, vlm, outputs));
	    }
	}
}