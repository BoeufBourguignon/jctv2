<?php

class Produit
{
    private string      $refProduit;
    private string      $refCateg;
    private ?Categorie  $categ = null;
    private ?string     $refSousCateg;
    private ?Categorie  $sousCateg = null;
    private string      $imgPath;
    private string      $libProduit;
    private string      $descProduit;
    private float       $prix;
    private int         $idDifficulte;
    private ?Difficulty $difficulte = null;
    private int         $seuilAlerte;
    private int         $qteStock;

    /**
     * @return Categorie
     */
    public function GetCategorie(): Categorie
    {
        if($this->categ == null || $this->categ->GetRef() != $this->refCateg) {
            $this->categ = $this->CategorieManager()->GetCategorie($this->refCateg);
        }
        return $this->categ;
    }

    /**
     * @param Categorie $categ
     * @return $this
     */
    public function SetCategorie(Categorie $categ): static
    {
        $this->categ = $categ;
        $this->refCateg = $categ->GetRef();
        return $this;
    }

    /**
     * @return Categorie
     */
    public function GetSousCategorie(): Categorie
    {
        if($this->sousCateg == null || $this->sousCateg->GetRef() != $this->refSousCateg) {
            $this->sousCateg = $this->CategorieManager()->GetCategorie($this->refSousCateg);
        }
        return $this->sousCateg;
    }

    /**
     * @param ?Categorie $sousCateg
     * @return $this
     */
    public function SetSousCategorie(?Categorie $sousCateg): static
    {
        $this->sousCateg = $sousCateg;
        $this->refSousCateg = $sousCateg->GetRef();
        return $this;
    }

    /**
     * @return Difficulty
     */
    public function GetDifficulte(): Difficulty
    {
        if($this->difficulte == null || $this->difficulte->GetId() != $this->idDifficulte) {
            $this->difficulte = DifficultiesManager::GetDifficulteById($this->idDifficulte);
        }
        return $this->difficulte;
    }

    /**
     * @param Difficulty $diff
     * @return $this
     */
    public function SetDifficulte(Difficulty $diff): static
    {
        $this->difficulte = $diff;
        $this->idDifficulte = $diff->GetId();
        return $this;
    }

    /**
     * @return string
     */
    public function GetRefProduit(): string
    {
        return $this->refProduit;
    }

    /**
     * @param string $refProduit
     * @return Produit
     */
    public function SetRefProduit(string $refProduit): static
    {
        $this->refProduit = $refProduit;
        return $this;
    }

    /**
     * @return string
     */
    public function GetRefCateg(): string
    {
        return $this->refCateg;
    }

    /**
     * @param string $refCateg
     */
    public function SetRefCateg(string $refCateg): static
    {
        $this->refCateg = $refCateg;
        return $this;
    }

    /**
     * @return ?string
     */
    public function GetRefSousCateg(): ?string
    {
        return $this->refSousCateg;
    }

    /**
     * @param ?string $refSousCateg
     * @return Produit
     */
    public function SetRefSousCateg(?string $refSousCateg): static
    {
        $this->refSousCateg = $refSousCateg;
        return $this;
    }

    /**
     * @return string
     */
    public function GetImgPath(): string
    {
        return $this->imgPath;
    }

    /**
     * @param string $imgPath
     * @return Produit
     */
    public function SetImgPath(string $imgPath): static
    {
        $this->imgPath = $imgPath;
        return $this;
    }

    /**
     * @return string
     */
    public function GetLibProduit(): string
    {
        return $this->libProduit;
    }

    /**
     * @param string $libProduit
     * @return Produit
     */
    public function SetLibProduit(string $libProduit): static
    {
        $this->libProduit = $libProduit;
        return $this;
    }

    /**
     * @return string
     */
    public function GetDescProduit(): string
    {
        return "<ul><li>" . str_replace("§p", "</li><li>", $this->descProduit) . "</li></ul>";
    }

    /**
     * @param string $descProduit
     * @return Produit
     */
    public function SetDescProduit(string $descProduit): static
    {
        $this->descProduit = $descProduit;
        return $this;
    }

    /**
     * @return float
     */
    public function GetPrix(): float
    {
        return $this->prix;
    }

    /**
     * @param float $prix
     * @return Produit
     */
    public function SetPrix(float $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    /**
     * @return int
     */
    public function GetIdDifficulte(): int
    {
        return $this->idDifficulte;
    }

    /**
     * @param int $idDifficulte
     * @return Produit
     */
    public function SetIdDifficulte(int $idDifficulte): static
    {
        $this->idDifficulte = $idDifficulte;
        return $this;
    }

    /**
     * @return int
     */
    public function GetSeuilAlerte(): int
    {
        return $this->seuilAlerte;
    }

    /**
     * @param int $seuilAlerte
     * @return Produit
     */
    public function SetSeuilAlerte(int $seuilAlerte): static
    {
        $this->seuilAlerte = $seuilAlerte;
        return $this;
    }

    /**
     * @return int
     */
    public function GetQteStock(): int
    {
        return $this->qteStock;
    }

    /**
     * @param int $qteStock
     * @return Produit
     */
    public function SetQteStock(int $qteStock): static
    {
        $this->qteStock = $qteStock;
        return $this;
    }
}