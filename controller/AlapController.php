<?php
require_once __DIR__ . '/../global/ViewKezelo.php';
require_once __DIR__ . '/../model/User.php';

class AlapController {

    /**
     * A viewkezelo peldanya
     * @var ViewKezelo
     */
    protected $view;

    /**
     * a user peldanya
     * @var User
     */
    protected $jelenlegiUser;

    public function __construct() {
        $this->view = ViewKezelo::getPeldany();
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if(isset($_SESSION['jelenlegiuser'])) {
            $this->jelenlegiUser = new User($_SESSION['jelenlegiuser']);
            $this->view->setValtozo('jelenlegiuser', $this->jelenlegiUser->getUsernev());
        }
    }
}
