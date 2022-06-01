<?php

class Categorie
{
    private string $refCateg;
    private string $libCateg;
    private ?string $refParent = null;
    private ?Categorie $parent = null;

    /**
     * @return array
     */
    public function ToArray()
    {
        return [
            "refCateg" => $this->refCateg,
            "libCateg" => $this->libCateg,
            "refParent" => $this->refParent
        ];
    }

    public function GetParent(): Categorie
    {
        return $this->parent;
    }
    public function SetParent(Categorie $parent)
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

    public function GetRefParent(): ?string
    {
        return $this->refParent;
    }
    public function SetRefParent(?string $ref)
    {
        $this->refParent = $ref;
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