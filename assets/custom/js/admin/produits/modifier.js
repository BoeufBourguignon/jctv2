//Au chargement de la page
$(function() {
    //region Champs
    let ref = $("#admin_edit_ref_produit"),
        libelle = $("#admin_edit_libelle_produit"),
        categ = $("#admin_edit_categ_produit"),
        sousCateg = $("#admin_edit_souscateg_produit"),
        desc = $("#admin_edit_desc_produit"),
        prix = $("#admin_edit_prix_produit"),
        difficulte = $("#admin_edit_difficulte_produit"),
        seuil = $("#admin_edit_seuil_produit"),
        stock = $("#admin_edit_stock_produit");
    //endregion

    //region Selection catégorie
    categ.on("change", function() {
        $.get({
            url: "/api/getSousCategoriesByCategorie",
            data: {
                refCateg: categ.val()
            },
            dataType: "json",
            success: function(data) {
                if(data.length === 0) {
                    sousCateg.prop("disabled", true);
                    sousCateg.empty().append($("<option>").html("Aucun").val("null"));
                } else {
                    sousCateg.prop("disabled", false);
                    sousCateg.empty();
                    data.forEach(function(val) {
                        sousCateg.append($("<option>").html(val["libCateg"]).val(val["refCateg"]));
                    });
                }
            },
            error: function(dataError) {
                console.log(dataError.errorText);
            },
            async: false
        });
    });
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
                console.log(data);
                ref.val(data["refProduit"]);
                libelle.val(data["libProduit"]);
                categ.val(data["refCateg"]);
                categ.trigger("change");
                if($("#admin_edit_souscateg_produit>option").length > 1) {
                    sousCateg.val(data["refSousCateg"]);
                }
                desc.val(data["descProduit"].replace("§p", "\n"));
                prix.val(data["prix"]);
                difficulte.val(data["idDifficulte"]);
                seuil.val(data["seuilAlerte"]);
                stock.val(data["qteStock"]);

                //Si le produit a des sous catégories, il faut les mettre dans le select et l'activer
                //Sinon on remet "Aucune" comme seule option et on désactive le select

            },
            error: function(data) {
                console.log(data.errorText);
            }
        });
    });

    //Au chargement de la page on affiche les infos du produit sélectionné
    selectProduit.trigger("change");
    //endregion
});