
using System;
using System.Net.NetworkInformation;
using Mono.Zeroconf;

public class SifClient
{
    private static bool done = false;

    public static int Main(string [] args)
    {
        string type = "_http._tcp";

        ServiceBrowser browser = new ServiceBrowser();
        browser.ServiceAdded += OnServiceAdded;
        browser.ServiceRemoved += OnServiceRemoved;
        browser.Browse(type, "local");

        while (!done) {
            System.Threading.Thread.Sleep(1000);
        }
        return 0;
    }

    private static void OnServiceAdded(object o, ServiceBrowseEventArgs args)
    {
        if (args.Service.AddressProtocol == AddressProtocol.IPv4 && args.Service.Name.CompareTo("SIF Management")==0) {
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
        string url = "http://"+service.HostEntry.AddressList[0]+":"+service.Port;
        ITxtRecord record = service.TxtRecord;
        int record_count = record != null ? record.Count : 0;
        if (record_count > 0) {
            for (int i = 0, n = record.Count; i < n; i++) {
                TxtRecordItem item = record.GetItemAt(i);
                if (item.Key.CompareTo("path")==0)
                {
                    url += item.ValueString;
                }
            }
        }
        NetworkInterface[] nics = NetworkInterface.GetAllNetworkInterfaces();
        if (nics == null || nics.Length < 1)
        {
            Console.WriteLine("  No network interfaces found.");
            return;
        }

        PhysicalAddress address = nics[0].GetPhysicalAddress();
        foreach (NetworkInterface adapter in nics) {
            if (adapter.Description.CompareTo("eth0")==0)
                address = adapter.GetPhysicalAddress();
        }
        url += "/register?mac=" + address.ToString();
        Console.WriteLine(url);
        done = true;
    }


}

