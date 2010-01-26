/*
 * Created by SharpDevelop.
 * User: CableJ01
 * Date: 19/01/2010
 * Time: 23:45
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;
using System.Text;
using System.Xml;
using System.Runtime.Serialization.Json;
using System.Collections.Generic;

namespace Sif
{
	/// <summary>
	/// Description of SifListener.
	/// </summary>

	[Serializable]
	class ListenerMessage
	{
		public string message="", service="", access="", dst="";
	}
	
	public class Listener : Edge
	{
		private VLM.Broadcast media_device;

		public Listener(string url, MessageConnection conn, string device, XmlNode node)
			:base(url, conn, device, node)
		{
			if(vlm==null)
				vlm = new VLM.VLM();
			media_device = new VLM.Broadcast(id, vlm);
			XmlDocument xd = Web.fetch(url+"/getedgeoutput.php?id="+id+"&device="+device);
	    	XmlNodeList outputs = xd.GetElementsByTagName("output");
	    	media_device.output=outputs[0].InnerText;
		}
		
        protected override void DoMessage(object sender, string key, byte[] message)
        {
            PrintMessage(sender, key, message);
    		DataContractJsonSerializer ser = new DataContractJsonSerializer(typeof(ListenerMessage));
    		MemoryStream ms = new MemoryStream(message);
    		ListenerMessage data = ser.ReadObject(ms) as ListenerMessage;
            if(key==id) // its a message for us as a listener
            {
	            switch (data.message)
	            {
	                case "oi":
	            		if(data.service=="OFF")
	            		{
	                		register_event_as_run(device, "ANY", id, "OFF");
	            		}
	            		else
	            		{
		            		media_device.addinput(data.access+"://@"+data.dst);
		            		media_device.enabled=true;
		            		media_device.play();
	                		register_event_as_run(device, data.service, id, "ON");
	            		}
	                    break;
	            }            	
            }
        }

	}
}
