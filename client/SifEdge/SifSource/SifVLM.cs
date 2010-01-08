using System;
using System.Collections.Generic;
using System.Text;
using System.Xml;
using System.Net;
using System.IO;

/* VLM example
new my_media broadcast enabled
setup my_media input my_video.mpeg input my_other_movie.mpeg
setup my_media output #standard{mux=ts,access=udp,dst=239.255.1.1,sap,name="My Media"}

control channel1 play
control channel2 play

new my_sched schedule enabled
setup my_sched date 2012/12/12-12:12:12
setup my_sched append control my_media play

* do we use the telnet or the http interface ?
*/

namespace SifSource
{
    class VLMInput
    {
    }

    class VLMOutput
    {
    }

    class VLMMedia
    {
        public VLMInput[] input;
        public Dictionary<string, string> option;
        bool enabled;

        public VLMMedia()
        {
            input = new VLMInput[1];
            option = new Dictionary<string,string>();
            enabled = false;
        }
    }

    class VLMVOD : VLMMedia
    {
    }

    class VLMBroadcast : VLMMedia
    {
        public VLMOutput output;
        public bool loop;
        public string mux;

        public VLMBroadcast()
        {
            output = new VLMOutput();
        }
        public void play()
        {
        }
        public void pause()
        {
        }
        public void stop()
        {
        }
        public void seek(int percent)
        {
        }
    }

    class VLMSchedule
    {
        public bool enabled;
        public string[] command;
        public DateTime date;
        public TimeSpan period;
        public uint repeat;
    }

	class SifVLM
	{
        private string url;

        public SifVLM()
        {
            url = "http://localhost:8080/requests/";
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

        public XmlDocument get()
        {
            return fetch(url + "vlm.xml");
        }
        public void cmd(string cmd)
        {
            XmlDocument resp = fetch(url + "vlm_cmd.xml?command="+cmd);
            string err = resp.GetElementsByTagName("error")[0].InnerText;
            if (err != "")
                Console.WriteLine(err);
        }
    }
}
