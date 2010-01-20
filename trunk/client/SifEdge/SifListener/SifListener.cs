/*
 * Created by SharpDevelop.
 * User: CableJ01
 * Date: 19/01/2010
 * Time: 23:45
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.Xml;
using RabbitMQ.Client;

namespace Sif
{
	/// <summary>
	/// Description of SifListener.
	/// </summary>

	
	public class Listener : Edge
	{
		private VLM.Broadcast media_device;

		public Listener(string url, IConnection conn, string device, XmlNode node)
			:base(url, conn, device, node)
		{
			media_device = new VLM.Broadcast(id, vlm);
		}
		
        protected override void DoMessage(object sender, string key, byte[] message)
        {
            PrintMessage(sender, key, message);
            string s = System.Text.Encoding.UTF8.GetString(message);
            string[] kv = s.Split('=');
            if(key==id) // its a message for us as a listener
            {
	            switch (kv[0])
	            {
	                case "oi":
	            		string service="ENAFW";
	            		string dst="239.1.1.1:5004";
	            		
	            		//new x broadcast enabled input "udp://@239.1.1.1:5004" output #display
	            		// JSON parse
	            		media_device.addinput("udp://@"+dst);
	            		media_device.enabled=true;
	            		media_device.output="#display";
	            		media_device.play();
                		listener.listenFor(service);
	                    break;
	            }            	
            }
        }

	}
}
