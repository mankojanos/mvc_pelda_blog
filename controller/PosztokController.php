<?php
require_once __DIR__ . '/../model/Komment.php';
require_once __DIR__ . '/../model/Poszt.php';
require_once __DIR__ . '/../model/PosztABKezelo.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../global/ViewKezelo.php';
require_once __DIR__ . '/../controller/AlapController.php';

class PosztokController extends AlapController {

    /**
     * Poszt AB kezeloje
     * @var PosztABKezelo
     */
    private $posztABKezelo;

    public function __construct()
    {
        parent::__construct();
        $this->posztABKezelo = new PosztABKezelo();
    }

    /**
     * a posztopk listazasa
     * az osszes posztot lekerjuk
     */
    public function index() {
        $posztok = $this->posztABKezelo->osszesElem();
        $this->view->setValtozo('posztok', $posztok);
        $this->view->render('posztok', 'index');
    }

    /**
     * Ez a fgv GET reqestet kovetoen hivodik meg
     * Egy poszt megjeleniteshez haszanljuk
     * @throws Exception HA nincs ilyen poszt
     */
    public function reszletek() {
        if(!filter_input(INPUT_GET, 'id')) {
            throw new Exception('ID-t kotelezo megadni');
        }
        $posztid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
        $poszt = $this->posztABKezelo->idAlapjanKommentekkel($posztid);
        if($poszt === null) {
            throw new Exception(('nincs ilyen poszt ezze laz id-val: '. $posztid));
        }
        $this->view->setValtozo('poszt', $poszt);
        $komment = $this->view->getValtozo('komment');
        $this->view->setValtozo('komment', ($komment === null) ? new Komment() : $komment);
        $this->view->render('posztok', 'reszletek');
    }

    /**
     * A poszt felvetelhez hazsnaljuk
     * HA GET requesten keresztul hivodik meg, akkor felvesz oldalt jelenitunk meg
     * HA POST requesten keresztul hivodik meg, akkor az uj posztot felveszzuk az adatbazisba
     * @throws Exception Ha hiba lep fel, vagy nincs user a minkamenetben
     *
     */
    public function felvesz() {
        if(!isset($this->jelenlegiUser)) {
            throw new Exception(' a user nincs a munkamenetben. Be kell jelentkezni');
        }

        $poszt = new Poszt();
        if(filter_input(INPUT_POST, 'submit')) {
            $poszt->setCim(filter_input(INPUT_POST, 'cim', FILTER_SANITIZE_SPECIAL_CHARS));
            $poszt->setTartalom(filter_input(INPUT_POST,'tartalom', FILTER_SANITIZE_SPECIAL_CHARS));
            $poszt->setSzerzo($this->jelenlegiUser);
            try {
                $poszt->posztEllenorzese();
                $this->posztABKezelo->mentes($poszt);
                $this->view->setMessageSession('A poszt sikeresen felveve');
                $this->view->atiranyitas('posztok', 'index');
            } catch (ValidacioException $e) {
                $this->view->setValtozo('errors', $e->getErrors());
            }
        }
        $this->view->setValtozo('poszt', $poszt);
        $this->view->render('posztok', 'felvesz');
    }

    /**
     * poszt modositasa
     *
     * HA GET request van, akkor megjelenitjuk a szerkeztest, es adatokkal fel kell tolteni az urlepot
     * HA POST requestet kapunk akkor modositun kaz adatbazisban
     *
     * @thorws Exception Ha nincs user a munkameneteben
     * @thorws Exception Ha nincs a megadott id-val poszt
     * @throws Exception Ha nincs id
     * @throws Exception Ha a belepett user ne ma poszt szerzoje
     */
    public function modosit() {
        if(!isset($this->jelenlegiUser)) {
            throw new Exception('nincs user a munakamnetben. Jelentekezzen be!');
        }
        if (!filter_input(INPUT_REQUEST, 'id', FILTER_SANITIZE_SPECIAL_CHARS)) {
            throw new Exception(' aposzt id megadasa kotelezo');
        }
        $posztid = filter_input(INPUT_REQUEST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
        $poszt = $this->posztABKezelo->idAlpjanKeres($posztid);

        if($poszt === null) {
            throw new Exception('Nincs poszt ezzel az idval: ' . $posztid);
        }

        if($poszt->getSzerzo() !== $this->jelenlegiUser) {
            throw new Exception(' a bejelentkezett user ne ma poszt szerzoje');
        }

        if(filter_input(INPUT_POST, 'submit')) {
            $poszt->setCim(filter_input(INPUT_POST, 'cim', FILTER_SANITIZE_SPECIAL_CHARS));
            $poszt->setTartalom(filter_input(INPUT_POST, 'tartalom', FILTER_SANITIZE_SPECIAL_CHARS));
            try{
                $poszt->posztEllenorzese();
                $this->posztABKezelo->modosit($poszt);
                $this->view->setMessageSession('a poszt sikeresen modositva');
                $this->view->atiranyitas('posztok', 'index');

            } catch (ValidacioException $e) {
                $this->view->setValtozo('errors', $e->getErrors());
            }
        }
        $this->view->setValtozo('poszt', $poszt);
        $this->view->render('posztok', 'modosit');
    }

    /**
     * Poszt torlesehez hasznalhato fuggveny
     *
     * Ez a muvelet post requestet hasznal
     * @throws Exception Ha nincs user a sessionben
     * @throws Exception Ha nincs megadva id
     * @throws Exception Ha nincs iylen id-valé poszt
     * @throws Exception Ha a szerzo nem a belepett user
     */
    public function torol() {
        if(!isset($this->jelenlegiUser)) {
            throw new Exception("nincs a munkamenetben user. Be kell elentekezni");
        }
        $posztid = !filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
        $poszt = $this->posztABKezelo->idAlpjanKeres($posztid);

        if($poszt === null) {
            throw new Exception('nincs ilyen poszt');
        }

        if($poszt->getSzerzo() !== $this->jelenlegiUser) {
            throw new Exception('ne ma belepet user a szerzo');
        }

        $this->posztABKezelo->torol($poszt);
        $this->view->setMessageSession('A poszt sikeresen torolve');
        $this->view->atiranyitas('posztok', 'index');
    }



}
