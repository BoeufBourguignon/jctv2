//Au chargement de la page
$(function() {
    //region Champs
    let ref = $("#admin_edit_ref_produit"),
        libelle = $("#admin_edit_libelle_produit"),
        categ = $("#admin_edit_categ_produit"),
        desc = $("#admin_edit_desc_produit"),
        prix = $("#admin_edit_prix_produit"),
        difficulte = $("#admin_edit_difficulte_produit"),
        seuil = $("#admin_edit_seuil_produit"),
        stock = $("#admin_edit_stock_produit");
    //endregion

    //region Selection produit
    //On récupère le produit sélectionné
    let selectProduit = $("#admin_edit_select_produits");
    selectProduit.on("change", function() {
        $.get({
            url: "/api/getProduitByRef",
            data: {
                ref: selectProduit.val()
            },
            dataType: "json",
            success: function(data) {
                ref.val(data["refProduit"]);
                libelle.val(data["libProduit"]);
                categ.val(data["refCateg"]);
                desc.val(data["descProduit"].replace("§p", "\n"));
                prix.val(data["prix"]);
                difficulte.val(data["idDifficulte"]);
                seuil.val(data["seuilAlerte"]);
                stock.val(data["qteStock"]);
            },
            error: function(data) {
                console.log(data.errorText);
            }
        })
    });
    //Au chargement de la page on affiche les infos du produit sélectionné
    selectProduit.trigger("change");
    //endregion

    //region Enregistrement produit
    let btnSaveProduit = $("#admin_edit_save_produit");
    btnSaveProduit.on("click", function() {
        console.log(desc.val().replace("\n", "§p"));
    });
    //endregion
});