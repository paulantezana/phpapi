<?php

class Customer extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('customers', 'id', $connection);
    }
}