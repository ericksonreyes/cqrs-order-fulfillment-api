import os
from dotenv import load_dotenv

# Get configuration from environment file
load_dotenv(dotenv_path="../../.env")

app_config = {
    'environment': os.getenv('APP_ENV'),
    'debug_enabled': os.getenv('APP_DEBUG'),
    'key': os.getenv('APP_KEY'),
    'timezone': os.getenv('APP_TIMEZONE'),
    'version': os.getenv('APP_VERSION')
}