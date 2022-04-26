<?php

class Produit
{
    private string $refProduit;
    private string $imgPath;
    private string $libProduit;
    private string $descProduit;
    private float $prix;
    private string $refCateg;
    private int $qteStock;
    private ?int $seuilAlerte;
    private int $idDifficulte;
    private Difficulty $difficulte;

    //ACCESSEURS
    public function GetReference(): string
    {
        return $this->refProduit;
    }

    public function SetReference(string $reference): Produit
    {
        $this->refProduit = $reference;
        return $this;
    }

    public function GetImagePath(): string
    {
        return $this->imgPath;
    }

    public function SetImagePath(string $image_path): Produit
    {
        $this->imgPath = $image_path;
        return $this;
    }

    public function GetLibelle(): string
    {
        return $this->libProduit;
    }

    public function SetLibelle(string $libelle): Produit
    {
        $this->libProduit = $libelle;
        return $this;
    }

    public function GetDescription(): string
    {
        return "<ul><li>" . str_replace("§p", "</li><li>", $this->descProduit) . "</li></ul>";
    }

    public function SetDescription(string $description): Produit
    {
        $this->descProduit = $description;
        return $this;
    }

    public function GetCateg(): string
    {
        return $this->refCateg;
    }

    public function SetCateg(string $refCateg): Produit
    {
        $this->refCateg = $refCateg;
        return $this;
    }

    public function GetPrix(): float
    {
        return $this->prix;
    }

    public function SetPrix(float $prix): Produit
    {
        $this->prix = $prix;
        return $this;
    }

    public function GetDifficulte(): Difficulty
    {
        return $this->difficulte;
    }

    public function SetDifficulte(Difficulty $difficulte): Produit
    {
        $this->difficulte = $difficulte;
        return $this;
    }

    public function GetQteStock(): int
    {
        return $this->qteStock;
    }

    public function SetQteStock(int $qteStock): Produit
    {
        $this->qteStock = $qteStock;
        return $this;
    }

    public function GetSeuilAlerte(): ?int
    {
        return $this->seuilAlerte;
    }

    public function SetSeuilAlerte(?int $seuilAlerte): Produit
    {
        $this->seuilAlerte = $seuilAlerte;
        return $this;
    }

    public function GetIdDifficulte(): int
    {
        return $this->idDifficulte;
    }

    public function SetIdDifficulte(int $idDifficulte): Produit
    {
        $this->idDifficulte = $idDifficulte;
        return $this;
    }
}