import time
from time import sleep

from DbConnection import DbConnection
from mySwitch import MySwitch

DEVICE_NAME = "Testdevice"
DEVICE_IP = "192.168.1.52"

current_milli_time = lambda: int(round(time.time() * 1000))

if __name__ == "__main__":
    dbConnection = DbConnection()

    dbIdTestDevice = dbConnection.findDataloggerDbIdByName(DEVICE_NAME)

    print("Device dbId", dbIdTestDevice)

    dbIdSeries = dbConnection.createNewSeries(dbIdTestDevice)

    print("Series dbId", dbIdSeries)

    dbConnection.createMeasurement(dbIdSeries, 1, 2, 1)

    switch = MySwitch(DEVICE_IP)

    energy = 0
    t = current_milli_time()

    while (True):
        values = switch.getReport()
        dT = current_milli_time() - t
        t = current_milli_time()
        power = values["power"]
        energy = energy + dT * power / (1000 * 3600)
        dbConnection.createMeasurement(dbIdSeries, power, energy, dT)
        print("Power", power, "Energy", energy, "dT", dT)

        sleep(1)
