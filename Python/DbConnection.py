import mysql.connector
import json


class DbConnection:
    def __init__(self):
        self.dbConnection = self.createDbConnection()
        self.cursor = self.createDbCursor()

    def createDbConnection(self):
        with open('config.json') as json_data:
            d = json.load(json_data)

        return mysql.connector.connect(
            host=d["server"],
            user=d["user"],
            passwd=d["passwd"],
            database=d["database"]
        )

    def createDbCursor(self):
        return self.dbConnection.cursor()

    def executeSqlAndFetchAll(self, sql):
        self.cursor.execute(sql)
        return self.cursor.fetchall()

    def executeSqlAndFetchFirst(self, sql):
        self.cursor.execute(sql)
        return self.cursor.fetchmany(1)

    def findDataloggerDbIdByName(self, dataLoggerName):
        sql = "SELECT dbId FROM Datalogger where Name = '" + dataLoggerName + "'"
        myresult = self.executeSqlAndFetchFirst(sql)

        if (len(myresult) == 0):
            raise Exception("Kein passendes Gerät gefunden")

        return myresult[0][0]

    def createNewSeries(self, dbIdDatalogger):
        sql = "INSERT INTO Series (dbidDatalogger) VALUES (" + str(dbIdDatalogger) + ")"

        self.cursor.execute(sql)
        self.dbConnection.commit()
        return self.cursor.lastrowid

    def createMeasurement(self, dbIdSeries, currentPower, energy, dT):
        sql = "INSERT INTO Measurements (dbIdSeries, CurrentPower, Energy,deltaT) VALUES (%s,%s,%s,%s)"
        val = (dbIdSeries, currentPower, energy, dT)

        self.cursor.execute(sql, val)
        self.dbConnection.commit()
