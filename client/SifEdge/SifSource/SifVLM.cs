﻿using System;
using System.Collections.Generic;
using System.Text;
using System.Xml;
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

namespace SifSource
{
    class VLMMedia
    {
        public List<string> input;
        public Dictionary<string, string> option_f;
        private bool enabled_f;
        protected string id;
		protected SifVLM vlm;

        public VLMMedia(string id, SifVLM vlm)
        {
			this.id = id.Replace(' ', '_');
        	this.vlm = vlm;
        	input = new List<string>();
            option_f = new Dictionary<string,string>();
            enabled = false;            
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
        		    vlm.cmd("setup "+id+" enabled");
	            else
		            vlm.cmd("setup "+id+" disabled");
	        }
	    }

        public void addinput(string inp)
		{
        	string[] p = inp.Split('\n');
        	string mrl = p[0];
        	// TODO options
        	input.Add(mrl);
        	vlm.cmd("setup "+id+" input \""+mrl+"\"");
        	for(int i=1; i<p.Length; i++)
        	{
        		string[] kv = p[i].Split('=');
        		option(kv[0], kv[1]);
        	}
        }

        public void deleteinput(string i)
		{
        	// TODO
        	int n=0;
        	vlm.cmd("setup "+id+" inputdeln "+n);
        }
        
        public void option(string key, string val)
		{
        	option_f[key] = val;
        	vlm.cmd("setup "+id+" option "+key+"="+val);
        }
    }

    class VLMVOD : VLMMedia
    {
        public VLMVOD(string id, SifVLM vlm):base(id,vlm)
        {
        	
        }
    }

    class VLMBroadcast : VLMMedia
    {
        private string output_f;
        private bool loop_f;
        public string mux;

        public VLMBroadcast(string id, SifVLM vlm):base(id,vlm)
        {
			vlm.cmd("new "+this.id+" broadcast");
            output = "";
            loop = false;
            mux = "";
        }

        public void play()	{ control("play");}
        public void pause()	{ control("pause");}
        public void stop()	{ control("stop");}
        public void seek(int percent) { control("seek "+percent);}

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
	        	vlm.cmd("setup "+id+" output "+mrl);
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
        		    vlm.cmd("setup "+id+" loop");
	            else
		            vlm.cmd("setup "+id+" unloop");
	        }
        }

        private void control(string cmd)
		{
        	vlm.cmd("control "+id+" "+cmd);
        }
    }

    class VLMSchedule
    {
        public bool enabled;
        public List<string> command;
        public DateTime date;
        public TimeSpan period;
        public uint repeat;
        
        public VLMSchedule()
        {
        	enabled=false;
        	command=new List<string>();
        	date=new DateTime();
        	period = new TimeSpan();
        	repeat = 0;
        }
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
        	Console.WriteLine(cmd);
            XmlDocument resp = fetch(url + "vlm_cmd.xml?command="+HttpUtility.UrlPathEncode(cmd));
            string err = resp.GetElementsByTagName("error")[0].InnerText;
            if (err != "")
                Console.WriteLine(err);
        }
    }
}
