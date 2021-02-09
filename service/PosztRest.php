<?php
require_once __DIR__ . "/AlapRest.php";
require_once __DIR__ . "/../model/Poszt.php";
require_once __DIR__ . "/../model/PosztABKezelo.php";
require_once __DIR__ . "/../model/Komment.php";
require_once __DIR__ . "/../model/KommentABKezelo.php";

class PosztRest extends AlapRest {
    private $posztAbkezelo;
    private $kommentAbkezelo;

    public function __construct() {
        $this->posztAbkezelo = new PosztABKezelo();
        $this->kommentAbkezelo = new KommentABKezelo();
    }

    public function getPosztok() {
        $posztok = $this->posztAbkezelo->osszesElem();

        $posztok_tomb = array();
        /**
         * @var Poszt $poszt
         */
        foreach ($posztok as $poszt) {
            $posztok_tomb[] = array(
                'id'        => $poszt->getId(),
                'cim'       => $poszt->getCim(),
                'tartalom'  => $poszt->getTartalom(),
                'szerzo_id' => $poszt->getSzerzo()->getUsernev()
            );
        }
        header($_SERVER['SERVER_PROTOCOL'] . ' 200 ok');
        header('Content-Type: application/json');
        echo json_encode($posztok_tomb);
    }

    public function posztKeszitese($adat) {
        $aktualisUser = parent::validaljaAUsert();
        $poszt = new Poszt();

        if(isset($adat->cim) && isset($adat->tartalom)) {
            $poszt->setCim($adat->cim);
            $poszt->setTartalom($adat->tartalom);
            $poszt->setSzerzo($aktualisUser);
        }

        try {
            $poszt->posztEllenorzese();
            $posztid = $this->posztAbkezelo->mentes($poszt);
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 ok');
            header('Content-Type: application/json');
            header('Location: ' . $_SERVER['REQUEST_URI'] . '/' . $posztid);
            echo json_encode(array(
                'id'       => $posztid,
                'cim'      => $poszt->getCim(),
                'tartalom' =>$poszt->getTartalom()
            ));

        } catch (ValidacioException $e) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Hiba');
            header('Content-Type: application/json');
            echo json_encode($e->getErrors());
        }
    }

    public function egyPosztOlvas($posztId) {
        $poszt = $this->posztAbkezelo->idAlapjanKommentekkel($posztId);
        if($poszt === NULL) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 hiba');
            return;
        }
        $poszt_tomb = array(
            'id'        => $poszt->getId(),
            'cim'       => $poszt->getCim(),
            'tartalom'  => $poszt->getTartalom(),
            'szerzo_id' =>$poszt->getSzerzo()->getUsernev()
        );

        $poszt_tomb['kommentek'] = array();
        /**
         * @var Komment $komment
         */
        foreach ($poszt->getKommentek() as $komment) {
            array_push($poszt_tomb['kommentek'], array(
               'id'         => $komment->getId(),
               'tartalom'   => $komment->getTartalom(),
               'szerzo'     => $komment->getSzerzo()->getUsernev()
            ));
        }

        header($_SERVER['SERVER_PROTOCOL'] . ' 200 ok');
        header('Content-Type: application/json');
        echo json_encode($poszt_tomb);
    }

    public function posztModositas($posztId, $adat) {
        $aktualisUser = parent::validaljaAUsert();
        $poszt = $this->posztAbkezelo->idAlpjanKeres($posztId);
        if($poszt == null) {
            header($_SERVER['SERVER_PROTOCOL'] . '4 00 hiba');
            return;
        }

        if($poszt->getSzerzo() !== $aktualisUser) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 nem jo a user');
            return;
        }
        $poszt->setCim($adat->cim);
        $poszt->setTartalom($adat->tartalom);

        try {
            $poszt->posztEllenorzese();
            $this->posztAbkezelo->modosit($poszt);
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 ok');
        } catch (ValidacioException $e) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 hiba');
            header('Content-Type: application/json');
            echo json_encode($e->getErrors());
        }
    }

    public function posztTorlese($posztId) {
        $jelUser = parent::validaljaAUsert();
        $poszt = $this->posztAbkezelo->idAlpjanKeres($posztId);
        if($poszt === null) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 hiba');
            return;
        }
        if($poszt->getSzerzo() !== $jelUser) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 hiba');
            return;
        }
        $this->posztAbkezelo->torol($poszt);
        header($_SERVER['SERVER_PROTOCOL'] . ' 200 ok');
    }

    public function kommentKeszitese($posztId, $adat) {
        $aktUser = parent::validaljaAUsert();
        $poszt = $this->posztAbkezelo->idAlpjanKeres($posztId);
        if($poszt === null) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 hiba');
            return;
        }
        $komment = new Komment();
        $komment->setTartalom($adat->tartalom);
        $komment->setSzerzo($aktUser);
        $komment->setPoszt($poszt);

        try {
            $komment->kommentEllenorzes();
            $this->kommentAbkezelo->mentes($komment);
        } catch (ValidacioException $e) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 hiba');
            header('Content-Type: application/json');
            echo json_encode($e->getErrors());
        }
    }
}

$posztRest = new PosztRest();
$posztRoute = Routing::getPeldany();
$posztRoute->route('GET', '/poszt', array($posztRest, 'getPosztok'));
$posztRoute->route('GET', '/poszt/$1', array($posztRest, 'egyPosztOlvas'));
$posztRoute->route('POST', '/poszt', array($posztRest, 'posztKeszitese'));
$posztRoute->route('PUT', '/poszt', array($posztRest, 'posztModositas'));
$posztRoute->route('DELETE', '/poszt/$1', array($posztRest, 'posztTorlese'));
$posztRoute->route('POST', '/poszt/$1/komment', array($posztRest, 'kommentKeszitese'));
