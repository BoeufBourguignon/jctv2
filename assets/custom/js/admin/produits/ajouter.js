$(function() {
    //region Champs
    let ref = $("#admin_new_ref_produit"),
        libelle = $("#admin_new_libelle_produit"),
        categ = $("#admin_new_categ_produit"),
        sousCateg = $("#admin_new_souscateg_produit"),
        desc = $("#admin_new_desc_produit"),
        prix = $("#admin_new_prix_produit"),
        difficulte = $("#admin_new_difficulte_produit"),
        seuil = $("#admin_new_seuil_produit"),
        stock = $("#admin_new_stock_produit");
    //endregion

    //region Selection catégorie
    //Quand on choisit une categ, on doit proposer les bonnes sous catégories
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
    categ.trigger("change");
    //endregion
});