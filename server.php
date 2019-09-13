<?
// No Timeout
set_time_limit(0);

/* Set variables "host" and "port" */
$host = '127.0.0.1'; //host
/* open port firewall windows 10 https://pureinfotech.com/open-port-firewall-windows-10/  */
$port = '25003'; //port, Port number can be any positive integer between 1024 -65535.

try {

    //Create Socket
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if(!is_resource($socket)){
        throw new Exception('Unable to create socket: '.socket_strerror(socket_last_error($socket)));
    }

    // Reports whether local addresses can be reused.
    if(!socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)){
        throw new Exception('Unable to set option on socket: '.socket_strerror(socket_last_error($socket)));
    }

    //Bind the socket to port and host
    if(socket_bind($socket, 0, $port) === false){
        throw new Exception('Unable to bind socket: '.socket_strerror(socket_last_error($socket)));
    }

    $setOpt = socket_get_option($socket, SOL_SOCKET, SO_REUSEADDR);
    if ($setOpt === false) {
        throw new Exception( 'Unable to get socket option: '.socket_strerror(socket_last_error($socket)));
    }

    //Start listening to the socket
    if(socket_listen($socket) === false){
        throw new Exception( 'Unable to to listen socket: '.socket_strerror(socket_last_error($socket)));
    }

    //Create for multiple Client
    $clients = array($socket);

    $write = NULL; //null var
    $except = NULL; //null var

    //Start endless loop from for Client sockets
    while (true) {

        //Manage multiple Client
        $changed = $clients;

        //Get a list of all the socket resources in $changed array that have data to be read from
        $changed_sockets = socket_select($changed, $write, $except, 0, 10);
        if ($changed_sockets === false) {
            throw new Exception( 'Unable to socket select: '.socket_strerror(socket_last_error($socket)));
        }

        //Check for new socket
        if (in_array($socket, $changed)) {

            $socket_new = socket_accept($socket); //Accept incoming connection

            $clients[] = $socket_new; //Add socket to client array

            $header = socket_read($socket_new, 1024); //Read the message from the Client socket

            perform_handshaking($header, $socket_new, $host, $port); //Perform websocket handshake

            socket_getpeername($socket_new, $ip); //Get ip address of connected socket

            $response = mask(json_encode(array('type' => 'system', 'message' => $ip . ' connected'))); //Prepare json data

            send_message($response); //Notify all client about new client

            //Make room for new socket
            $found_socket = array_search($socket, $changed);
            unset($changed[$found_socket]);
        }

        //Loop through all connected sockets
        foreach ($changed as $c => $changed_socket) {

            //Check for Receives data from a connected socket
            while (socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
                $received_text = unmask($buf); //unmask data
                $tst_msg = json_decode($received_text); //json decode
                $user_name = $tst_msg->name; //sender name
                $user_message = $tst_msg->message; //message text
                $user_color = $tst_msg->color; //color
                $user_align = ($c%2)?'left':'right'; //left or right

                //prepare data to be sent to client
                $response_text = mask(json_encode(array('type' => 'usermsg', 'name' => $user_name, 'message' => $user_message, 'color' => $user_color, 'align' => $user_align)));
                send_message($response_text); //send data
                break 2; //exist this loop
            }

            //Check disconnected socket
            $buf = socket_read($changed_socket, 1024, PHP_NORMAL_READ);
            if ($buf === false) {

                //Remove client for $clients array
                $found_socket = array_search($changed_socket, $clients);
                socket_getpeername($changed_socket, $ip);
                unset($clients[$found_socket]);

                //Notify all users about disconnected connection
                $response = mask(json_encode(array('type' => 'system', 'message' => $ip . ' disconnected')));
                send_message($response);
            }
        }
    }
// close the listening socket
    socket_close($socket);

}catch (Exception $e){
    echo $e->getMessage();
}

function send_message($msg)
{
    global $clients;
    foreach ($clients as $changed_socket) {
        //Send message to the client socket
        @socket_write($changed_socket, $msg, strlen($msg));
    }
    return true;
}
//Unmask incoming framed message
function unmask($text) {
    $length = ord($text[1]) & 127;
    if($length == 126) {
        $masks = substr($text, 4, 4);
        $data = substr($text, 8);
    }
    elseif($length == 127) {
        $masks = substr($text, 10, 4);
        $data = substr($text, 14);
    }
    else {
        $masks = substr($text, 2, 4);
        $data = substr($text, 6);
    }
    $text = "";
    for ($i = 0; $i < strlen($data); ++$i) {
        $text .= $data[$i] ^ $masks[$i%4];
    }
    return $text;
}
//Encode message for transfer to client.
function mask($text)
{
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($text);

    if($length <= 125)
        $header = pack('CC', $b1, $length);
    elseif($length > 125 && $length < 65536)
        $header = pack('CCn', $b1, 126, $length);
    elseif($length >= 65536)
        $header = pack('CCNN', $b1, 127, $length);
    return $header.$text;
}

//handshake new client.
function perform_handshaking($receved_header,$client_conn, $host, $port)
{
    $headers = array();
    $lines = preg_split("/\r\n/", $receved_header);
    foreach($lines as $line)
    {
        $line = chop($line);
        if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
        {
            $headers[$matches[1]] = $matches[2];
        }
    }
    $secKey = $headers['Sec-WebSocket-Key'];
    $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
    //hand shaking header
    $upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "WebSocket-Origin: $host\r\n" .
        "WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
        "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
    socket_write($client_conn,$upgrade,strlen($upgrade));
}