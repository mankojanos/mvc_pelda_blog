<?php
require_once __DIR__ . '/../global/PDOConnection.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/Poszt.php';
require_once __DIR__ . '/../model/Komment.php';

class PosztABKezelo {

    private PDO $db;

    public function __construct()
    {
        $this->db = PDOConnection::getPeldany();
    }

    /**
     * Az osszes poszt kiolvasasa
     * @return array a posztokat tartalmazo tomb (kommentek nelkul)
     * @throws PDOException ha db hiba lep fel
     */
    public function osszesElem():array {
        $query = $this->db->query("SELECT * FROM posztok INNER JOIN userek ON posztok.szerzo = userek.usernev");
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        $posztok = array();
        foreach ($res as $poszt) {
            $szerzo = new User($poszt['usernev']);
            $posztok[] = new Poszt($poszt['id'], $poszt['cim'], $poszt['tartalom'], $szerzo);
        }
        return $posztok;
    }

    /**
     * A megadott id alapjan a post lekerese(kommentek nelkul)
     * @param string $posztId a poszt azonositoja
     * @return Poszt|null A poszt peldanya vagy ha noncs akkor null
     * @throws PDOException Ha db hiba lep fel
     */

    public function idAlpjanKeres(string $posztId): ?Poszt {
        $query = $this->db->prepare("SELECT * FROM posztok WHERE id=?");
        $query->execute(array($posztId));
        $poszt = $query->fetch(PDO::FETCH_ASSOC);

        if($poszt != null) {
            return new Poszt ($poszt['id'], $poszt['cim'], $poszt['tartalom'], new User($poszt['szerzo']));
        }
        return null;
    }

    /**
     * Egy poszt lekerese az adatbazisbol kommentekkel
     * @param string $posztId a betoltendo poszt
     * @return Poszt|null a poszt peldanya, null ha nincs poszt
     * @throws PDOException db hiba esetén
     */
    public function idAlapjanKommentekkel(string $posztId): ?Poszt {
        $query = $this->db->prepare("SELECT P.id AS 'poszt.id', P.cim AS 'poszt.cim', P.tartalom AS 'poszt.tartalom', P.szerzo AS 'poszt.szerzo', k.id AS 'komment.id', k.tartalom AS 'komment.tartalom', k.poszt AS 'komment.poszt', k.szerzo AS 'komment.szerzo' FROM posztok as P LEFT OUTER JOIN kommentek k in P.id = K.poszt WHERE P.id=?" );
        $query->execute(array($posztId));
        $posztKommentekkel = $query->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($posztKommentekkel)) {
            $poszt = new Poszt($posztKommentekkel[0]['poszt.id'], $posztKommentekkel[0]['poszt.cim'], $posztKommentekkel[0]['poszt.tartalom'], new User($posztKommentekkel[0]['poszt.szerzo']));
            $kommentek = array();
            if($posztKommentekkel[0]['kommentek.id'] != null) {
                foreach ($posztKommentekkel as $komment) {
                    $komment = new Komment($komment['komment.id'], $komment['komment.tartalom'], new User($komment['komment.szerzo']), $poszt);
                    $kommentek[]=$komment;
                }
            }
            $poszt->setKommentek($kommentek);
            return $poszt;
        }
        return null;
    }

    /**
     * Poszt mentese a db-be
     * @param Poszt $poszt a mentendo poszt
     * @return int  az uj poszt id
     * @throws PDOException ha db hiba lep fel
     */
    public function mentes(Poszt $poszt): int {
        $query = $this->db->prepare('INSERT INTO posztok(cim, tartalom, szerzo) VALUES (?, ?, ?)');
        $query->execute(array($poszt->getCim(), $poszt->getTartalom(), $poszt->getSzerzo()->getUsernev()));
        return $this->db->lastInsertId();
    }

    /**
     * Poszt modositas a db-ben
     * @param Poszt $poszt a modositando poszt
     * @throws PDOException ha db hiba lep fel
     */
    public function modosit(Poszt $poszt) {
        $query = $this->db->prepare('UPDATE posztok SET cim=?, tartalom=? WHERE id =?');
        $query->execute(array($poszt->getCim(), $poszt->getTartalom(), $poszt->getId()));
    }

    /**
     * Poszt torlese
     * @param Poszt $poszt a torlendo poszt
     * @throws PDOException ha db hiba lep fel
     */
    public function torol(Poszt $poszt) {
        $query = $this->db->prepare('DELETE FROM posztok WHERE id=?');
        $query->execute(array($poszt->getId()));
    }
}
