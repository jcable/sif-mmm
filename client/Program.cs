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
            m1(args);
        }
        static void m1(string[] args)
        {
            string hostName = "dev.rabbitmq.com";
            hostName = "192.168.2.137";
            hostName = args[0];
            int portNumber = 5672;
            string exchangeName = args[1];
            string routingKey = args[2];
            Sender sender = new Sender(exchangeName, hostName, portNumber, routingKey);
            byte[] messageBodyBytes = System.Text.Encoding.UTF8.GetBytes(args[3]);
            sender.send(messageBodyBytes);
        }
        static void m2(string[] args)
        {
            string hostName = "dev.rabbitmq.com";
            hostName = "192.168.2.137";
            hostName = "localhost";
            int portNumber = 5672;
            string exchangeName = "sif";
            string routingKey = "Player 1";
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
