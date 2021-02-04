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
}
