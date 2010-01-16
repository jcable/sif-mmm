using System;

namespace SifClient
{
    class MainClass
    {
        public static int Main(string [] args)
        {
        	SifClient client;
			if(args.Length==1)
			{
            	client = new SifClient(args[0]);
			}
			else
			{
            	client = new SifClient();
			}
            Console.ReadKey();
            return 0;
        }
    }
}
