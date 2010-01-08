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
using System.Xml;
using System.Net;
using SimplePublishSubscribe;

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

	class Source
	{
		private string url;
		private string id;
		private string device, pcm;
		private bool active;
		private RecordBasedSchedule rsched;
        private EventBasedSchedule esched;
        private MediaEventSchedule msched;
        private SifVLM vlm;
		              
		public Source(string url, string id, string device, string pcm, bool active)
		{
			this.url = url;
			this.id = id;
            this.device = device;
            this.pcm = pcm;
            refresh();
			if(active)
				setactive();
			else
				setinactive();
            //runEncoder();
            vlm = new SifVLM();
            vlm.cmd("new "+id+" broadcast");
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
            string config_item = "message_bus_host";
            XmlDocument doc = fetch(url + "/getconfig.php?key=" + config_item);
            string hostName = doc.GetElementsByTagName(config_item)[0].InnerText;
            int portNumber = 5672;
            string exchangeName = "sif";
            string routingKey = id;
			string queueName = System.Guid.NewGuid().ToString();
            Listener listener = new Listener(exchangeName, hostName, portNumber, routingKey, queueName);
            listener.MessageReceived += new MessageHandler(DoMessage);
            listener.listen();
		}

		private void setactive()
		{
			active=true;
            vlm.cmd("setup sif enabled");
            Console.WriteLine("active");
        }

        private void setinactive()
		{
			active=false;
            vlm.cmd("setup sif disabled");
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
                    refresh();
                    break;
            }
        }
	}
}