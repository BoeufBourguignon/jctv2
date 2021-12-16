<?php

class Produit
{
    private int $idProduit;
    private string $refProduit;
    private string $imgPath;
    private string $libProduit;
    private string $descProduit;
    private float $prix;
    private int $idCateg;
    private Difficulty $difficulte;


    public function __construct() {

    }

    //ACCESSEURS
    public function GetId():int {
        return $this->idProduit;
    }
    public function SetId(int $id) {
        $this->idProduit = $id;
    }

    public function GetReference():string
    {
        return $this->refProduit;
    }
    public function SetReference(string $reference)
    {
        $this->refProduit = $reference;
    }

    public function GetImagePath():string
    {
        return $this->imgPath;
    }
    public function SetImagePath(string $image_path)
    {
        $this->imgPath = $image_path;
    }

    public function GetLibelle():string
    {
        return $this->libProduit;
    }
    public function SetLibelle(string $libelle)
    {
        $this->libProduit = $libelle;
    }

    public function GetDescription():string
    {
        return "<ul>".str_replace(["§s","§p","§e"],["<li>","</li><li>","</li>"],$this->descProduit)."</ul>";
    }
    public function SetDescription(string $description)
    {
        $this->descProduit = $description;
    }

    public function GetCateg():int
    {
        return $this->idCateg;
    }
    public function SetCateg(int $idSousCategorie)
    {
        $this->idCateg = $idSousCategorie;
    }

    public function GetPrix():float
    {
        return $this->prix;
    }
    public function SetPrix(float $prix) {
        $this->prix = $prix;
    }

    public function GetDifficulte():Difficulty {
        return $this->difficulte;
    }
    public function SetDifficulte(Difficulty $difficulte) {
        $this->difficulte = $difficulte;
    }
}