import logging

import paho.mqtt.client as mqtt


class MqttReceiver(mqtt.Client):

    def __init__(self, client_id="", clean_session=True, userdata=None, protocol=mqtt.MQTTv311, transport="tcp"):
        super().__init__(client_id, clean_session, userdata, protocol, transport)
        self.logger = logging.getLogger(__name__)
        self.dataloggerDataTopic = ""

    def on_connect(self, mqttc, obj, flags, rc):
        self.logger.debug("rc: " + str(rc))

    def on_publish(self, mqttc, obj, mid):
        self.logger.debug("mid: " + str(mid))

    def on_subscribe(self, mqttc, obj, mid, granted_qos):
        self.logger.info("Subscribed: " + str(mid) + " " + str(granted_qos))

    def superRun(self, username, password, server, port, dataloggerDataTopic):
        self.logger.info("Connect to MqTT Broker")
        self.dataloggerDataTopic = dataloggerDataTopic
        self.username_pw_set(username, password)
        self.connect(server, port, 60, bind_address="")
        self.subscribe(dataloggerDataTopic, 0)
        self.loop_start()
