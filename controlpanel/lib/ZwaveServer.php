<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ZwaveServer
 *
 * @author conradv
 */
class ZwaveServer {

    //put your code here
    private $socket;

    function __construct($host, $port) {


        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false)
            echo "error";
        else {
            $result = socket_connect($this->socket, $host, $port);

            if ($result === false) {
                echo "error " . socket_strerror(socket_last_error($this->socket));
                
            } 
        }
    }

    function read() {
        return socket_read($this->socket, 2048);
    }

    function send($data) {
        socket_write($this->socket, $data, strlen($data.chr(0)));
    }

    function close() {
        socket_close($this->socket);
    }

}

?>
