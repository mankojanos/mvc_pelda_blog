<?php
require_once __DIR__ . '/../global/PDOConnection.php';
require_once __DIR__ . '/../model/Komment.php';

class KommentABKezelo {
    /**
     * KommentABKezelo constructor.
     *
     */
    public function __construct() {
        $this->db = PDOConnection::getPeldany();
}

    /**
     * a komment mentese a db-be
     * @param Komment $komment a beszurando komment
     * @return int a komment idja
     * @trow PDOException ha db hiba van
     */

public function mentes(Komment $komment): int {
        $query = $this->db->prepare("INSERT INTO komment(tartalom, szerzo, poszt) VALUES (?, ?, ?)");
        $query->execute(array($komment->getTartalom(), $komment->getSzerzo()->getUsernev(), $komment->getPoszt()->getId()));
        return $this->db->lastInsertId();
    }
}
