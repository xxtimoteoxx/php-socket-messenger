<?php
// no Time Limit
set_time_limit(0);

//Set variables such as "host" and "port"
$host = '127.0.0.1';
$port = 5353; // Port number can be any positive integer between 1024 -65535.

// Create socket
$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
if(!$socket) {
    echo "socket_create() Fail to create:".socket_strerror($socket)."\n";
}

// Bind the socket to port and host
$result = socket_bind($socket,$host,$port);
if(!$result) {
    echo "socket_bind() Fail to bind:".socket_strerror($result)."\n";
}

// Start listening to the socket
$result = socket_listen($socket,4);
if(!$result) {
    echo "socket_listen() Fail to listen:".socket_strerror($result)."\n";
}

// Accept incoming connection
$spawn =  socket_accept($socket);
if(!$spawn) {
    echo "socket_accept() Fail to accept:".socket_strerror($spawn)."\n";
}

// Read the message from the Client socket
$input = socket_read($spawn, 1024);
if(!$input) {
    echo "socket_read() Fail to read:".socket_strerror($input)."\n";
}

// Reverse the message
$output  = strrev($input);

// Send message to the client socket
$write = socket_write($spawn, $output, strlen ($output));
if(!$input) {
    echo "socket_write() Fail to write:".socket_strerror($write)."\n";
}

// Close the socket
socket_close($spawn);
socket_close($socket);