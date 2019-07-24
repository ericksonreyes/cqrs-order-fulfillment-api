import datetime
def handle(event_name, context, happened_on, entity_type, entity_id, data):
    if event_name == "OrderWasPlaced":
        print('\033[32m', 'Python: [√]', datetime.datetime.now(), 'Let\'s pretend an email was sent to the customer telling him that the order was posted.')
    if event_name == "OrderWasAccepted":
        print('\033[32m', 'Python: [√]', datetime.datetime.now(), 'Let\'s pretend an email was sent to the customer telling him that the order was accepted.')
    if event_name == "OrderWasCancelled":
        print('\033[32m', 'Python: [√]', datetime.datetime.now(), 'Let\'s pretend an email was sent to the customer telling him that the order was cancelled.')
    if event_name == "OrderWasShipped":
        print('\033[32m', 'Python: [√]', datetime.datetime.now(), 'Let\'s pretend an email was sent to the customer telling him that the order was shipped.')
    if event_name == "OrderWasShipped":
        print('\033[32m', 'Python: [√]', datetime.datetime.now(), 'Let\'s pretend an email was sent to the customer thanking him and the order was completed.')