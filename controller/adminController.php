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

        $this->render("admin/afficProduits.phtml", [
            "produits" => ProduitManager::GetAllProduits()
        ], false);
    }
}