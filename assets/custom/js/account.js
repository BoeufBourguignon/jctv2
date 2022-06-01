$(function() {
    $("#account_logout").on("click", function() {
        if(confirm("Voulez-vous vous déconnecter ?")) {
            window.location.href = "/account/logout";
        }
    });
});