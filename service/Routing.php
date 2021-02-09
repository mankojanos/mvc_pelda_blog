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
        $path = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['PHP_SELF']) - strlen(basename($_SERVER['PHP_SELF'])) - 1);
        $path = parse_url($path)['path'];
        if($_SERVER['REQUEST_METHOD'] != strtoupper($http_metodika) && ($this->cors == false || $_SERVER['REQUEST_METHOD'] != 'OPTIONS')) {
            return false;
        }
        $pathTomb = explode('/', $path);
        $urlTomb = explode('/', $url);
        if(count($pathTomb) !== count($urlTomb)) {
            return false;
        }

        $length = count($pathTomb);
        for($i=0; $i < $length; $i++) {
            if($pathTomb[$i] !== $urlTomb[$i]) {
                if(preg_match('/\$([0-9]+?)/', $urlTomb[$i]) !== 1) {
                    return false;
                }
            }
        }
        return true;
    }

    public function routingRequestFeldolgozasa() {
        $tamogatottMetodika = array();
        foreach ($this->routingArray as $route) {
            $parameterek = array();
            if($this->reques_azonositasa($route['http_metodika'], $route['url_minta'], $parameterek)) {
                if($this->cors == true && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                    array_push($tamogatottMetodika, strtoupper($route['http_metodika']));
                }else {
                    if($route['json'] && isset($_SERVER['Content-Típe']) && strpos($_SERVER['Content-Type'], 'application/json') !== false) {
                        array_push($parameterek, json_decode(file_get_contents('php://input')));
                    }
                    if ($this->cors === true) {
                        header('Access-Control-Allow-Origin:', $this->allowedOrigin);
                    }
                    call_user_func_array($route['muvelet'], $parameterek);
                    return true;
                }
            }
        }
        if($this->cors) {
            header('Acces-Control-Allow-Origin', $this->allowedOrigin);
            header('Acces-Control-Allow-Headers', $this->acah);
            header('Acces-Control-Allow-Methods', implode(',', $tamogatottMetodika . ',OPTIONS'));
            return true;
        }

        return false;
    }

}
