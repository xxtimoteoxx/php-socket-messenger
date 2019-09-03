<?php
// no Time Limit
set_time_limit(0);

//Set variables such as "host" and "port"
$host = '127.0.0.1';
$port = 5353; // Here the port and host should be same as defined in server.

// Create socket
$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
if(!$socket) {
    echo "socket_create() Fail to create:".socket_strerror($socket)."\n";
}

// Connect to the server
$result = socket_connect($socket, $host, $port);
if(!$result) {
    echo "socket_connect() Fail to connect:".socket_strerror($result)."\n";
}

//Write to server socket
$message = "Hello!";
$write = socket_write($socket, $message, strlen($message));
if(!$write) {
    echo "socket_write() Fail to connect:".socket_strerror($write)."\n";
}

//Read the response from the server
$result = socket_read ($socket, 1024) or die("Could not read server response\n");
if(!$result) {
    echo "socket_read() Fail to connect:".socket_strerror($result)."\n";
}
echo "Reply From Server  :".$result;

// Close the socket
socket_close($socket);