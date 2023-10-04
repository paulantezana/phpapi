<?php
    require_once(__DIR__ . '/Exceptions/ControlledException.php');
    require_once(__DIR__ . '/Core/Model.php');
    require_once(__DIR__ . '/Database.php');
    require_once(__DIR__ . '/settings.php');

    $router = new Router();
    $router->run();
