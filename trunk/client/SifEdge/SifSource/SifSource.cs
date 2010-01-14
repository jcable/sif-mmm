/*
Sif source

classes to handle one logical source

can be an active or a standby instance

fetches a schedule from the server and translates to VLC/VLM syntax

listens for update messages
go active/standby
re-read schedule
publishes a dns-sd service for itself (not really needed)

controls a vlc instance
*/
using System;
using System.Collections.Generic;
using System.IO;
using System.Text;
using System.Xml;
using System.Net;
using SimplePublishSubscribe;
using RabbitMQ.Client;

namespace SifSource
{
    class MediaEvent
    {
        public MediaEvent(XmlNode node)
        {
            XmlNodeList childNodes = node.ChildNodes;
            foreach (XmlNode child in childNodes)
            {
                Console.WriteLine(child.Name+" "+child.InnerXml);
            }
            childNodes = null;
        }
    }

    class MediaEventSchedule
    {
        public List<MediaEvent> schedule;

        public MediaEventSchedule(XmlDocument xd)
        {
            XmlNodeList xn = xd.GetElementsByTagName("row");
            schedule = new List<MediaEvent>(xn.Count);
            foreach (XmlNode n in xn)
            {
                schedule.Add(new MediaEvent(n));
            }
        }
    }

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

        public override string ToString()
        {
            DateTime d=new DateTime();
            string s = "{ " + service;
            if (first_date != d)
                s += ", "+first_date.ToString();
            if (last_date != d)
                s += ", "+last_date.ToString();
            if (days != "")
                s += ", "+days;
            s += ", "+start.ToString() + ", " + duration.ToString();
            if (material_id != "")
                s += ", "+material_id;
            if(rot)
                s += ", rot";
            s+="}";
            return s;
        }

        public string service;
        private DateTime first_date, last_date;
        private string days;
        public DateTime start;
        private TimeSpan duration;
        public string material_id;
        private bool rot;
    }

    class RecordBasedSchedule
	{
        List<ScheduleRecord> schedule;

		public RecordBasedSchedule(XmlDocument xd)
		{
            XmlNodeList xn = xd.GetElementsByTagName("row");
            schedule = new List<ScheduleRecord>(xn.Count);
            foreach (XmlNode n in xn)
            {
                schedule.Add(new ScheduleRecord(n));
            }
		}

	}

    class EventBasedSchedule
    {
        public List<ScheduleRecord> schedule;

        public EventBasedSchedule(XmlDocument xd)
        {
            XmlNodeList xn = xd.GetElementsByTagName("row");
            schedule = new List<ScheduleRecord>(xn.Count);
            foreach (XmlNode n in xn)
            {
                schedule.Add(new ScheduleRecord(n));
            }
        }
    }

	public class Source
	{
		private string url;
		private string id, vlm_id;
		private string device, pcm;
		private bool active;
		private RecordBasedSchedule rsched;
        private EventBasedSchedule esched;
        private MediaEventSchedule msched;
        private SifVLM vlm;
        private IConnection conn;
		              
		public Source(string url, string id, string device, string pcm, bool active, IConnection conn)
		{
			this.url = url;
			this.id = id;
			this.vlm_id = id.Replace(' ','_');
            this.device = device;
            this.pcm = pcm;
            this.conn = conn;
		}
		
		public void run()
		{
            vlm = new SifVLM();
            refresh();
			if(active)
				setactive();
			else
				setinactive();
            vlm.cmd("new "+vlm_id+" broadcast");
            subscribe();
        }

        private void refresh()
        {
            fetchSchedule();
            writeSchedule();
        }

        private XmlDocument fetch(string url)
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

        private XmlDocument post(string url, Dictionary<string, string> args)
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

		private void fetchSchedule()
        {
            Console.WriteLine("fetching schedule");
            esched = new EventBasedSchedule(fetch(url + "/serviceeventschedule.php?source=" + id));
        }

        private void fetchParams(string service)
        {
            Console.WriteLine("fetching params for "+service);
            msched = new MediaEventSchedule(fetch(url + "/serviceparams.php?service=" + service));
        }

        private void fetchRSchedule()
		{
            Console.WriteLine("fetching schedule");
            rsched = new RecordBasedSchedule(fetch(url + "/serviceschedule.php?source=" + id));
		}

        private void writeSchedule()
		{
            DateTime now = System.DateTime.Now;
            ScheduleRecord current = null;
            foreach (ScheduleRecord s in esched.schedule)
            {
                Console.WriteLine(s.ToString());
                if(current==null)
                    current = s;
                if (s.start <= now && s.start > current.start)
                    current = s;
            }
            if (current != null)
            {
                fetchParams(current.service);
            }
        }

        private void subscribe()
		{
            string exchangeName = "sif";
            string routingKey = id;
			string queueName = System.Guid.NewGuid().ToString();
			try {
	            Listener listener = new Listener(exchangeName, routingKey, queueName, conn);
	            listener.MessageReceived += new MessageHandler(DoMessage);
	            Console.WriteLine("listening on queue "+queueName+" for "+id);
	            listener.listen();
			}catch(Exception e)
			{
				Console.WriteLine(e.Message);
			}
		}

		private void setactive()
		{
			active=true;
            vlm.cmd("setup "+vlm_id+" enabled");
            Console.WriteLine("active");
        }

        private void setinactive()
		{
			active=false;
            vlm.cmd("setup "+vlm_id+" disabled");
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
                case "oi":
                    switch(kv[1])
                    {
                    	case "OFF":
							register_event_as_run(device, id, "", "OFF");
                    		break;
                    	case "":
		                    refresh();
                    		break;
                    	default:
                    		register_event_as_run(device, id, kv[1], "ON");
                    		break;
                    }
                    break;
            }
        }
									
        private void register_event_as_run(string device, string input, string output, string action)
        {
        	Dictionary<string, string> args = new Dictionary<string, string>();
        	args.Add("device", device);
        	args.Add("input", input);
        	args.Add("output", output);
        	args.Add("action", action);
        	post(url+"/register_event_as_run.php", args);
        }

	}
}