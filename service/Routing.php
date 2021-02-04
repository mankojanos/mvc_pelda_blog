<?php
class Routing {
    private static $routing_singleton = null;
    public static function getPeldany() {
        if(self::$routing_singleton == null) {
            self::$routing_singleton = new Routing();
        }
        return self::$routing_singleton;
    }

    private function __construct() {
        $this->cors = false;
    }

    private $routingArray = array();

    public function route($httpMethod, $url, $muvelet, $json = true) {
        array_push($this->routingArray, array(
            "http_metodika" => $httpMethod,
            "url_minta" => $url,
            "muvelet" => $muvelet,
            "json" => $json
        ));
    }

    public function trueCors($allowedOrigin, $acah) {
        $this->cors = true;
        $this->allowedOrigin = $allowedOrigin;
        $this->acah = $acah;
    }

    private function reques_azonositasa($http_metodika, $url, $parameterek = array()) {

    }

}
