import requests
import json

class MySwitch:
    def __init__(self,ip):
        self.ip = ip

    def getReport(self):
        r = requests.get("http://"+self.ip+"/report")
        if(r.status_code == 200):
            return json.loads(r.text)
        else:
            raise Exception("Fehler bei Report abfrage")

