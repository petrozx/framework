<?php
namespace Kernel\Async;


class AsyncEngine
{
    public function __construct()
    {}

    public function getData($method, array $args)
    {
        $url = "tcp://$_SERVER[SERVER_ADDR]:$_SERVER[SERVER_PORT]";
        $sockets = [];
        foreach ($args as $arg) {
            $arg = serialize($arg);
            $socket = stream_socket_client($url, $errno, $errstr, 30);
            if (!$socket) {
                echo "Error connecting to $url: $errstr ($errno)\n";
                continue;
            }
            fwrite($socket, "GET /async?function=$method&arg=$arg HTTP/1.0\r\nHost: $_SERVER[SERVER_ADDR]\r\nAccept: */*\r\n\r\n");
            stream_set_blocking($socket, false);
            $sockets[$arg] = $socket;
        }
        while (!empty($sockets)) {
            $read = $sockets;
            stream_select($read, $write, $except, null);
        
            foreach ($read as $socket) {
                $url = array_search($socket, $sockets);
                $data = '';
        
                while ($chunk = fread($socket, 1024)) {
                    $data .= $chunk;
                }
                fclose($socket);
                $bodyStart = strpos($data, "\r\n\r\n");
                // Выделяем тело ответа, начиная с позиции конца заголовков
                $body = substr($data, $bodyStart + 4);
                // Обработка данных
                $returned[] = unserialize($body);
                unset($sockets[$url]);
            }
        }
        return $returned;
    }
}