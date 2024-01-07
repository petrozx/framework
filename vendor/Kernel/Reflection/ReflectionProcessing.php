<?php
namespace Kernel\Reflection;
use Kernel\Reflection\WorkingWithInterface;

abstract class ReflectionProcessing implements WorkingWithInterface
{

    private $arFiles = [];
    private $attributes = [];

    public function __construct()
    {
        $this->arFiles = $this->scanDirectory($_SERVER["DOCUMENT_ROOT"]."/src");
        $this->attributes = $this->searching();
    }

    private function searching()
    {
        $attributesInfo = [];
        foreach ($this->arFiles as $key => $fileInfo) {
            require_once($fileInfo['realpath']);
            if (class_exists($fileInfo['name'],false)) {
                $reflection = new \ReflectionClass($fileInfo['name']);
                $constructor = $reflection->getConstructor();
                if (isset($constructor)) {
                    foreach ($constructor->getParameters() as $param) {
                        $name = $param->name;
                        $instance = $param->getType()->getName()::getInstance();
                        $params[$name] = $instance;
                        
                    }
                }
                $attributesInfo[] = $this->getAttribute($reflection, $reflection->newInstance(...$params));
            }
        }
        return $attributesInfo;
    }

    private function getAttribute(\ReflectionClass $reflection, $instanceUserClass)
    {
        $attributes = [];
        if ($reflection->getName() !== "Response_404") {
            foreach ($reflection->getAttributes() as $classAttribute) {
                $arAttribute = [
                    'name'        => $classAttribute->getName(),
                    'args'        => $classAttribute->getArguments(),
                    'instance'    => $instanceUserClass,
                ];
                foreach ($reflection->getMethods() as $method) {
                    foreach ($method->getAttributes() as $methodAttribute) {
                        $arAttribute['methods'][] = [
                            'method'        => $method,
                            'name'          => $methodAttribute->getName(),
                            'args'          => $methodAttribute->getArguments(),
                        ];
                    }
                }
                $attributes[$classAttribute->getName()] = $arAttribute;
            }
        } else {
            $attributes['response_404'] = [
                'instance' => $instanceUserClass,
                'methods'  => [
                    'method' => new \ReflectionMethod($instanceUserClass, 'NOT_FOUND')
                ],
                'params' => [],
            ];
        }
        return $attributes;
    }

    private function scanDirectory($dir) {
        $files = [];
     
        // Проверяем, существует ли директория
        if (file_exists($dir) && is_dir($dir)) {
            // Получаем список файлов в директории
            $scanned = scandir($dir);
     
            // Проходимся по каждому элементу
            foreach ($scanned as $item) {
                // Пропускаем текущую (.) и родительскую (..) директории
                if ($item === '.' || $item === '..') {
                    continue;
                }
     
                // Формируем полный путь к элементу
                $fullPath = $dir . DIRECTORY_SEPARATOR . $item;

                if (is_dir($fullPath)) {
                    $files = array_merge($files, $this->scanDirectory($fullPath));
                }
                // Проверяем, является ли элемент файлом
                if (is_file($fullPath)) {
                    $files[] = [
                        "realpath" => $fullPath,
                        "name"     => str_replace(".php", "", $item),
                    ];
                }
            }
        }
     
        return $files;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }
}