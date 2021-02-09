<?php
require_once __DIR__ . "/../model/User.php";
require_once __DIR__ . "/../model/UserAbKezelo.php";

class AlapRest {
    public function validaljaAUsert() {
        if(!isset($_SERVER ['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realme="Megbukott az azonositason"');
            header($_SERVER['SERVER_PROTOCOL'] . '401 azonositasi hiba');
            die();
        } else {
            $userAB = new UserAbKezelo();
            if ($userAB->validEAUser($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
                return new User($_SERVER['PHP_AUTH_USER']);
            } else {
                header('WWW-Authenticate: Basic realme="Megbukott az azonositason"');
                header($_SERVER['SERVER_PROTOCOL'] . '401 azonositasi hiba');
                die();
            }
        }
    }
}
