using System;
using System.Threading;
using System.Runtime.Serialization.Json;
using System.IO;
using RabbitMQ.Client;

namespace SimplePublishSubscribe
{

	public class MessageConnection
	{
		public string exchangeName="sif";
		public IConnection conn;
		
		public MessageConnection(string host)
		{
            ConnectionFactory factory = new ConnectionFactory();
            conn = factory.CreateConnection( Protocols.FromEnvironment(), host, 5672);
		}
		public MessageConnection(IConnection conn)
		{
			this.conn = conn;
		}
	}

	public class Subscription
	{
		private Listener listener;
		public MessageConnection conn;
		
		public Subscription(MessageConnection conn, string key, MessageHandler msgh)
		{
			this.conn = conn;
			string queueName = System.Guid.NewGuid().ToString();
            listener = new Listener(conn.exchangeName, queueName, conn.conn);
            listener.MessageReceived += new MessageHandler(msgh);
            listener.listenFor(key);
            Console.WriteLine("listening on queue "+queueName+" for "+key);
		}
		
		public void listenFor(string key)
		{
			listener.listenFor(key);
		}

		public void listen()
		{
			listener.listen();
		}

		public void ignore(string key)
		{
			listener.ignore(key);
		}
		
        public void send(string routingKey, byte[] message)
        {
        	listener.send(routingKey, message);
        }

        public void send(string routingKey, Object o)
        {
        	listener.send(routingKey, ToBytes(o));
        }

        public static void FromBytes(byte[] message, Object o)
		{
			DataContractJsonSerializer ser = new DataContractJsonSerializer(o.GetType());
    		MemoryStream ms = new MemoryStream(message);
    		o = ser.ReadObject(ms);
		}

		public static byte[] ToBytes(Object o)
		{
			MemoryStream ms = new MemoryStream();
			DataContractJsonSerializer ser = new DataContractJsonSerializer(o.GetType());
			ser.WriteObject(ms, o);
			return ms.ToArray();
		}
	}
	
	public delegate void MessageHandler(object sender, string key, byte[] message);

    public class Client
    {
        protected string exchangeName;
        protected IConnection conn;
        protected IModel channel;

        public Client(string eName, string hostName, int portNumber)
        {
            ConnectionFactory factory = new ConnectionFactory();
            IProtocol protocol = Protocols.FromEnvironment();
            conn = factory.CreateConnection(protocol, hostName, portNumber);
            channel = conn.CreateModel();
            exchangeName = eName;
            channel.ExchangeDeclare(exchangeName, ExchangeType.Direct);
        }
        
        public Client(string eName, IConnection conn)
        {
        	this.conn = conn;
            channel = conn.CreateModel();
            exchangeName = eName;
            channel.ExchangeDeclare(exchangeName, ExchangeType.Direct);
        }

        public void send(string routingKey, byte[] message)
        {
            channel.BasicPublish(exchangeName, routingKey, null, message);
        }
    }

    public class Sender : Client
    {
        protected string routingKey;

        public Sender(string exchangeName, string key, IConnection conn)
            :             base(exchangeName, conn)
        {
        	routingKey = key;
        }

        public Sender(string exchangeName, string hostName, int portNumber, string key)
            :             base(exchangeName, hostName, portNumber)
        {
        	routingKey = key;
        }

        public void send(byte[] message)
        {
            send(routingKey, message);
        }
    }

    public class Listener : Client
    {
        public event MessageHandler MessageReceived;
        QueueingBasicConsumer consumer;
		string queueName;
        
        public Listener(string exchangeName, string queueName, IConnection conn)
            : base(exchangeName, conn)
        {
            channel.QueueDeclare(queueName);
            consumer = new QueueingBasicConsumer(channel);
            channel.BasicConsume(queueName, null, consumer);
			this.queueName = queueName;
        }

        public Listener(string exchangeName, string hostName, int portNumber, string queueName)
            : base(exchangeName, hostName, portNumber)
        {
            channel.QueueDeclare(queueName);
            consumer = new QueueingBasicConsumer(channel);
            channel.BasicConsume(queueName, null, consumer);
			this.queueName = queueName;
        }

		public void listenFor(string key)
		{
			Console.WriteLine("listening for "+key);
            channel.QueueBind(queueName, exchangeName, key, false, null);
		}

		public void ignore(string key)
		{
            channel.QueueUnbind(queueName, exchangeName, key, null);
		}
		
        public void listen()
        {
            while (true)
            {
                try
                {
                	Thread.Sleep(0);
                    RabbitMQ.Client.Events.BasicDeliverEventArgs e =
                    (RabbitMQ.Client.Events.BasicDeliverEventArgs)
                    consumer.Queue.Dequeue();
                    IBasicProperties props = e.BasicProperties;
                    // ... process the message
                    MessageReceived(this, e.RoutingKey, e.Body);
                    channel.BasicAck(e.DeliveryTag, false);
                }
                catch (RabbitMQ.Client.Exceptions.OperationInterruptedException ex)
                {
                    // The consumer was removed, either through
                    // channel or connection closure, or through the
                    // action of IModel.BasicCancel().
                    break;
                }
            }
        }
    }
}
