<?php

class Client
{
    private int $idClient, $idRoleClient;
    private string $loginClient, $passwordClient, $mailClient;

    public function __construct() {}

    public function GetId(): int { return $this->idClient; }

    public function SetId(int $val) { $this->idClient = $val; }

    public function GetIdRole(): int { return $this->idRoleClient; }

    public function SetIdRole(int $val) { $this->idRoleClient = $val; }

    public function GetLogin(): string { return $this->loginClient; }

    public function SetLogin(string $val) { $this->loginClient = $val; }

    public function GetPassword(): string { return $this->passwordClient; }

    public function SetPassword(string $val) { $this->passwordClient = $val; }

    public function GetMail(): string { return $this->mailClient; }

    public function SetMail(int $val) { $this->mailClient = $val; }
}