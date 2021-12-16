<?php

class ModalError extends ClassModalManager
{
    public function __construct(string $content)
    {
        parent::__construct("Erreur", $content);
        $this->footerHasOkBt = false;
        $this->footerHasCloseBt = true;
    }
}