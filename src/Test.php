<?php

class Test
{

    public static function do($num)
    {
        sleep($num);
        
        return ["ok" => $num];
    }

}
   
