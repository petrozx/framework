<?php

namespace Entity;

class User
{
    private string $username;
    private string $pasword;
    
    public function __construct(string $username, string $pasword)
    {
        $this->username = $username;
        $this->pasword = $pasword;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPasword(): string
    {
        return $this->pasword;
    }
}