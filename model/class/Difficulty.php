<?php

class Difficulty
{
    private int $idDifficulte;
    private string $libDifficulte;

    public function __construct()
    {}

    public function GetId():int
    {
        return $this->idDifficulte;
    }
    public function SetId(int $id)
    {
        $this->idDifficulte = $id;
    }

    public function GetLibelle():string
    {
        return $this->libDifficulte;
    }
    public function SetLibelle(string $lib)
    {
        $this->libDifficulte = $lib;
    }
}