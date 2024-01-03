<?php
namespace Kernel\HttpEngine;

class HttpResponse
{

    private static $instance;

    private function __construct(
        private mixed $data = null,
    ){}

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __destruct()
    {
        echo json_encode($this->data);
    }

    public function success($data, $message = '', $statusCode = 200)
    {
        header($_SERVER["SERVER_PROTOCOL"]." $statusCode");
        $this->data = [
            'error' => [],
            'data' => $data,
            'message' => $message,
        ];
    }

    public function fail($errors, $message = '', $statusCode = 400)
    {
        header($_SERVER["SERVER_PROTOCOL"]." $statusCode");
        $this->data = [
            'error' => $errors,
            'data' => null,
            'message' => $message,
        ];
    }

    public function __invoke($data)
    {
        $this->data = $data;
    }
}