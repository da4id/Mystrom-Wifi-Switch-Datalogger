import json
import logging.config
import os
import time
from datetime import datetime
import sys
import signal

from ShellyMqttAdapter import ShellyMqttAdapter

Server = "192.168.12.16"
Port = 1883
User = "david"
Password = "j4BURx801Avq9UuUXpoV"
EnergyTopic = "shellies/shelly1pm-C49DF4/relay/0/energy"
PowerTopic = "shellies/shelly1pm-C49DF4/relay/0/power"
DEVICE_NAME = "C49DF4"
ID = "ShellyAdapter" + str(datetime.now())

def setup_logging(
        default_path=os.path.dirname(os.path.abspath(__file__)) + '/logging.json',
        default_level=logging.INFO,
        env_key='LOG_CFG'
):
    """Setup logging configuration

    """
    path = default_path
    value = os.getenv(env_key, None)
    if value:
        path = value
    if os.path.exists(path):
        with open(path, 'rt') as f:
            config = json.load(f)
        logging.config.dictConfig(config)
    else:
        print("use default config, no config file found")
        logging.basicConfig(level=default_level)

def signal_term(signal, frame):
    dataloggerHttpAdapter.loop_stop(True)
    dataloggerHttpAdapter.stopTimer = True
    logging.shutdown()
    sys.exit(0)

if __name__ == "__main__":
    signal.signal(signal.SIGTERM, signal_term)
    signal.signal(signal.SIGINT, signal_term)
    setup_logging()

    dataloggerHttpAdapter = ShellyMqttAdapter(ID, clean_session=True)
    dataloggerHttpAdapter.run(User, Password, Server, Port, PowerTopic, EnergyTopic, DEVICE_NAME)


    while (True):
        time.sleep(3600)
