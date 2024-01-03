<?php

namespace Kernel\Debug;

class Dump
{
    public static function echo($data)
    {
        echo '<pre style="background-color: #f4f4f4; padding: 10px; border: 1px solid #ddd; border-radius: 5px; overflow: auto; font-family: monospace;">';
        print_r($data);
        echo '</pre>';
    }
}