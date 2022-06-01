<?php

class panierController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function draw()
    {
        $this->render("/panier/afficPanier.phtml");
    }

    public function add()
    {
        $produit = $this->Request()->post("refProduit");
        $qte = $this->Request()->post("qte");

        if($produit != false && $qte != false) {
            $this->Panier()->Add($produit, $qte);
            $_SESSION["add_panier"][$produit] = true;
        }

        $this->redirect("/produit/" . $produit);
    }

    public function update()
    {
        $this->Panier()->Update($this->Request()->post("panier"));

        $this->redirect("/panier");
    }

    public function infosCommande()
    {
        if($this->Request()->user() == null) {
            $this->redirect("/account");
        }
        if($this->Panier()->PrixTotal() == 0) {
            $this->redirect("/panier");
        }

        $this->render("panier/afficInfosCommande.phtml");
    }

    public function passerCommande()
    {
        if($this->Request()->user() == null) {
            $this->redirect("/account");
        }
        if($this->Panier()->PrixTotal() == 0) {
            $this->redirect("/panier");
        }

        if(isset($_POST["passer_commande_panier"])) {
            //On récupère les champs
            $destinataire = $this->Request()->post("commande_destinataire");
            $adresse = $this->Request()->post("commande_adresse");
            $ville = $this->Request()->post("commande_ville");
            $cp = $this->Request()->post("commande_cp");

            $verif = true;
            if($destinataire == null || ctype_space($destinataire) || strlen($destinataire) == 0 || strlen($destinataire) > 50) {
                $verif = $_SESSION["commande_passee"] = false;
            }
            if($adresse == null || ctype_space($adresse) || strlen($adresse) == 0 || strlen($adresse) > 50) {
                $verif = $_SESSION["commande_passee"] = false;
            }
            if($ville == null || ctype_space($ville) || strlen($ville) == 0 || strlen($ville) > 50) {
                $verif = $_SESSION["commande_passee"] = false;
            }
            if($cp == null || ctype_space($cp) || strlen($cp) == 0 || strlen($cp) > 5) {
                $verif = $_SESSION["commande_passee"] = false;
            }

            if($verif) {
                $_SESSION["commande_passee"] = $this->Panier()->PasserCommande($destinataire, $adresse, $ville, $cp);
            }
        }

        $this->render("panier/afficPasserCommande.phtml");
    }
}