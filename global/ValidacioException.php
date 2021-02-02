<?php
class ValidacioException extends Exception {
    private array $errors = array();

    public function __construct(array $errors, $msg=null)
    {
        parent::__construct($msg);
        $this->errors = $errors;
    }

    /**
     * Validacios hibak lekérése
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
