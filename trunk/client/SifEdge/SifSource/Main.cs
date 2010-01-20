using System;
using System.Xml;
using RabbitMQ.Client;

namespace Sif
{

    class SourceMainClass
    {
        public static void Main(string[] args)
        {
            ConnectionFactory factory = new ConnectionFactory();
            IConnection conn = factory.CreateConnection(Protocols.FromEnvironment(), "localhost", 5672);
            XmlDocument doc = new XmlDocument();
            doc.LoadXml("<sif><id></id><input></input><active>1</active><loop>0</loop></sif>");
            Source source = new Source(args[0], conn, args[1], doc.FirstChild);
        	source.run();
            //"http://ws13.dyndns.ws/sif", "Player 1", "sif-03", "analog2", false);
            Console.WriteLine(source.ToString());
        }
    }
}