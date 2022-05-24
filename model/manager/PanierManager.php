<?php

class PanierManager extends BaseManager
{
    private ?Client $client;

    private array $panier;
    private ?array $panierComplet = null;
    private int $idCommande = 0;

    public function __construct(?Client $client)
    {
        $this->client = $client;

        if($this->client == null) {
            //Panier hors ligne
            $this->panier = isset($_COOKIE["panier"]) ? json_decode($_COOKIE["panier"], true) : [];
        } else {
            //Panier en ligne
            //On récupère son panier en BDD
            self::getConnection();
            $stmtIdCommande = self::$cnx->prepare("
                SELECT idCommande
                FROM v_panier
                WHERE idClient = :idC
            ");
            $stmtIdCommande->execute([":idC" => $this->client->GetId()]);
            $idPanier = $stmtIdCommande->fetchColumn();
            $this->idCommande = $idPanier === false ? 0 : $idPanier;

            $stmtPanier = self::$cnx->prepare("
                SELECT p.refProduit, qte
                FROM v_panier vp
                    JOIN lignecommande l on vp.idCommande = l.idCommande
                    JOIN produit p on l.refProduit = p.refProduit
                WHERE idClient = :idC
            ");
            $stmtPanier->setFetchMode(PDO::FETCH_KEY_PAIR);
            $stmtPanier->execute([":idC" => $this->client->GetId()]);
            $panier = $stmtPanier->fetchAll();
            $this->panier = $panier === false ? array() : $panier;
        }
    }

    public function Panier(): array
    {
        if($this->panierComplet == null) {
            if(!empty($this->panier)) {
                foreach($this->panier as $refProduit => $qte) {
                    $this->panierComplet[$refProduit] = [
                        "produit" => ProduitManager::GetProduit($refProduit),
                        "qte" => $qte
                    ];
                }
            } else {
                $this->panierComplet = array();
            }
        }
        return $this->panierComplet;
    }

    public function PanierMin() {
        return $this->panier;
    }

    public function QteTotale()
    {
        $qteTotale = 0;
        foreach($this->panier as $qte) {
            $qteTotale += $qte;
        }
        return $qteTotale;
    }

    /**
     * @return float
     */
    public function PrixTotal(): float
    {
        $total = 0;
        foreach($this->panierComplet as $infos) {
            $total += $infos["produit"]->GetPrix() * $infos["qte"];
        }
        return $total;
    }

    public function PrixTotalFormatted(): string
    {
        return number_format($this->PrixTotal(), 2);
    }

    public function Add(string $refProduit, int $qte)
    {
        if($qte != 0) {
            if(isset($this->panier[$refProduit]) && is_int($this->panier[$refProduit])) {
                $this->panier[$refProduit] += $qte;
            } else {
                $this->panier[$refProduit] = $qte;
            }
            $this->UpdateCookieOrBDD();
        }
    }

    public function Update(array $panier)
    {
        $newPanier = array();
        foreach($panier as $refProduit => $qte) {
            if($qte > 0) {
                $newPanier[$refProduit] = $qte;
            }
        }
        $this->panier = $newPanier;
        $this->UpdateCookieOrBDD();
    }

    private function UpdateCookieOrBDD()
    {
        if($this->client == null) {
            setcookie("panier", json_encode($this->panier), time() + 365*24*60*60, "/");
        } else {
            self::getConnection();
            if($this->idCommande == 0) {
                //Pas de panier, donc je crée la commande et j'ajoute au suivi etat commande
                $stmtCreerCommande = self::$cnx->prepare("
                    INSERT INTO commande (idClient) VALUES
                        (:idC)
                ");
                $stmtCreerCommande->execute([":idC" => $this->client->GetId()]);
                $this->idCommande = self::$cnx->lastInsertId();
                $stmtSuiviEtat = self::$cnx->prepare("
                    INSERT INTO suivietatcommande (idCommande, idEtatCommande, date) VALUES
                        (:idC, 1, NOW())
                ");
                $stmtSuiviEtat->execute([":idC" => $this->idCommande]);
            }
            $stmtSupprPanier = self::$cnx->prepare("
                DELETE FROM lignecommande WHERE idCommande = :commande
            ");
            $stmtSupprPanier->execute([":commande" => $this->idCommande]);
            foreach($this->panier as $prod => $qte) {
                $stmtAjouterProduit = self::$cnx->prepare("
                    INSERT INTO lignecommande (idCommande, refProduit, qte) VALUES
                        (:commande, :produit, :qte)
                ");
                $stmtAjouterProduit->execute([
                    ":commande" => $this->idCommande,
                    ":produit" => $prod,
                    ":qte" => $qte
                ]);
            }
        }
    }
}