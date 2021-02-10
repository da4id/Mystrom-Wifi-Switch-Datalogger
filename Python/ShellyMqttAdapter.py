import logging
from decimal import Decimal

import paho.mqtt.client as mqtt
from DbConnection import DbConnection
from MqttReceiver import MqttReceiver


class ShellyMqttAdapter(MqttReceiver):

    def __init__(self, client_id="", clean_session=True, userdata=None, protocol=mqtt.MQTTv311, transport="tcp"):
        super().__init__(client_id, clean_session, userdata, protocol, transport)
        self.logger = logging.getLogger(__name__)
        self.enable_logger(self.logger)
        self.httpPostUrl = ""
        self.stopTimer = False

        self.lastEnergy = None
        self.lastPower = None
        self.dbIdSeries = None
        self.dbIdTestDevice = None

        self.isFirstMeasurment = True
        self.lastLoggedEnergy = 0

    def checkAndLog(self):
        if self.lastEnergy is not None and self.lastPower is not None:
            try:
                dbConnection = DbConnection()
                if self.isFirstMeasurment or self.lastLoggedEnergy > self.lastEnergy:
                    self.dbIdSeries = dbConnection.createNewSeries(self.dbIdTestDevice)
                    self.logger.info("Create New Series dbId %s after Shelly Reboot" % self.dbIdSeries)
                self.isFirstMeasurment = False
                self.lastLoggedEnergy = self.lastEnergy
                dbConnection.createMeasurement(self.dbIdSeries, self.lastPower, self.lastEnergy, 1)
                dbConnection.close()
            except Exception as e:
                self.logger.warning(e)
            self.lastPower = None
            self.lastEnergy = None

    def on_message(self, mqttc, obj, msg):
        self.logger.info(msg.topic + " " + str(msg.qos) + " " + str(msg.payload))
        payload = msg.payload.decode("utf-8")
        if msg.topic == self.dataloggerDataTopic:
            self.lastPower = Decimal(payload)
        if msg.topic == self.energyTopic:
            self.lastEnergy = Decimal(payload) / (60 * 1000)  # Von Wattminuten in Kilowattstunden
        self.checkAndLog()

    def run(self, username, password, server, port, powerTopic, energyTopic, deviceName):
        dbConnection = DbConnection()
        self.dbIdTestDevice = dbConnection.findDataloggerDbIdByName(deviceName)
        self.logger.info("Device dbId %s" % self.dbIdTestDevice)

        dbConnection.close()

        self.superRun(username, password, server, port, powerTopic)
        self.subscribe(energyTopic, 0)
        self.energyTopic = energyTopic
