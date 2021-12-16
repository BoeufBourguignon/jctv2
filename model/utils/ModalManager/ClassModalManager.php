<?php

class ClassModalManager
{
    protected bool $headerHasCloseBt = true;
    protected bool $footerHasCloseBt = false;
    protected bool $footerHasOkBt = true;
    private bool $escapeKeyEnabled = true;
    private bool|string $backdropEnabled = true;
    private string $onClose = "";
    protected string $title;
    protected string $content;

    public function __construct()
    {}

    public function buildModal()
    {
        $params = array(
            "title" => $this->title,
            "content" => $this->content,
            "headerHasCloseBt" => $this->headerHasCloseBt,
            "footerHasCloseBt" => $this->footerHasCloseBt,
            "footerHasOkBt" => $this->footerHasOkBt,
            "escapeKeyEnabled" => $this->escapeKeyEnabled,
            "backdropEnabled" => $this->backdropEnabled,
            "onClose" => $this->onClose
        );
        extract($params);
        include "AfficModalManager.phtml";
    }

    public function getModalErrorReloadOnClose(string $content):ClassModalManager
    {
        $this->getModalError($content);
        $this->onClose = "location.reload();";
        return $this;
    }

    public function getModalError(string $content):ClassModalManager
    {
        $this->title="Erreur";
        $this->content=$content;
        $this->footerHasOkBt=false;
        $this->footerHasCloseBt=true;
        return $this;
    }
}
