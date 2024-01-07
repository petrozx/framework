<?php

use Entity\User;
class Test
{

    public static function do($arg)
    {
        sleep(1);
        $user = new User($arg, "123");
        return $user;
    }

}
   
