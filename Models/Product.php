<?php

class Product extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('products', 'id', $connection);
    }
}