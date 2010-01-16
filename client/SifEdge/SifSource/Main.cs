using System;
using RabbitMQ.Client;

namespace SifSource
{

    class MainClass
    {
        public static void Main(string[] args)
        {
            ConnectionFactory factory = new ConnectionFactory();
            IConnection conn = factory.CreateConnection(Protocols.FromEnvironment(), "localhost", 5672);

            Source source = new Source(args[0], args[1], args[2], args[3], conn);
        	source.run();
            //"http://ws13.dyndns.ws/sif", "Player 1", "sif-03", "analog2", false);
            Console.WriteLine(source.ToString());
        }
    }
}