/*
Sif source

program to handle one logical source

can be an active or a standby instance

fetches a schedule from the server and translates to VLC/VLM syntax

listens for update messages
go active/standby
re-read schedule
publishes a dns-sd service for itself

controls a vlc instance
*/
using System;
using System.Text;
using System.Xml;
using System.Web;
using System.Net;
using System.Web.Services;
using System.IO;
using Mono.Zeroconf;

public class SifSource
{
    public static int Main(string [] args)
    {
        return 0;
    }
}
