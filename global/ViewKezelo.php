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

    //TODO: Nezet kezelo
}
