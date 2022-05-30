<?php

class adminController extends Controller
{
    private function canAccess()
    {
        if($this->Request()->user() == null || $this->Request()->user()->GetIdRole() != 2) {
            $this->redirect("/");
        }
    }

    /**
     * @throws Exception
     */
    public function draw()
    {
        $this->canAccess();

        $this->render("admin/afficAdmin.phtml", [], false);
    }

    /**
     * @throws Exception
     */
    public function produits()
    {
        $this->canAccess();

        if(isset($_POST["submit_update"])) {
            //On récupère les données texte
            $ref = filter_input(INPUT_POST, "admin_edit_ref_produit") ?? "";
            $libelle = filter_input(INPUT_POST, "admin_edit_libelle_produit") ?? "";
            $categ = filter_input(INPUT_POST, "admin_edit_categ_produit") ?? "";
            $sousCateg = filter_input(INPUT_POST, "admin_edit_souscateg_produit");
            $desc = filter_input(INPUT_POST, "admin_edit_desc_produit") ?? "";
            $prix = filter_input(INPUT_POST, "admin_edit_prix_produit") ?? "";
            $difficulte = filter_input(INPUT_POST, "admin_edit_difficulte_produit") ?? "";
            $seuilAlerte = filter_input(INPUT_POST, "admin_edit_seuil_produit") ?? "";
            $qteStock = filter_input(INPUT_POST, "admin_edit_stock_produit") ?? "";

            $_SESSION["selectedProduit"] = $ref;

            //On fait la maj des données texte du produit dans la bdd
            if(    strlen($ref) !== 0
                && strlen($libelle) !== 0
                && strlen($categ) !== 0
                && strlen($desc) !== 0
                && strlen($prix) !== 0
                && strlen($difficulte) !== 0
                && strlen($seuilAlerte) !== 0
                && strlen($qteStock) !== 0
            ) {
                $produit = new Produit();
                $produit->SetRefProduit($ref);
                $produit->SetLibProduit($libelle);
                $produit->SetRefCateg($categ);
                if($sousCateg === null || $sousCateg == "null") {
                    $produit->SetRefSousCateg(null);
                } else {
                    $produit->SetRefSousCateg($sousCateg);
                }
                $produit->SetDescProduit($desc);
                $produit->SetPrix($prix);
                $produit->SetIdDifficulte($difficulte);
                $produit->SetSeuilAlerte($seuilAlerte);
                $produit->SetQteStock($qteStock);

                ProduitManager::SaveProduit($produit);

                //On s'occupe de l'image
                $image_file = "assets/" . $produit->GetImgPath();
                if(!move_uploaded_file($_FILES["admin_edit_photo_produit"]["tmp_name"], $image_file)) {
                    var_dump($_FILES);
                    exit;
                }

                $this->redirect("/admin/produits");
            }
        }

        if(isset($_POST["submit_add"])) {
            //On récupère les données texte
            $ref = filter_input(INPUT_POST, "admin_new_ref_produit") ?? "";
            $libelle = filter_input(INPUT_POST, "admin_new_libelle_produit") ?? "";
            $categ = filter_input(INPUT_POST, "admin_new_categ_produit") ?? "";
            $sousCateg = filter_input(INPUT_POST, "admin_new_souscateg_produit");
            $desc = filter_input(INPUT_POST, "admin_new_desc_produit") ?? "";
            $prix = filter_input(INPUT_POST, "admin_new_prix_produit") ?? "";
            $difficulte = filter_input(INPUT_POST, "admin_new_difficulte_produit") ?? "";
            $seuilAlerte = filter_input(INPUT_POST, "admin_new_seuil_produit") ?? "";
            $qteStock = filter_input(INPUT_POST, "admin_new_stock_produit") ?? "";

            $_SESSION["selectedProduit"] = $ref;

            if(    strlen($ref) !== 0
                && strlen($libelle) !== 0
                && strlen($categ) !== 0
                && strlen($desc) !== 0
                && strlen($prix) !== 0
                && strlen($difficulte) !== 0
                && strlen($seuilAlerte) !== 0
                && strlen($qteStock) !== 0
            ) {
                $produit = new Produit();
                $produit->SetRefProduit($ref);
                $produit->SetLibProduit($libelle);
                $produit->SetRefCateg($categ);
                if($sousCateg === null || $sousCateg == "null") {
                    $produit->SetRefSousCateg(null);
                } else {
                    $produit->SetRefSousCateg($sousCateg);
                }
                $produit->SetDescProduit($desc);
                $produit->SetPrix($prix);
                $produit->SetIdDifficulte($difficulte);
                $produit->SetSeuilAlerte($seuilAlerte);
                $produit->SetQteStock($qteStock);

                ProduitManager::SaveProduit($produit);

                //On s'occupe de l'image
                $image_file = "assets/" . $produit->GetImgPath();
                if(!move_uploaded_file($_FILES["admin_new_photo_produit"]["tmp_name"], $image_file)) {
                    var_dump($_FILES);
                    exit;
                }

                $this->redirect("/admin/produits");
            }
        }

        $this->render("admin/afficProduits.phtml", [
            "produits" => ProduitManager::GetAllProduits(),
            "categories" => CategorieManager::GetLesCategories(),
            "difficultes" => DifficultiesManager::GetLesDifficultes()
        ], false);
    }
}