/*
 * Created by SharpDevelop.
 * User: CableJ01
 * Date: 20/01/2010
 * Time: 08:57
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.Collections.Generic;
using System.IO;
using System.Text;
using System.Xml;
using System.Net;
using SimplePublishSubscribe;
using RabbitMQ.Client;
using VLM;

namespace Sif
{
	/// <summary>
	/// Description of Edge.
	/// </summary>
	public class Edge
	{
		protected string url;
		protected string id, input;
		protected bool loop, active;
		protected string device;
        protected VLM.VLM vlm;
        protected IConnection conn;
	    protected Listener listener;

		public Edge(string url, IConnection conn, string device, XmlNode node)
		{
			this.url = url;
			this.conn = conn;
			this.device = device;

			id=""; input=""; active=false; loop=false;
			
            //service = new Dictionary<string,ServiceInstance>();

            foreach (XmlNode child in node.ChildNodes)
            {
                switch (child.Name)
                {
                    case "id":
                        id = child.InnerText;
                        break;
                    case "input":
                        input = child.InnerText;
                        break;
                    case "active":
                        if (child.InnerText == "true" || child.InnerText == "1")
                            active = true;
                        else
                            active = false;
                        break;
                    case "loop":
                        if (child.InnerText == "true" || child.InnerText == "1")
                            loop = true;
                        else
                            loop = false;
                        break;
                }
            }
 		}
		
		public virtual void run()
		{
            vlm = new VLM.VLM();
            subscribe();
        }

        protected void subscribe()
		{
            string exchangeName = "sif";
            string routingKey = id;
			string queueName = System.Guid.NewGuid().ToString();
			try {
	            listener = new Listener(exchangeName, queueName, conn);
	            listener.MessageReceived += new MessageHandler(DoMessage);
	            listener.listenFor(routingKey);
	            Console.WriteLine("listening on queue "+queueName+" for "+id);
	            listener.listen();
			}catch(Exception e)
			{
				Console.WriteLine(e.Message);
			}
		}

        protected void register_event_as_run(string device, string input, string output, string action)
        {
        	Dictionary<string, string> args = new Dictionary<string, string>();
        	args.Add("device", device);
        	args.Add("input", input);
        	args.Add("output", output);
        	args.Add("action", action);
        	Web.post(url+"/register_event_as_run.php", args);
        }

        protected void PrintMessage(object sender, string key, byte[] message)
        {
            Console.WriteLine(System.Text.Encoding.UTF8.GetString(message)+" for "+key);
        }

        protected virtual void DoMessage(object sender, string key, byte[] message)
        {
            PrintMessage(sender, key, message);
        }									
	}
	
	public class Web {
	    public static XmlDocument fetch(string url)
	    {
	        WebClient myClient = new WebClient();
	        Stream response = myClient.OpenRead(url);
	        StreamReader reader = new StreamReader(response);
	        string r = reader.ReadToEnd();
	        XmlDocument xd = new XmlDocument();
	        xd.LoadXml(r);
	        response.Close();
	        return xd;
	    }
	
	    public static XmlDocument post(string url, Dictionary<string, string> args)
	    {
	        HttpWebRequest request = WebRequest.Create(url) as HttpWebRequest;
	        request.Method = "POST";
	        string parameters = "";
	        foreach(string key in args.Keys)
	        {
	        	parameters += "&" + key + "=" + args[key];
	        }
	        byte[] byteArray = Encoding.UTF8.GetBytes(parameters.Substring(1));
	
	        request.ContentType = "application/x-www-form-urlencoded";
	        request.ContentLength = byteArray.Length;
	        Stream dataStream = request.GetRequestStream();
	        dataStream.Write(byteArray, 0, byteArray.Length);
	        dataStream.Close();
	
	        HttpWebResponse response = (HttpWebResponse)request.GetResponse();
	
	        StreamReader reader = new StreamReader(response.GetResponseStream());
	        string r = reader.ReadToEnd();
	        XmlDocument xd = new XmlDocument();
	        xd.LoadXml(r);
	        return xd;
		}
	}
}
