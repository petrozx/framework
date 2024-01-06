<?php

class Test
{

    public static function do($arg)
    {
        sleep($arg);
        
        return ["ok" => $arg];
    }

}
   
