
using System;
using System.Threading;
using System.Collections.Generic;
using System.Diagnostics;
using System.Text;
using System.Xml;
using System.Web;
using System.Net;
using System.Net.NetworkInformation;
using System.Web.Services;
using System.IO;
using Mono.Zeroconf;
using RabbitMQ.Client;

namespace SifClient
{

    public class SifClient
    {
        private bool done = false;
        private Process vlc;
        private List<Process> edge;
        private List<Thread> local_edge;
        private string url, device;

        public SifClient()
        {
            string type = "_http._tcp";

            try {
	            ServiceBrowser browser = new ServiceBrowser();
	            browser.ServiceAdded += OnServiceAdded;
	            browser.ServiceRemoved += OnServiceRemoved;
	            browser.Browse(type, "local");
	
            } catch(Exception e)
            {
            	url = "http://localhost/sif";
            	register();
            }
            while (!done)
            {
                System.Threading.Thread.Sleep(1000);
            }
        }

        public SifClient(string url)
        {
			this.url = url;
        	register();
            while (!done)
            {
                System.Threading.Thread.Sleep(1000);
            }
        }

		private void OnServiceAdded(object o, ServiceBrowseEventArgs args)
        {
            if (args.Service.AddressProtocol == AddressProtocol.IPv4 && args.Service.Name.CompareTo("SIF Management") == 0)
            {
                args.Service.Resolved += OnServiceResolved;
                args.Service.Resolve();
            }
        }

        private void OnServiceRemoved(object o, ServiceBrowseEventArgs args)
        {
            Console.WriteLine("*** Lost  name = '{0}', type = '{1}', domain = '{2}'",
                              args.Service.Name,
                              args.Service.RegType,
                              args.Service.ReplyDomain);
        }

        private void OnServiceResolved(object o, ServiceResolvedEventArgs args)
        {
            string path = "";
            IResolvableService service = o as IResolvableService;
            ITxtRecord record = service.TxtRecord;
            int record_count = record != null ? record.Count : 0;
            if (record_count > 0)
            {
                for (int i = 0, n = record.Count; i < n; i++)
                {
                    TxtRecordItem item = record.GetItemAt(i);
                    if (item.Key.CompareTo("path") == 0)
                    {
                        path = item.ValueString;
                    }
                }
            }
            url = "http://" + service.HostEntry.AddressList[0] + ":" + service.Port+"/"+path;

            register();
        }
        
        private void register()
        {
            NetworkInterface[] nics = NetworkInterface.GetAllNetworkInterfaces();
            if (nics == null || nics.Length < 1)
            {
                Console.WriteLine("  No network interfaces found.");
                return;
            }

            PhysicalAddress address = nics[0].GetPhysicalAddress();
            foreach (NetworkInterface adapter in nics)
            {
                if (adapter.Description.CompareTo("eth0") == 0)
                    address = adapter.GetPhysicalAddress();
            }
            
            try {
	            HttpWebRequest request = WebRequest.Create(url+"/register.php") as HttpWebRequest;
	            request.Method = "POST";
	            string parameter = "mac=" + address.ToString();
	            byte[] byteArray = Encoding.UTF8.GetBytes(parameter);
	
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
	            parseRegisterResponse(xd);
            } catch(Exception e)
            {
            	Console.WriteLine(e.Message);
	            done = true;
            }
        }

        private void parseRegisterResponse(XmlDocument xd)
        {
            XmlNodeList xn = xd.GetElementsByTagName("response");
            string result = xn[0].InnerText;
            Console.WriteLine(result);
            xn = xd.GetElementsByTagName("message_bus_host");
			string message_bus_host = xn[0].InnerText;
            ConnectionFactory factory = new ConnectionFactory();
            IConnection conn = factory.CreateConnection(
            	Protocols.FromEnvironment(), message_bus_host, 5672
            );

            xn = xd.GetElementsByTagName("id");
            if(xn.Count>0)
            {
            	device = xn[0].InnerText;
            	edge = new List<Process>();
            	local_edge = new List<Thread>();
	            xn = xd.GetElementsByTagName("source");
	            foreach (XmlNode node in xn)
	            {
	                createSource(node, conn);
	            }
	            xn = xd.GetElementsByTagName("listener");
	            foreach (XmlNode node in xn)
	            {
	                createListener(node);
	            }
            }
            xn = null;
        }

        private void createSource(XmlNode node, IConnection conn)
        {
            XmlNodeList childNodes = node.ChildNodes;

            string id="", input="";
            bool active=false;

            //And walk through them
            foreach (XmlNode child in childNodes)
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
                }
            }
            childNodes = null;

            SifSource.Source s = new SifSource.Source(url, id, input, device, conn);
		    Thread oThread = new Thread(new ThreadStart(s.run));
			oThread.Start();
			local_edge.Add(oThread);
       }

        private void createListener(XmlNode node)
        {
        	
        }
        
        private void runEncoder()
        {
            vlc = new Process();
            vlc.StartInfo.FileName = "C:\\Program Files\\VideoLAN\\VLC\\vlc.exe";
            vlc.StartInfo.Arguments = "--intf http";
            vlc.StartInfo.UseShellExecute = false;
            vlc.StartInfo.RedirectStandardOutput = false;
            vlc.Start();
        }
 
        private void runSource(string id, string pcm, bool active)
        {
            Process p = new Process();
            p.StartInfo.FileName = "SifSource.exe";
            p.StartInfo.Arguments = url+" \""+id+"\" "+device+" "+pcm+(active?"true":"false");
            p.StartInfo.UseShellExecute = false;
            p.StartInfo.RedirectStandardOutput = false;
            p.Start();
            edge.Add(p);
        }
    }
}