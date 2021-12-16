<?php

class Categorie
{
    private int $idCateg;
    private ?Categorie $parent = null;
    private string $refCateg;
    private string $libCateg;

    public function __construct() {}

    public function GetId():int
    {
        return $this->idCateg;
    }
    public function SetId($id)
    {
        $this->idCateg = $id;
    }

    public function GetParent():Categorie
    {
        return $this->parent;
    }
    public function SetParent($parent)
    {
        $this->parent = $parent;
    }

    public function GetRef():string
    {
        return $this->refCateg;
    }
    public function SetRef($ref)
    {
        $this->refCateg = $ref;
    }

    public function GetLibelle():string
    {
        return $this->libCateg;
    }
    public function SetLibelle($lib)
    {
        $this->libCateg = $lib;
    }
}