using System;
using RabbitMQ.Client;

namespace SimplePublishSubscribe
{
    public delegate void MessageHandler(object sender, string key, byte[] message);

    class Client
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
    }

    class Sender : Client
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
            channel.BasicPublish(exchangeName, routingKey, null, message);
        }
    }

    class Listener : Client
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
