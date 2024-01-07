<?php
namespace Kernel\Async;

class AsyncEngine
{
    private $url;
    private $activeConnections;
    private $responses = [];
    private static $instance;
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->url = "tcp://$_SERVER[SERVER_ADDR]:$_SERVER[SERVER_PORT]";
    }

    public function getData($method, $args)
    {
        foreach ($args as $key => $arg) {
            $this->addRequest($method, $arg, $key);
        }
        $this->waitForResponses();
        return $this->getResponses();
    }

    private function addRequest($method, $arg, $key)
    {
        $arg = serialize($arg);
        $connection = stream_socket_client($this->url, $errno, $errstr, 30);
        
        if (!$connection) {
            echo "Error connecting to $this->url: $errstr ($errno)\n";
            return;
        }

        stream_set_blocking($connection, 0);

        $request = "GET /async?function=$method&arg=$arg HTTP/1.0\r\nHost: $_SERVER[SERVER_ADDR]\r\nAccept: */*\r\n\r\n";
        fwrite($connection, $request);

        $this->activeConnections[$key] = $connection;
    }

    private function waitForResponses()
    {
        $timeout = 10000; // Ограничение в 10 секунд для чтения

        while (!empty($this->activeConnections) && $timeout > 0) {
            $read = $this->activeConnections;
            $write = null;
            $except = null;

            if (stream_select($read, $write, $except, 0, 10000) > 0) {
                foreach ($read as $socket) {
                    $data = fread($socket, 1024);

                    if ($data === false || $data === '') {
                        // Сокет закрыт или произошла ошибка
                        fclose($socket);
                        $index = array_search($socket, $this->activeConnections);
                        unset($this->activeConnections[$index]);
                    } else {
                        $bodyStart = strpos($data, "\r\n\r\n");
                        // Выделяем тело ответа, начиная с позиции конца заголовков
                        $data = substr($data, $bodyStart + 4);
                        // Обработка данных
                        $this->responses[] = unserialize($data);
                        $index = array_search($socket, $this->activeConnections);
                        unset($this->activeConnections[$index]);
                    }
                }
            }

            $timeout--;
        }

        // Закрываем все соединения после выполнения всех запросов
        foreach ($this->activeConnections as $socket) {
            fclose($socket);
        }
        $this->activeConnections = [];
    }

    public function getResponses()
    {
        return $this->responses;
    }
}
