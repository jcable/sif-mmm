using System;
using RabbitMQ.Client;

namespace SimplePublishSubscribe
{
    public delegate void MessageHandler(object sender, byte[] message);

    class Client
    {
        protected string exchangeName;
        protected string routingKey;
        protected IConnection conn;
        protected IModel channel;

        public Client(string eName, string key, string hostName, int portNumber)
        {
            ConnectionFactory factory = new ConnectionFactory();
            IProtocol protocol = Protocols.FromEnvironment();
            conn = factory.CreateConnection(protocol, hostName, portNumber);
            channel = conn.CreateModel();
            exchangeName = eName;
            channel.ExchangeDeclare(exchangeName, ExchangeType.Direct);
            routingKey = key;
        }
        
        public Client(string eName, string key, IConnection conn)
        {
        	this.conn = conn;
            channel = conn.CreateModel();
            exchangeName = eName;
            channel.ExchangeDeclare(exchangeName, ExchangeType.Direct);
            routingKey = key;
        }
    }

    class Sender : Client
    {
        public Sender(string exchangeName, string key, IConnection conn)
            :             base(exchangeName, key, conn)
        {
        }

        public Sender(string exchangeName, string hostName, int portNumber, string key)
            :             base(exchangeName, key, hostName, portNumber)
        {
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
        
        public Listener(string exchangeName, string key, string queueName, IConnection conn)
            : base(exchangeName, key, conn)
        {
            channel.QueueDeclare(queueName);
            channel.QueueBind(queueName, exchangeName, routingKey, false, null);
            consumer = new QueueingBasicConsumer(channel);
            channel.BasicConsume(queueName, null, consumer);
			this.queueName = queueName;
        }

        public Listener(string exchangeName, string hostName, int portNumber, string key, string queueName)
            : base(exchangeName, key, hostName, portNumber)
        {
            channel.QueueDeclare(queueName);
            channel.QueueBind(queueName, exchangeName, routingKey, false, null);
            consumer = new QueueingBasicConsumer(channel);
            channel.BasicConsume(queueName, null, consumer);
			this.queueName = queueName;
        }

		public void alsoListenFor(string key)
		{
            channel.QueueBind(queueName, exchangeName, routingKey, false, null);
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
                    MessageReceived(this, e.Body);
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
