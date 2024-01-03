<?php

namespace Kernel\HttpEngine;

class HttpRequest
{
    private mixed $request;
    private mixed $files;
    private array $error;
    private string $route;
    private string $method;
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
        if (empty($_POST)) {
            try {
                $stream = fopen("php://input", "r");
                $this->request = stream_get_contents($stream);
            } catch (Exception $e) {
                $error[] = $e;
            } finally {
                fclose($stream);
            }
        } else {
            $this->request = $_POST;
            unset($_POST);
        }
        if (!empty($_FILES)) {
            $this->files = $_FILES;
            unset($_FILES);
        }
        $this->route = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function toArray()
    {
        return is_array($this->request) ? $this->request : json_decode($this->request, true);
    }

    public function getFiles()
    {
        return $this->files;
    }
}