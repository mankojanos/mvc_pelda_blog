<?php
require_once __DIR__ . '/../global/ViewKezelo.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/UserAbKezelo.php';
require_once __DIR__ . '/../controller/AlapController.php';

class UserekController extends AlapController {

    /**
     * user ab kezelo peldanya
     * @var UserAbKezelo
     */
    private $userAbKezelo;

    public function __construct()
    {
        parent::__construct();
        $this->userAbKezelo = new UserAbKezelo();
        $this->view->setLayout('index');;
    }

    /**
     * A user ellenorzese
     * HA GET request erkezik akkor a login formot jelenitjuk meg
     * HA POST request erkezik akkor beleptetjuk (validalas utan)
     */
    public function belep() {
    if(filter_input(INPUT_POST, 'usernev')) {
        $usernev = filter_input(INPUT_POST, 'usernev', FILTER_SANITIZE_SPECIAL_CHARS);
        if($this->userAbKezelo->validEAUser($usernev, filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_SPECIAL_CHARS))) {
            $_SESSION['jelenlegiuser'] = $usernev;
            $this->view->atiranyitas('posztok', index);
        } else {
            $errors = array();
            $errors['usernev'] = 'A user nem jo';
            $this->view->setValtozo('errors', $errors);
        }
    }
    $this->view->render('userek', 'belep');
    }

    /**
     * Regisztracio
     * Ha GET request erkezik az oldalt jelenitjuk meg
     * HA POST request erkezik akkor regisztral az oldalon
     */
    public function regisztracio() {
        $user = new User();

        if(filter_input(INPUT_POST, 'usernev')) {
            $usernev = filter_input(INPUT_POST, 'usernev', FILTER_SANITIZE_SPECIAL_CHARS);
            $user->setUsernev($usernev);
            $user->setPasswd(filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_SPECIAL_CHARS));

            try {
                $user->regisztracioEllenorzese();
                if(!$this->userAbKezelo->usernevLetezik($usernev)) {
                    $this->userAbKezelo->mentes($user);
                    $this->view->setMessageSession('A user: ' . $user->getUsernev() . ' sikeresen felveve. Kerem jelenetekezzen be!');
                    $this->view->atiranyitas('userek', 'belep');
                } else {
                    $errors = [];
                    $errors['usernev'] = 'a felhasznalo mar letezik';
                  $this->view->setValtozo('errors', $errors );
                }
            } catch (ValidacioException $e) {
                $this->view->setValtozo('errors', $e->getErrors());
            }
        }

        $this->view->setValtozo('user', $user);
        $this->view->render('userek', 'regisztracio');
    }

    public function kilep() {
        session_destroy();
        $this->view->atiranyitas('userek', 'belep');
    }

}
