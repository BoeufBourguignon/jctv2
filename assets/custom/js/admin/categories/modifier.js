$(function() {
    //region Champs
    let ref = $("#admin_edit_ref_categorie"),
        libelle = $("#admin_edit_libelle_categorie"),
        parent = $("#admin_edit_parent_categorie");
    //endregion

    //region Selection catégorie
    let selectCategorie = $("#admin_edit_select_categorie");
    selectCategorie.on("change", function() {
        $.get({
            url: "/api/getCategorieByRef",
            data: {
                ref: selectCategorie.val()
            },
            dataType: "json",
            success: function(data) {
                ref.val(data["refCateg"]);
                libelle.val(data["libCateg"]);
                if(data["refParent"] !== null) {
                    parent.val(data["refParent"]);
                } else {
                    parent.val("null");
                }
            },
            error: function(data) {
                console.log(data.responseText);
            }
        })
    });

    selectCategorie.trigger("change");
    //endregion

    let btnSupprimer = $("#admin_edit_supprimer_categorie");
    btnSupprimer.on("click", function() {
        if(confirm("Etes-vous sûr de vouloir supprimer cette catégorie ?")) {
            window.location.href = "/admin/deleteCategorie?ref=" + ref.val();
        }
    });
});