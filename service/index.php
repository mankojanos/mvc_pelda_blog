<?php
try {
    require_once __DIR__ .'/Routing.php';

    $fajlok = scandir(__DIR__);
    foreach ($fajlok as $fajl) {
        if(preg_match('/.*Rest\\.php/', strltolwer($fajl))) {
            include_once __DIR__ . '/' .$fajl;
        }
    }

    $routing = Routing::getPeldany();
    $routing->trueCors("*", "origin, content-type, accept, authorization");
    $routing = $routing->routingRequestFeldolgozasa();

    if(!$routing) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 400 hiba');
        die();
    }
} catch (Exception $e) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 vegzetes hiba');
    header('Content-Type: application/json');
    die(json_encode(array("error" => $e->getMessage())));
}
