<?php

class ViewKezelo {
    /**
     * Az alapertelemeztt nzet kulcsa
     * @var string DEFAULT_VIEW
     */
    const DEFAULT_VIEW = "__default__";

    /**
     * pufferelt tartalom minden nezetreszhez
     * @var array $viewTartalmak
     */
    private $viewTartalmak = array();

    /**
     * Nezetvaltozok tombje
     * @var array $valtozok
     */
    private $valtozok = array();

    /**
     * a jelenlegi view ami megadja hogy melyik nézet lesz az aktualis
     * @var string $aktualisView
     */
    private $aktualisView = self::DEFAULT_VIEW;

    /**
     * Az adott layout neve, amit rendereleskor hasznalunk
     * @var string $layout
     */

    private $layout = 'alap';

    public function __construct() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        ob_start();
    }

    /**
     * a nezetet mentjuk a kimeneti pufferbol az adott nezetreszhez
     * muvelet vegen a puffert toroljuk
     */
    private function mentiAJelenlegiNezetet() {
        $this->viewTartalmak[$this->aktualisView] .= ob_get_contents();
        ob_clean();
    }

    /**
     * A valtozas elott le kell tarolni az outputot. Abban az esteben hivjuk meg ha az adott nezet,
     * vagy nezetresz megvaltozik
     * @param string $nev Nezetresz neve
     */
    public function ujNezet(string $nev) {
        $this->mentiAJelenlegiNezetet();
        $this->aktualisView = $nev;
    }

    /**
     * Az alapertelmezett nezetreszt tolti be
     */
    public function alapertelmezettNezetetToltiBe() {
        $this->ujNezet(self::DEFAULT_VIEW);
    }

    /**
     * Megadja egy megadott view reszben tarolt tartalmat
     * @param string $view A nezetresz ahonnan a tartalamat megkapjuk
     * @param string $default Opcionalis. Az alapertelemezett tartalom ha a nezetresz nem letezuk
     * @return string a nezetresz tartalma
     */

    public function getView(string $view, string $default = ''): string {
        if(!isset($this->viewTartalmak[$view])) {
            return $default;
        }
        return  $this->viewTartalmak[$view];
    }

    /**
     * A valtozo elerhetove tetele a view-n. Barmit atadhatunk
     * @param string $valtozonev a valtozo neve
     * @param mixed $ertek a valtozo erteke
     * @param bool $tarol Szeretnenk-e a session-ben tarrolni
     */
    public function setValtozo(string $valtozonev, $ertek, bool $tarol = false) {
        $this->valtozok[$valtozonev] = $ertek;
        if ($tarol === true) {
            $_SESSION['viewkezelo_valtozotomb'][$valtozonev] = $ertek;
        }
    }

    /**
     * A beallitott ertekek lekerese
     * Ha a valtozo a munkameneteben van tarolva akkor azt toroljuk
     * @param string $valtozonev a valtozo neve
     * @param mixed $default a valtozo erteke amit visszaadunk ha a valtozo nem letezik
     * @return mixed a valtozo erteke
     */
    public function getValtozo(string $valtozonev, $default = null) {
        if(!isset($this->valtozok[$valtozonev])) {
            if(isset($_SESSION['viewkezelo_valtozotomb']) && isset($_SESSION['viewkezelo_valtozotomb'][$valtozonev])) {
                $ertek = $_SESSION['viewkezelo_valtozotomb'][$valtozonev];
                unset($_SESSION['viewkezelo_valtozotomb'][$valtozonev]);
                return $ertek;
            }
            return $default;
        }
        return $this->valtozok[$valtozonev];
    }

    /**
     * A sessionben tarolhato uzenetek
     * ez gyakorlatilag a flash messagek egy light verzioja
     * @param string $message az uezenet amit el akarunk tarolni a munkamenetben
     */

    public function setMessageSession(string $message) {
        $this->setValtozo('_message', $message, true);
    }

    /**
     * A beallitott flash message light lekerese
     * @return string uzenet
     */
    public function getMessageSession() {
        return $this->setValtozo('_message', '');
    }

    /**
     * Beallitjuk a layoutot amit a renderelesnel hazsnalni fogunk
     * @param string $layout A hasznalni kivant layout
     * @return void
     */
    public function setLayout(string $layout) {
        $this->layout = $layout;
    }

    /**
     * Megadja a megadott controller altal hasznalt nezetet
     * @example HA a controllerunk a $controller=peldacontroller es a $view=peldavew a kivalaztott fajl a view/peldakontroller/peldaview.php
     * @param string $controller a controller nece (url kompatibilis verzioban
     * @param string $viewneve a megadott view neve
     */
    public function render(string $controller, string $viewneve) {
        include(__DIR__ . "/../view/$controller/$viewneve.php");
        $this->rendereliALayoutot();
    }

    /**
     * A megadott layout renderelese
     * alapvetoen egy import
     */
    private function rendereliALayoutot() {
        $this->ujNezet("layout");
        include_once(__DIR__ . '/../view//layoutok/' . $this->layout . '.php');
        ob_flush(); //kimenetri puffer tartalmanak torese
    }

    /**
     * Alpavetoen egy 302-es atiranyitas
     * @param string $controller a controller neve
     * @param string $muvelet a muvelet neve
     * @param string $parameterek opcionalisan parameterek (pl.: querystringek)
     */
    public function atiranyitas(string $controller, string $muvelet, string $parameterek = '' ) {
        header("Location: index.php?controller=$controller&muvelet=$muvelet" . isset($parameterek) ? "$parameterek" : '' );
        die();
    }

    /**
     * egy 302-es atiranyitas arra az oldalra amelyiken a user elozoleg volt a request elott
     * @param string $parameterek Opcionalisan querystring
     */
    public function visszairanyitas(string $parameterek = '') {
        header("Location: " . $_SERVER['HTTP_REFERER'] . $parameterek);
    }

    /**
     * Viewkezelo singleton
     */

    private static $viewkezelo_singleton = NULL;

    public static function getPeldany():ViewKezelo {
        if(self::$viewkezelo_singleton === NULL) {
            self::$viewkezelo_singleton = new ViewKezelo();
        }
        return self::$viewkezelo_singleton;
    }
}

/**
 * Kenyszeritenunk kell a ViewKezelo elso inicializalasat mivel a puferre es a sessionre akkor is szukseg van ha nincs controller
 */
ViewKezelo::getPeldany();
