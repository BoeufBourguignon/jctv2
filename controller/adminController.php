<?php

class adminController extends Controller
{
    private function canAccess()
    {
        if($this->Request()->user() == null || $this->Request()->user()->GetIdRole() != 2) {
            $this->redirect("/account");
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

        $this->render("admin/afficProduits.phtml", [
            "produits" => ProduitManager::GetAllProduits(),
            "categories" => CategorieManager::GetLesCategories(),
            "difficultes" => DifficultiesManager::GetLesDifficultes()
        ], false);
    }

    public function updateProduit()
    {
        if(isset($_POST["submit_update"])) {
            //On récupère les données texte
            $ref = $this->Request()->post("admin_edit_ref_produit");
            $libelle = $this->Request()->post("admin_edit_libelle_produit");
            $categ = $this->Request()->post("admin_edit_categ_produit");
            $sousCateg = $this->Request()->post("admin_edit_souscateg_produit");
            $desc = $this->Request()->post("admin_edit_desc_produit");
            $prix = $this->Request()->post("admin_edit_prix_produit");
            $difficulte = $this->Request()->post("admin_edit_difficulte_produit");
            $seuilAlerte = $this->Request()->post("admin_edit_seuil_produit");
            $qteStock = $this->Request()->post("admin_edit_stock_produit");

            //On savegarde le produit sélectionné
            $_SESSION["selectedProduit"] = $ref;

            $verif = true;
            //Vérification des inputs
            if($ref == null || ctype_space($ref) || strlen($ref) > 20) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la référence";
            }
            if($libelle == null || ctype_space($libelle) || strlen($libelle) > 50) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur le libelle";
            }
            if($categ == null || ctype_space($categ) || strlen($categ) > 20) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la catégorie";
            }
            if($sousCateg != null && (strlen($sousCateg) > 20 || ctype_space($sousCateg))) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la catégorie";
            }
            if($desc == null || ctype_space($desc) || strlen($desc) > 2000) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la description";
            }
            if($prix == null || !is_numeric($prix)) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur le prix";
            }
            if($difficulte == null || !is_numeric($difficulte)) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la difficulté";
            }
            if($seuilAlerte == null || !is_numeric($seuilAlerte)) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur le seuil d'alerte";
            }
            if($qteStock == null || !is_numeric($qteStock)) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la quantité en stock";
            }

            if($verif) {
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
                //On récupère le chemin de l'image
                $image_file = "assets/" . $produit->GetImgPath();
                //On vérifie que le chemin existe
                if (!file_exists(dirname($image_file))) {
                    mkdir(dirname($image_file), 0777, true);
                }
                //On vérifie que l'image ait bien été envoyée, au cas ou l'admin a modifié que les textes
                if($_FILES["admin_edit_photo_produit"]["name"] != '') {
                    //On vérifie que l'image a bien été envoyée au serveur
                    if(    strlen($_FILES["admin_edit_photo_produit"]["tmp_name"]) == 0
                        || !getimagesize($_FILES["admin_edit_photo_produit"]["tmp_name"])
                        || !move_uploaded_file($_FILES["admin_edit_photo_produit"]["tmp_name"], $image_file)
                    ) {
                        $_SESSION["admin_produits_erreur"][] = "Une erreur est survenue lors de l'enregistrement de l'image.";
                    }
                }
            }
        } else if(isset($_POST["annuler_update"])) {
            $_SESSION["selectedProduit"] = $this->Request()->post("admin_edit_ref_produit");
        } else {
            $_SESSION["admin_produits_erreur"][] = "Une erreur est survenue.";
        }
        $this->redirect("/admin/produits");
    }

    public function addProduit()
    {
        if(isset($_POST["submit_add"])) {
            //On récupère les données texte
            $ref = $this->Request()->post("admin_new_ref_produit");
            $libelle = $this->Request()->post("admin_new_libelle_produit");
            $categ = $this->Request()->post("admin_new_categ_produit");
            $sousCateg = $this->Request()->post("admin_new_souscateg_produit");
            $desc = $this->Request()->post("admin_new_desc_produit");
            $prix = $this->Request()->post("admin_new_prix_produit");
            $difficulte = $this->Request()->post("admin_new_difficulte_produit");
            $seuilAlerte = $this->Request()->post("admin_new_seuil_produit");
            $qteStock = $this->Request()->post("admin_new_stock_produit");

            //On sauvegarde le produit ajouté
            $_SESSION["selectedProduit"] = $ref;

            $verif = true;
            //Vérification des inputs
            if($ref == null || strlen($ref) > 20 || !preg_match("/^[a-zA-Z\s]*$/g", $ref)) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la référence";
            }
            if($libelle == null || ctype_space($libelle) || strlen($libelle) > 50) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur le libelle";
            }
            if($categ == null || ctype_space($categ) || strlen($categ) > 20) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la catégorie";
            }
            if($sousCateg != null && (ctype_space($sousCateg) || strlen($sousCateg)) > 20) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la catégorie";
            }
            if($desc == null || ctype_space($desc) || strlen($desc) > 2000) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la description";
            }
            if($prix == null || !is_numeric($prix)) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur le prix";
            }
            if($difficulte == null || !is_numeric($difficulte)) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la difficulté";
            }
            if($seuilAlerte == null || !is_numeric($seuilAlerte)) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur le seuil d'alerte";
            }
            if($qteStock == null || !is_numeric($qteStock)) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la quantité en stock";
            }

            if($verif) {
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

                ProduitManager::AddProduit($produit);

                //On s'occupe de l'image
                //On récupère le chemin de l'image
                $image_file = "assets/" . $produit->GetImgPath();
                //On vérifie que le chemin existe
                if (!file_exists(dirname($image_file))) {
                    mkdir(dirname($image_file), 0777, true);
                }
                //On vérifie que l'image ait bien été envoyée, au cas ou l'admin a modifié que les textes
                if($_FILES["admin_new_photo_produit"]["name"] != '') {
                    //On vérifie que l'image a bien été envoyée au serveur
                    if(   strlen($_FILES["admin_new_photo_produit"]["tmp_name"]) == 0
                        || !getimagesize($_FILES["admin_new_photo_produit"]["tmp_name"])
                        || !move_uploaded_file($_FILES["admin_new_photo_produit"]["tmp_name"], $image_file)
                    ) {
                        $_SESSION["admin_produits_erreur"][] = "Une erreur est survenue lors de l'enregistrement de l'image.";
                    }
                }
            }
        } else if(isset($_POST["annuler_add"])) {
            $_SESSION["selectedProduit"] = $this->Request()->post("admin_edit_ref_produit");
        } else {
            $_SESSION["admin_produits_erreur"][] = "Une erreur est survenue.";
        }
        $this->redirect("/admin/produits");
    }

    public function deleteProduit() {
        $ref = $this->Request()->get("ref");
        if($ref != null) {
            if(ProduitManager::DeleteProduit($ref) == false) {
                $_SESSION["admin_produits_erreur"][] = "Ce produit ne peut pas être supprimé";
                $_SESSION["selectedProduit"] = $ref;
            }
        }
        $this->redirect("/admin/produits");
    }

    public function categories()
    {
        $this->canAccess();

        $this->render("admin/afficCategories.phtml", [
            "categories" => CategorieManager::GetAllCategories(),
        ], false);
    }

    public function updateCategorie()
    {
        if(isset($_POST["submit_update"])) {
            //On récupère les textes
            $ref = $this->Request()->post("admin_edit_ref_categorie");
            $libelle = $this->Request()->post("admin_edit_libelle_categorie");
            $parent = $this->Request()->post("admin_edit_parent_categorie");

            $verif = true;
            if($ref == null || ctype_space($ref) || strlen($ref) > 20) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la référence";
            }
            if($libelle == null || ctype_space($libelle) || strlen($libelle) > 50) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la référence";
            }
            if($parent != null && (ctype_space($parent) || strlen($parent) > 20)) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la catégorie parente";
            }

            if($verif) {
                $categ = new Categorie();
                $categ->SetRef($ref);
                $categ->SetLibelle($libelle);
                if($parent != "null") {
                    $categ->SetRefParent($parent);
                }

                CategorieManager::UpdateCategorie($categ);

                $_SESSION["selectedCategorie"] = $categ->GetRef();
            }
        } else if(isset($_POST["annuler_update"])) {
            $_SESSION["selectedCategorie"] = $this->Request()->post("admin_edit_ref_categorie");
        } else if(isset($_POST["supprimer_categorie"])) {
            $ref = $this->Request()->post("admin_edit_ref_categorie");
            CategorieManager::DeleteCategorie($ref);
        } else {
            $_SESSION["admin_categories_erreur"][] = "Une erreur est survenue.";
        }
        $this->redirect("/admin/categories");
    }

    public function addCategorie()
    {
        if(isset($_POST["submit_add"])) {
            //On récupère les textes
            $ref = $this->Request()->post("admin_new_ref_categorie");
            $libelle = $this->Request()->post("admin_new_libelle_categorie");
            $parent = $this->Request()->post("admin_new_parent_categorie");

            $verif = true;
            if($ref == null || ctype_space($ref) || strlen($ref || !preg_match("/^[a-zA-Z\s]*$/g", $ref)) > 20) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la référence";
            }
            if($libelle == null || ctype_space($libelle) || strlen($libelle) > 50) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la référence";
            }
            if($parent != null && (ctype_space($parent) || strlen($parent) > 20)) {
                $verif = false;
                $_SESSION["admin_produits_erreur"][] = "Erreur sur la catégorie parente";
            }

            if($verif) {
                $categ = new Categorie();
                $categ->SetRef($ref);
                $categ->SetLibelle($libelle);
                if($parent != "null") {
                    $categ->SetRefParent($parent);
                }

                CategorieManager::AddCategorie($categ);

                $_SESSION["selectedCategorie"] = $categ->GetRef();
            }
        } else if(isset($_POST["annuler_add"])) {
            $_SESSION["selectedCategorie"] = $this->Request()->post("admin_new_ref_categorie");
        } else {
            $_SESSION["admin_categories_erreur"][] = "Une erreur est survenue.";
        }
        $this->redirect("/admin/categories");
    }

    public function deleteCategorie()
    {
        $ref = $this->Request()->get("ref");
        if($ref != null) {
            if(CategorieManager::DeleteCategorie($ref) == false) {
                $_SESSION["admin_categories_erreur"][] = "Cette catégorie ne peut pas être supprimé";
                $_SESSION["selectedProduit"] = $ref;
            }
        }
        $this->redirect("/admin/categories");
    }

    public function clients()
    {
        $this->render("admin/afficClients.phtml", [
            "clients" => ClientManager::GetLesClients()
        ], false);
    }

    public function changePassword()
    {
        $regexPwd = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/";
        $idClient = $this->Request()->post("select_client");
        $pwd = $this->Request()->post("admin_client_mdp");
        $pwdVerif = $this->Request()->post("admin_client_mdp_verif");

        $verif = true;
        if(!preg_match($regexPwd, $pwd)) {
            $_SESSION["admin_erreur_client_password"][] = "Le mot de passe doit contenir une majuscule, 
                                une minuscule, un chiffre, un caractère spécial (@,$,!,%,*,?,&) et faire plus 
                                de 8 caractères";
            $verif = false;
        }
        if($pwd != $pwdVerif) {
            $_SESSION["admin_erreur_client_password"][] = "Les mots de passe ne correspondent pas";
            $verif = false;
        }
        if(strlen($pwd) >= 40) {
            $_SESSION["admin_erreur_client_password"][] = "Le mot de passe doit faire moins de 40 caractères";
        }
        if(strlen($pwd) < 6) {
            $_SESSION["admin_erreur_client_password"][] = "Le mot de passe doit faire 6 caractères ou plus";
        }

        if($verif) {
            if(!ClientManager::ChangerPassword($idClient,$pwd)) {
                $_SESSION["admin_erreur_client_password"][] = "Une erreur est survenue";
            }
        }
        $this->redirect("/admin/clients");
    }
}