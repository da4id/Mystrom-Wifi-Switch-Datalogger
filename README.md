# Mystrom-Wifi-Switch-Datalogger
power and energy datalogger for mystrom wifi switch

Python Datalogger for mystrom wifi switch

mystrom wifi switch provides power data but not consumed energy. This will be calculated using the python script. Therefore its not accurate.

In the mysql folder you can find the script to setup your database
you need to change to config.json files under PHP and Python folders with your Database informations
the you need manualy craete a Datalogger in the mysql database. The Dataloggername and IP Adress of the switch must be set in the myStrom.py file

Every Time you start the python script a new Series is generated.

The php script is used to visualize the data. Under "Logger und Series" you can change the Logger and Series you want to view. 

Have fun!
