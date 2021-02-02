<?php
require_once __DIR__ . '/../global/ValidacioException.php';
class User {
    private string $usernev;
    private string $passwd;

    public function __construct(string $usernev = '', string $passwd = '')
    {
        $this->usernev = $usernev;
        $this->passwd = $passwd;
    }

    /**
     * @return string
     */
    public function getUsernev(): string
    {
        return $this->usernev;
    }

    /**
     * @param string $usernev
     */
    public function setUsernev(string $usernev): void
    {
        $this->usernev = $usernev;
    }

    /**
     * @return string
     */
    public function getPasswd(): string
    {
        return $this->passwd;
    }

    /**
     * @param string $passwd
     */
    public function setPasswd(string $passwd): void
    {
        $this->passwd = $passwd;
    }

    public function regisztracioEllenorzese(): void {
        $errors = array();

        if(mb_strlen($this->usernev) < 5) {
            $errors['usernev'] = 'Afelhasznalo neve minimum 5 karakteres kell legyen';
        }
        if (mb_strlen($this->passwd) < 8) {
            $errors['passwd'] = 'A jelszo minimum 8 karakteres kell legyen';
        }
        if(!empty($errors)) {
            throw new ValidacioException($errors, 'A felhasznalo nem megfelelo');
        }
    }
}
