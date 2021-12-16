$(function() {
    let uF = $("#unknown-field");

    $("#btn-connect").on("click", function() {
        $.post(
            "model/ajax/connect.php",
            {
                   "login": $("#login").val(),
                "password": $("#password").val()
            },
            function(ret) {
                if (ret === "true") {
                    // Connecté
                    console.log("connecté");
                }
                else
                {
                    // Pas connecté
                    console.log("pas connecté");
                    uF.attr("aria-hidden", "false");
                }
            },
            "text"
        );
    });
});