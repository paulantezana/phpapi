<?php

class PageController
{
    public function __construct(PDO $connection)
    {
        
    }

    public function home()
    {
        $res = new Result();
        $res->message = 'Hola Continental';
        $res->success = true;
        return $res;
    }
}