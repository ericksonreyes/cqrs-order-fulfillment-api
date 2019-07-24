import pika, json, datetime
from multiprocessing import Process


class Listener:

    def __init__(self, amqp_settings):
        self.amqp_settings = amqp_settings
        self.handlers = []

    def __open_connection(self):
        virtual_host = self.amqp_settings['virtual_host']
        host = self.amqp_settings['host']
        port = self.amqp_settings['port']
        username = self.amqp_settings['username']
        password = self.amqp_settings['password']

        credentials = pika.PlainCredentials(username, password)
        parameters = pika.ConnectionParameters(host, port, virtual_host, credentials)
        return pika.BlockingConnection(parameters)

    def __open_channel(self, connection):
        exchange_name = self.amqp_settings['exchange_name']
        queue = self.amqp_settings['queue']
        exchange_type = self.amqp_settings['exchange_type']

        channel = connection.channel()
        channel.exchange_declare(exchange=exchange_name, exchange_type=exchange_type, durable=True)

        result = channel.queue_declare(queue=queue, exclusive=False, durable=True)
        queue_name = result.method.queue

        channel.queue_bind(exchange=exchange_name, queue=queue_name)
        channel.basic_consume(queue=queue_name, on_message_callback=self.__callback, auto_ack=False)

        return channel

    def __callback(self, ch, method, properties, body):
        ch.basic_ack(method.delivery_tag)
        event = json.loads(body)
        event_name = event['eventName']
        happened_on = event['happenedOn']
        entity_type = event['entityType']
        entity_id = event['entityId']
        data = event['data']

        context = 'Fulfillment'
        if 'context' in context:
            context = event['context'];

        print(
        '\033[33m', 'Python: [ ]', datetime.datetime.now(), context + '.' + entity_type + '.' + event_name + ' was raised.',
        '\033[0m')

        for handler in self.handlers:
            process = Process(target=handler, args=(event_name, context, happened_on, entity_type, entity_id, data))
            process.daemon = True
            process.start()

    def add_handler(self, handler):
        self.handlers.append(handler)

    def listen(self):
        print ('\033[33m', 'Creates projections from domain events.', '\033[0m')
        while True:
            try:
                connection = self.__open_connection()
                channel = self.__open_channel(connection)

                print(' Python: [*] Waiting for events. To exit press CTRL+C')
                try:
                    channel.start_consuming()
                except KeyboardInterrupt:
                    print('\033[91m', 'Python: [x] Terminated', '\033[0m')
                    channel.stop_consuming()
                    connection.close()
                break
            except pika.exceptions.ConnectionClosedByBroker:
                print('\033[91m', "Python: [x] ", datetime.datetime.now(), "Connection was closed, retrying...", '\033[0m')
                pass
            except pika.exceptions.AMQPChannelError as err:
                print(
                    '\033[91m', "Python: [x] ", datetime.datetime.now(), "Caught a channel error: {}, stopping...".format(err),
                    '\033[0m')
                break
            except pika.exceptions.AMQPConnectionError:
                print('\033[91m', "Python: [x] ", datetime.datetime.now(), "Connection was closed, retrying...", '\033[0m')
                continue