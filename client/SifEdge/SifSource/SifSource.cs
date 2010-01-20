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
using RabbitMQ.Client;

namespace Sif
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
    
    public class Source : Edge
	{
		private Dictionary<string, ServiceInstance> service;
		private RecordBasedSchedule rsched;
        private EventBasedSchedule esched;
        private MediaEventSchedule msched;

		public Source(string url, IConnection conn, string device, XmlNode node)
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
            fetchSchedule();
            writeSchedule();
        }

		private void fetchSchedule()
        {
            Console.WriteLine("fetching schedule");
            esched = new EventBasedSchedule(Web.fetch(url + "/serviceeventschedule.php?source=" + id));
        }

        private void fetchParams(string service)
        {
            Console.WriteLine("fetching params for "+service);
            msched = new MediaEventSchedule(Web.fetch(url + "/serviceparams.php?service=" + service));
        }

        private void fetchRSchedule()
		{
            Console.WriteLine("fetching schedule");
            rsched = new RecordBasedSchedule(Web.fetch(url + "/serviceschedule.php?source=" + id));
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

        protected override void DoMessage(object sender, string key, byte[] message)
        {
            PrintMessage(sender, key, message);
            string s = System.Text.Encoding.UTF8.GetString(message);
            string[] kv = s.Split('=');
            if(key==id) // its a message for us as a source
            {
	            switch (kv[0])
	            {
	                case "oi":
	            		add_instance(kv[1]);
	            		service[kv[1]].playAll();
                		register_event_as_run(device, id, kv[1], "ON");
                		listener.listenFor(kv[1]);
	                    break;
	            }            	
            }
            else // assume its a message for a service we are sourcing
            {
	            switch (kv[0])
	            {
	                case "oi":
	                    switch(kv[1])
	                    {
	                    	case "OFF":
			            		service[key].stopAll();
			            		service[key].delete();
			            		service.Remove(key);
								register_event_as_run(device, id, key, "OFF");
								listener.ignore(key);
	                    		break;
	                    	case "":
			                    refresh();
	                    		break;
	                    	default:
	                    		break;
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