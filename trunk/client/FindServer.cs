
using System;

using Mono.Zeroconf;

public class MZClient 
{
    public static int Main(string [] args)
    {
        string type = "_http._tcp";
            
        ServiceBrowser browser = new ServiceBrowser();
        browser.ServiceAdded += OnServiceAdded;
        browser.ServiceRemoved += OnServiceRemoved;
        browser.Browse(type, "local");
       
        while(true) {
            System.Threading.Thread.Sleep(1000);
        }
    }
    
    private static void OnServiceAdded(object o, ServiceBrowseEventArgs args)
    {
        Console.WriteLine("*** Found name = '{0}', type = '{1}', domain = '{2}'", 
            args.Service.Name,
            args.Service.RegType,
            args.Service.ReplyDomain);
        
        //if(args.Service.Name.CompareTo("SIF Management")==0) {
        if(true) {
            args.Service.Resolved += OnServiceResolved;
            args.Service.Resolve();
        }
    }
    
    private static void OnServiceRemoved(object o, ServiceBrowseEventArgs args)
    {        
        Console.WriteLine("*** Lost  name = '{0}', type = '{1}', domain = '{2}'", 
            args.Service.Name,
            args.Service.RegType,
            args.Service.ReplyDomain);    
    }
    
    private static void OnServiceResolved(object o, ServiceResolvedEventArgs args)
    {
        IResolvableService service = o as IResolvableService;
        Console.Write("*** Resolved name = '{0}', port = '{1}', interface = '{2}'", 
            service.FullName, service.Port, service.NetworkInterface);
        foreach(System.Net.IPAddress j in service.HostEntry.AddressList) {
        	Console.Write("host = '{0}'", j);
	}
        
        ITxtRecord record = service.TxtRecord;
        int record_count = record != null ? record.Count : 0;
        if(record_count > 0) {
            Console.Write(", TXT Record = [");
            for(int i = 0, n = record.Count; i < n; i++) {
                TxtRecordItem item = record.GetItemAt(i);
                Console.Write("{0} = '{1}'", item.Key, item.ValueString);
                if(i < n - 1) {
                    Console.Write(", ");
                }
            }
            Console.WriteLine("]");
        } else {
            Console.WriteLine("");
        }
    }
    
}

