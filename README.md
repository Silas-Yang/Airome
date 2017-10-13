# Airome
Multi-user remote control system based on Arduino.

Airome enable your eletrical appliances to connect with the internet, and make them smarter.

## Hardware

There are four types of nodes: gateway, infrared node, switch node, temperature and humidity node, based on Arduino. The circuit diagrams of these nodes are in folder `Arduino/Airome Circuit Diagram/`.

1. Gateway connects the internet and the wireless sensor network of the nodes. It can transfer data between server and the Wireless Sensor Network (WSN);
2. Infrared node can transmit infrared signal to control airconditioner.
3. Temperature and humidity node can indicate temperature and humidity in the air.
4. Switch node consists of relay and other electrical appliance. eg. electrical kettle can be control remotely if connected with this switch node.


## Web Application

This GUI is developed with PHP based on a simple MVC architecutre, using MySQL as its database.

It contains user module and node management.

It will require user to register their gateway node, so the server can differentiate between different WSN and send the corresponding commands.

## Node Server

This program provides service for nodes, based on GateWayworker, a PHP framework providing TCP connection.

Gateway nodes will connect to this server.

Web server transfers users' command through a socket that connects to the node server, and then this node server control the nodes by communicating with gateway node with base64.
