<?php
require_once __DIR__ . '/../global/ValidacioException.php';

class Poszt {
    private string $id;
    private string $cim;
    private string $tartalom;
    private ?User $szerzo;
    private ?array $kommentek;

    /**
     * Poszt constructor.
     * @param string $id
     * @param string $cim
     * @param string $tartalom
     * @param User|null $szerzo
     * @param array|null $kommentek
     */
    public function __construct(string $id = '', string $cim = '', string $tartalom = '', ?User $szerzo = null, ?array $kommentek = null)
    {
        $this->id = $id;
        $this->cim = $cim;
        $this->tartalom = $tartalom;
        $this->szerzo = $szerzo;
        $this->kommentek = $kommentek;
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
    public function getCim(): string
    {
        return $this->cim;
    }

    /**
     * @param string $cim
     */
    public function setCim(string $cim): void
    {
        $this->cim = $cim;
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
     * @return array|null
     */
    public function getKommentek(): ?array
    {
        return $this->kommentek;
    }

    /**
     * @param array|null $kommentek
     */
    public function setKommentek(?array $kommentek): void
    {
        $this->kommentek = $kommentek;
    }

    /**
     *A poszt ellenorzese
     * @throw ValidacioException
     *
     */
    public function posztEllenorzese(): void {
        $errors = array();

        if(mb_strlen(trim($this->cim))>10) {
            $errors['cim'] = 'A cim megadasa kotelkezo. minimum 10 karakter!';
        }

        if(mb_strlen(trim($this->tartalom)) < 10) {
            $errors['tartalom'] = 'A tartalom megadasa kotelkezo. minimum 10 karakter!';
        }

        if(!empty($errors)) {
            throw new ValidacioException($errors, 'A poszt nem megfelelo');
        }
    }

}
