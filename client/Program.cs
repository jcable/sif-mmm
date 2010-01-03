using System;
using RabbitMQ.Client;

namespace SimplePublishSubscribe
{
    class Program
    {
        private static void PrintMessage(object sender, byte[] message)
        {
            Console.WriteLine(System.Text.Encoding.UTF8.GetString(message));
        }

        static void Main(string[] args)
        {
            string hostName = "dev.rabbitmq.com";
            hostName = "192.168.2.137";
            int portNumber = 5672;
            string exchangeName = "sif";
            string routingKey = "studio1";
            if (args.Length > 0)
            {
                Console.WriteLine("listening");
                Listener listener = new Listener(exchangeName, hostName, portNumber, routingKey, args[0]);
                listener.MessageReceived += new MessageHandler(PrintMessage);
                listener.listen();
            }
            else
            {
                Console.WriteLine("sending");
                Sender sender = new Sender(exchangeName, hostName, portNumber, routingKey);
                while (true)
                {
                    byte[] messageBodyBytes = System.Text.Encoding.UTF8.GetBytes(Console.ReadLine());
                    sender.send(messageBodyBytes);
                }
            }
        }
    }
}
