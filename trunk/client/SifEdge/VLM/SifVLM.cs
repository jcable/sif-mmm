using System;
using System.Collections.Generic;
using System.Text;
using System.Xml;
using System.Xml.XPath;
using System.Net;
using System.Web;
using System.IO;

/* VLM example
new my_media broadcast enabled
setup my_media input my_video.mpeg input my_other_movie.mpeg
setup my_media output #standard{mux=ts,access=udp,dst=239.255.1.1,sap,name="My Media"}

control channel1 play
control channel2 play
 :dshow-vdev= :dshow-adev=:dshow-caching=200 :dshow-vdev=none :dshow-adev=SoundMAX HD Audio :dshow-size= :dshow-chroma= :dshow-fps=0 :dshow-config :no-dshow-tuner :dshow-tuner-channel=0 :dshow-tuner-country=0 :dshow-tuner-input=0 :dshow-video-input=-1 :dshow-video-output=-1 :dshow-audio-input=-1 :dshow-audio-output=-1 :dshow-amtuner-mode=1 :dshow-audio-channels=0 :dshow-audio-samplerate=0 :dshow-audio-bitspersample=0
new my_sched schedule enabled
setup my_sched date 2012/12/12-12:12:12
setup my_sched append control my_media play

* do we use the telnet or the http interface ?
* currently we use the VLM http interface

--sout-ts-pid-audio
--sout-ts-pid-pmt <integer>
 */

namespace VLM
{
    public class Media
    {
        private bool enabled_f;
        protected string id;
		protected VLM vlm;

        public Media(string id, VLM vlm)
        {
		this.id = id.Replace(' ', '_');
		this.id = this.id.Replace('\'', '_');
        	this.vlm = vlm;
            enabled_f = false;            
        }

	    public bool enabled
	    {
	        get
	        {
	            return enabled_f;
	        }
	        set
	        {
	            enabled_f = value;
	            if(enabled_f)
        		    setup( "enabled");
	            else
		            setup( "disabled");
	        }
	    }

		public void addinput(string inp)
		{
        	string[] p = inp.Split('\n');
        	string mrl = p[0];
        	setup("input \""+mrl+"\"");
        	for(int i=1; i<p.Length; i++)
        	{
        		string[] kv = p[i].Split('=');
        		option(kv[0], kv[1]);
        	}
        }

		public string[] inputs()
		{
			XPathDocument state = vlm.get();
			XPathNavigator navigator = state.CreateNavigator();
			XPathNodeIterator iterator = navigator.Select("//broadcast[@name='"+id+"']/inputs/input");
			string[] input = new string[iterator.Count];
			int i=0;
			while (iterator.MoveNext())
			{
			    input[i++] = iterator.Current.Value;
			}
			return input;
        }

        public void deleteinput(string inp)
		{
        	string[] input = inputs();
        	for(int i=1; i<=input.Length; i++)
        	{
        		if(input[i]==inp)
        		{
		        	setup("inputdeln "+i);
        		}	
        	}
        }

        public void clearinputs()
		{
        	string[] input = inputs();
        	for(int i=input.Length; i>0; i--)
        	{
		        setup("inputdeln "+i);
        	}
        }

        public void option(string key, string val)
		{
        	setup("option "+key+"="+val);
        }

        protected void setup(string prop)
		{
        	vlm.cmd("setup "+id+" "+prop);
        }
    }

    public class VOD : Media
    {
        public VOD(string id, VLM vlm):base(id,vlm)
        {
        	
        }
    }

    public class Broadcast : Media
    {
        private string output_f;
        private bool loop_f;
        public string mux;

        public Broadcast(string id, VLM vlm):base(id,vlm)
        {
			vlm.cmd("new "+this.id+" broadcast");
            output_f = "";
            loop_f = false;
            mux = "";
        }

        public void play()	{control("play");}
        public void pause()	{control("pause");}
        public void stop()	{control("stop");}
        public void seek(int percent) {control("seek "+percent);}
        public void delete() {vlm.cmd("del "+id);}

	    public string output
	    {
	        get
	        {
	            return output_f;
	        }
	        set
	        {
	            output_f = value;
	        	string[] p = output_f.Split('\n');
	        	string mrl = p[0];
        		setup("output "+mrl);
	        	for(int i=1; i<p.Length; i++)
	        	{
	        		string[] kv = p[i].Split('=');
	        		option(kv[0], kv[1]);
	        	}
	        }
        }
        
        public bool loop
		{
        	get
        	{
        		return loop_f;
        	}
	        set
	        {
	            loop_f = value;
	            if(loop_f)
        		    setup("loop");
	            else
        		    setup("unloop");
	        }
        }

        private void control(string cmd)
		{
        	vlm.cmd("control "+id+" "+cmd);
        }
    }

    public class Schedule
    {
        public bool enabled;
        public List<string> command;
        public DateTime date;
        public TimeSpan period;
        public uint repeat;
        
        public Schedule()
        {
        	enabled=false;
        	command=new List<string>();
        	date=new DateTime();
        	period = new TimeSpan();
        	repeat = 0;
        }
    }

	public class VLM
	{
        private string url;

        public VLM()
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

        public XPathDocument get()
        {
			return new XPathDocument(url + "vlm.xml");
        }

        public void cmd(string cmd)
        {
		    Console.WriteLine(cmd);
            string args = HttpUtility.UrlEncode(cmd).Replace("+","%20");
            XmlDocument resp = fetch(url + "vlm_cmd.xml?command="+args);
            string err = resp.GetElementsByTagName("error")[0].InnerText;
            if (err != "")
                Console.WriteLine("VLM Error: "+err);
        }
    }
}
