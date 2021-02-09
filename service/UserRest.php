<?php
require_once __DIR__ . "/AlapRest.php";

class UserRest extends AlapRest {
    private $userAb;

    public function __construct() {
        $this->userAb = new UserAbKezelo();
    }

    public function regisztracio($adatok) {
        $user = new User($adatok->usernev, $adatok->passwd);
        try {
            $user->regisztracioEllenorzese();
            $this->userAb->mentes($user);
            header($_SERVER["SERVER_PROTOCOL"] . '201 Elkeszult');
            header("Location: " . $_SERVER['REQUEST_URI'] . '/' . $adatok->usernev);

        } catch (ValidacioException $e) {
            http_response_code(400);
            header('Content-Type: applicatio/json');
            echo(json_encode($e->getErrors()));
        }
    }

    public function belepes($usernev) {
        $jelenlegiBelepettUser = parent::validaljaAUsert();
        if($jelenlegiBelepettUser->getUsernev() != $usernev) {
            header($_SERVER['SERVER_PROTOCOL'] . '200 ok');
        }
    }
}

$userRest = new UserRest();
$route = Routing::getPeldany();
$route->route("GET", "/user/$1", array($userRest, 'belepes'));
$route->route('POST', '/user', array($userRest, 'regisztracio'));
