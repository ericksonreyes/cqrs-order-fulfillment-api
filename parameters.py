import yaml, os

# Get configuration from parameters.yml
with open("config/parameters.yml", 'r') as stream:
    config = yaml.safe_load(stream)['parameters']

amqp_config = {
    'virtual_host': config['rabbitmq_vhost'],
    'exchange_name': config['rabbitmq_exchange_name'],
    'host': config['rabbitmq_host'],
    'port': config['rabbitmq_port'],
    'username': config['rabbitmq_username'],
    'password': config['rabbitmq_password'],
    'queue': config['rabbitmq_queue'],
    'auto_ack': False,
    'exchange_type': 'fanout'
}