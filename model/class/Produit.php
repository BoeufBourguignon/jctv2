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

    public function SetReference(string $reference)
    {
        $this->refProduit = $reference;
    }

    public function GetImagePath(): string
    {
        return $this->imgPath;
    }

    public function SetImagePath(string $image_path)
    {
        $this->imgPath = $image_path;
    }

    public function GetLibelle(): string
    {
        return $this->libProduit;
    }

    public function SetLibelle(string $libelle)
    {
        $this->libProduit = $libelle;
    }

    public function GetDescription(): string
    {
        return "<ul><li>" . str_replace("§p", "</li><li>", $this->descProduit) . "</li></ul>";
    }

    public function SetDescription(string $description)
    {
        $this->descProduit = $description;
    }

    public function GetCateg(): string
    {
        return $this->refCateg;
    }

    public function SetCateg(string $refCateg)
    {
        $this->refCateg = $refCateg;
    }

    public function GetPrix(): float
    {
        return $this->prix;
    }

    public function SetPrix(float $prix)
    {
        $this->prix = $prix;
    }

    public function GetDifficulte(): Difficulty
    {
        return $this->difficulte;
    }

    public function SetDifficulte(Difficulty $difficulte)
    {
        $this->difficulte = $difficulte;
    }

    public function GetQteStock(): int
    {
        return $this->qteStock;
    }

    public function SetQteStock(int $qteStock): void
    {
        $this->qteStock = $qteStock;
    }

    public function GetSeuilAlerte(): ?int
    {
        return $this->seuilAlerte;
    }

    public function SetSeuilAlerte(?int $seuilAlerte): void
    {
        $this->seuilAlerte = $seuilAlerte;
    }

    public function GetIdDifficulte(): int
    {
        return $this->idDifficulte;
    }

    public function SetIdDifficulte(int $idDifficulte): void
    {
        $this->idDifficulte = $idDifficulte;
    }
}