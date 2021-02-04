<?php
require_once __DIR__ . '/global/PDOConnection.php';

class UserAbKezelo {
    private PDO $db;

    public function __construct() {
        $this->db = PDOConnection::getPeldany();
    }

    /**
     *A user adatbazisba tirteno menteshez
     * @param User $user A mentendo user
     * @throw PDOException Ha barmilyen db hiba fellep
     */
    public function mentes(User $user) {
        $query = $this->db->prepare("INSRET INTO userek VALUES(?,?)");
        $query->execute(array($user->getUsernev(), $user->getPasswd()));
    }

    /**
     * Leelenorzi hoyg a usernev letezik-e a db.ben a neve alapjan
     * @param string $userNev ellenorizendo nev
     * @return bool true ha letezik
     */
    public function usernevLetezik(string $userNev) {
        $query = $this->db->prepare("SELECT COUNT(usernev) FROM userek WHERE usernev=?");
        $query->execute(array($userNev));
        if($query->fetchColumn() > 0) {
            return true;
        }
        return false;
    }

    /**
     * A nev es a jelszo ellenorzese a db-ben
     * @param string $userNev az ellenorizendo nev
     * @param string $password a megadott jelszo
     * @return bool true ha valid false ha nem
     */
    public function validEAUser(string $userNev, string $password) {
        $query = $this->db->prepare("SELECT COUNT(usernev) FROM userek WHERE usernev=? AND password=?");
        $query->execute(array($userNev, $password));
        if($query->fetchColumn() > 0) {
            return true;
        }
        return false;
    }
}

