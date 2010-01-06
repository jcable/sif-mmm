/*
Sif source

classes to handle one logical source

can be an active or a standby instance

fetches a schedule from the server and translates to VLC/VLM syntax

listens for update messages
go active/standby
re-read schedule
publishes a dns-sd service for itself

controls a vlc instance
*/
using System;
using System.IO;
using System.Xml;
using System.Net;
using SimplePublishSubscribe;

namespace SifSource
{
	class ScheduleRecord
	{
        public ScheduleRecord(XmlNode node)
        {
            XmlNodeList childNodes = node.ChildNodes;
            foreach (XmlNode child in childNodes)
            {
        		switch(child.Name)
        		{
            		case "service":
                        service = child.InnerXml;
                        break;
            		case "first_date":
                        first_date = System.DateTime.Parse(child.InnerXml);
                        break;
                    case "last_date":
                        last_date = System.DateTime.Parse(child.InnerXml);
                        break;
                    case "days":
                        days = child.InnerXml;
                        break;
                    case "start":
                        start = System.DateTime.Parse(child.InnerXml);
                        break;
                    case "duration":
                        duration = System.TimeSpan.Parse(child.InnerXml);
                        break;
                    case "material_id":
                        material_id = child.InnerXml;
                        break;
                    case "rot":
                        rot = (child.InnerXml == "1") ? true : false;
                        break;
                }
            }
            childNodes = null;
        }

        private string service;
        private DateTime first_date, last_date;
        private string days;
        private DateTime start;
        private TimeSpan duration;
        private string material_id;
        private bool rot;
    }

    class RecordBasedSchedule
	{
        ScheduleRecord[] schedule;

		public RecordBasedSchedule(XmlDocument xd)
		{
            XmlNodeList xn = xd.GetElementsByTagName("row");
            schedule = new ScheduleRecord[xn.Count];
            int i=0;
            foreach (XmlNode n in xn)
            {
                schedule[i++] = new ScheduleRecord(n);
            }
		}

	}

	class Source
	{
		private string url;
		private string id;
		private string device, pcm;
		private bool active;
		private RecordBasedSchedule rsched;
		              
		public Source(string url, string id, string device, string pcm, bool active)
		{
			this.url = url;
			this.id = id;
            this.device = device;
            this.pcm = pcm;
			fetchSchedule();
			writeSchedule();
			runEncoder();
			subscribe();
			if(active)
				setactive();
			else
				setinactive();
		}
		
		private void fetchSchedule()
		{
            Console.WriteLine("fetching schedule");
            WebClient myClient = new WebClient();
			Stream response = myClient.OpenRead(url+"/serviceschedule.php?source="+id);
	        StreamReader reader = new StreamReader(response);
	        string r = reader.ReadToEnd();	
	        XmlDocument xd = new XmlDocument();
	        xd.LoadXml(r);
			rsched = new RecordBasedSchedule(xd);
			response.Close();
		}
		private void writeSchedule()
		{
		}
		private void runEncoder()
		{
		}
		private void subscribe()
		{
			// TODO get MQ host from zeroconf or web server
            string hostName = "localhost";
            int portNumber = 5672;
            string exchangeName = "sif";
            string routingKey = id;
			string queueName = "some guid";
            Listener listener = new Listener(exchangeName, hostName, portNumber, routingKey, queueName);
            listener.MessageReceived += new MessageHandler(DoMessage);
            listener.listen();
		}
		private void setactive()
		{
			active=true;
            Console.WriteLine("active");
        }
		private void setinactive()
		{
			active=false;
            Console.WriteLine("inactive");
        }
        private void PrintMessage(object sender, byte[] message)
        {
            Console.WriteLine(System.Text.Encoding.UTF8.GetString(message));
        }

        private void DoMessage(object sender, byte[] message)
        {
            PrintMessage(sender, message);
            string s = System.Text.Encoding.UTF8.GetString(message);
            string[] kv = s.Split('=');
            switch (kv[0])
            {
                case "activate":
                    if (kv[1] == device)
                        setactive();
                    break;
                case "deactivate":
                    if (kv[1] == device)
                        setinactive();
                    break;
                case "refresh":
                    fetchSchedule();
                    break;
            }
        }
	}
}