<?php

$dispatcher = FastRoute\simpleDispatcher(require __DIR__ . "/routes.php");

$httpMethod = $_SERVER["REQUEST_METHOD"];
$uri = $_SERVER["REQUEST_URI"];

if (false !== ($pos = strpos($uri, "?"))) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

require __DIR__ . "/dispatch.php";
dispatch($dispatcher, $httpMethod, $uri);
