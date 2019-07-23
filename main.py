#!/usr/bin/env python3

from parameters import amqp_config
from env import *
from python.modules.listener import Listener
import python.mailers.order_email_notifier as OrderEmailer

listener = Listener(amqp_config)
listener.add_handler(OrderEmailer.handle)
listener.listen()