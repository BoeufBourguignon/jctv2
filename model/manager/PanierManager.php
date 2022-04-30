<?php

class PanierManager extends BaseManager
{
    private ?Client $client;

    private array $panier = array();
    private int $idCommande = 0;

    public function __construct(?Client $client)
    {
        parent::__construct();

        $this->client = $client;

        if($this->client == null) {
            $this->panier = isset($_COOKIE["panier"]) ? json_decode($_COOKIE["panier"], true) : [];
        } else {
            //Si le client est connecté
            //On récupère son panier en BDD
            $cnx = Database::GetConnection();
            $query = "
                SELECT p.refProduit, qte
                FROM v_panier vp
                    JOIN lignecommande l on vp.idCommande = l.idCommande
                    JOIN produit p on l.refProduit = p.refProduit
                WHERE idClient = :idC
            ";
            $stmt = $cnx->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_KEY_PAIR);
            $stmt->execute([":idC" => $this->client->GetId()]);
            $this->panier = $stmt->fetchAll();
        }
    }

    public function Panier(): array
    {
        $produits = array();
        foreach($this->panier as $refProduit => $qte) {
            $produits[$refProduit] = [
                "produit" => $this->ProduitManager()->GetProduitByRef($refProduit),
                "qte" => $qte
            ];
        }
        return $produits;
    }

    public function QteTotale()
    {
        $qteTotale = 0;
        foreach($this->panier as $qte) {
            $qteTotale += $qte;
        }
        return $qteTotale;
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
            $cnx = Database::GetConnection();
            //Y-a-t-il déjà un panier ?
            $query = "
                SELECT idCommande
                FROM v_panier
                WHERE idClient = :idC
            ";
            $stmt = $cnx->prepare($query);
            $stmt->execute([":idC" => $this->client->GetId()]);
            $idCommandePanier = $stmt->fetchColumn();
            if($idCommandePanier === false) {
                //Pas de panier, donc je crée la commande et j'ajoute au suivi etat commande
                $queryCreerCommande = "
                    INSERT INTO commande (idClient) VALUES
                        (:idC)
                ";
                $stmtCreerCommande = $cnx->prepare($queryCreerCommande);
                $stmtCreerCommande->execute([":idC" => $this->client->GetId()]);
                $idCommandePanier = $cnx->lastInsertId();
                $querySuiviEtat = "
                    INSERT INTO suivietatcommande (idCommande, idEtatCommande, date) VALUES
                        (:idC, 1, NOW())
                ";
                $stmtSuiviEtat = $cnx->prepare($querySuiviEtat);
                $stmtSuiviEtat->execute([":idC" => $idCommandePanier]);
            }
            $querySupprPanier = "
                DELETE FROM lignecommande WHERE idCommande = :commande
            ";
            $stmtSupprPanier = $cnx->prepare($querySupprPanier);
            $stmtSupprPanier->execute([":commande" => $idCommandePanier]);
            foreach($this->panier as $prod => $qte) {
                $queryAjouterProduit = "
                    REPLACE INTO lignecommande (idCommande, refProduit, qte) VALUES
                        (:commande, :produit, :qte)
                ";
                $stmtAjouterProduit = $cnx->prepare($queryAjouterProduit);
                $stmtAjouterProduit->execute([
                    ":commande" => $idCommandePanier,
                    ":produit" => $prod,
                    ":qte" => $qte
                ]);
            }
        }
    }
}