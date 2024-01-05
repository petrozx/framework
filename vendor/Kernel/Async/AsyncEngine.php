<?php
namespace Kernel\Async;


class AsyncEngine
{

    private const URL = "tcp://192.168.88.243:80";
    private const HEADER = "GET /async HTTP/1.0\r\nHost: 192.168.88.243\r\nAccept: */*\r\n\r\n";
    public function __construct()
    {}

    public function getData($method, array $counts)
    {
        $url = self::URL;
        $sockets = [];
        $results = [];
        foreach ($counts as $count) {
            $socket = stream_socket_client($url, $errno, $errstr, 30);
    
            if (!$socket) {
                echo "Error connecting to $url: $errstr ($errno)\n";
                continue;
            }
            fwrite($socket, "GET /async?oper=$method&num=$count HTTP/1.0\r\nHost: 192.168.88.243\r\nAccept: */*\r\n\r\n");
            stream_set_blocking($socket, false);
            $sockets[$count] = $socket;
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
                $returned[] = json_decode($body);
        
                unset($sockets[$url]);
            }
        }
        return $returned;
    }
}