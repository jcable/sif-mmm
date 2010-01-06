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
	class RecordBasedSchedule
	{
		public RecordBasedSchedule(XmlDocument xd)
		{
		}
	}

	class Source
	{
		private string url;
		private string id;
		private string pcm;
		private bool active;
		private RecordBasedSchedule rsched;
		              
		public Source(string url, string id, string pcm, bool active)
		{
			this.url = url;
			this.id = id;
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
			WebClient myClient = new WebClient();
			Stream response = myClient.OpenRead(url+"/serviceschedule.php?source="+id);
	        StreamReader reader = new StreamReader(response);
	        string r = reader.ReadToEnd();	
	        Console.WriteLine(r);
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
            string hostName = "dev.rabbitmq.com";
            hostName = "192.168.2.137";
            int portNumber = 5672;
            string exchangeName = "sif";
            string routingKey = id;
			string queueName = "some guid";
            Listener listener = new Listener(exchangeName, hostName, portNumber, routingKey, queueName);
            listener.MessageReceived += new MessageHandler(PrintMessage);
            listener.listen();
		}
		private void setactive()
		{
			active=true;
		}
		private void setinactive()
		{
			active=false;
		}
        private void PrintMessage(object sender, byte[] message)
        {
            Console.WriteLine(System.Text.Encoding.UTF8.GetString(message));
        }


	
	}
}