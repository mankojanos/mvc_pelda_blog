<?php
require_once __DIR__ . '/../global/ValidacioException.php';

class Komment {
    private string $id;
    private string $tartalom;
    private ?User $szerzo;
    private ?Poszt $poszt;

    /**
     * Komment constructor.
     * @param string $id
     * @param string $tartalom
     * @param User $szerzo
     * @param Poszt $poszt
     */
    public function __construct(string $id, string $tartalom, ?User $szerzo = null, ?Poszt $poszt = null)
    {
        $this->id = $id;
        $this->tartalom = $tartalom;
        $this->szerzo = $szerzo;
        $this->poszt = $poszt;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTartalom(): string
    {
        return $this->tartalom;
    }

    /**
     * @param string $tartalom
     */
    public function setTartalom(string $tartalom): void
    {
        $this->tartalom = $tartalom;
    }

    /**
     * @return User|null
     */
    public function getSzerzo(): ?User
    {
        return $this->szerzo;
    }

    /**
     * @param User|null $szerzo
     */
    public function setSzerzo(?User $szerzo): void
    {
        $this->szerzo = $szerzo;
    }

    /**
     * @return Poszt|null
     */
    public function getPoszt(): ?Poszt
    {
        return $this->poszt;
    }

    /**
     * @param Poszt|null $poszt
     */
    public function setPoszt(?Poszt $poszt): void
    {
        $this->poszt = $poszt;
    }

    /**
     * Komment ellenorzese
     * @throw ValidacioException ha a koomment nem valid
     *
     */
    public function kommentEllenorzes(): void {
        $errors = array();
        if (mb_strlen(trim($this->getTartalom())) < 5) {
            $errors['tartalom'] = 'a tartalom megadasa kotelezo. minimum 5 karakter';
        }
        if($this->szerzo == null) {
            $errors['szerzo'] = 'A szerzo megadasa kotelezo';
        }
        if($this->poszt == null) {
            $errors['poszt'] = 'A poszt megadasa kotelezo';
        }
        if(!empty($errors)) {
            throw new ValidacioException($errors, 'A komment nem volt megfelelo');
        }
    }
}
