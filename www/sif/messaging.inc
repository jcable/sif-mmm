<?php

require_once('amqp.inc');

class Sender
{
	public $conn;
	public $ch;
	public $exchange;

	public function __construct()
	{
		$BROKER_HOST   = 'dev.rabbitmq.com';
		$BROKER_PORT   = 5672;
		$USER     ='guest';
		$PASSWORD ='guest';

		$this->exchange = "sif";

    		$this->conn = new AMQPConnection($BROKER_HOST, $BROKER_PORT, $USER, $PASSWORD);
    		$this->ch = $this->conn->channel();
    		$this->ch->access_request('/', false, false, true, true);
   	 
    		$this->ch->exchange_declare($this->exchange, 'direct', false, false, false);
	}

	public function send($routingkey, $msg_body)
	{
    		$msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain'));
    		$this->ch->basic_publish($msg, $this->exchange, $routingkey);
	}

	public function close()
	{
    		$this->ch->close();
    		$this->conn->close();
	}
}
?>
