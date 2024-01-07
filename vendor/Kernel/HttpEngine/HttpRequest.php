<?php

namespace Kernel\HttpEngine;

class HttpRequest
{
    private mixed $files;
    private array $error;
    private string $route;
    private string $method;
    private static $instance;
    private array $get;
    private mixed $post;


    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->get = $_GET;
        unset($_GET);
        if (empty($_POST)) {
            try {
                $stream = fopen("php://input", "r");
                $this->post = stream_get_contents($stream);
            } catch (\Exception $e) {
                $error[] = $e;
            } finally {
                fclose($stream);
            }
        } else {
            $this->post = $_POST;
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

    public function toArray()
    {
        return is_array($this->post) ? $this->post : json_decode($this->post, true);
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function GET()
    {
        return $this->get;
    }

    public function POST()
    {
        return $this->post;
    }
}