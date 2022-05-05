$(function() {
    $("#filters_button>button.btn").on("click", function() {

    });
});


function loadEventsTri() {
    $("#filters_button>.btn").on("click", function () {
        let categ = window.location.href.substring(window.location.href.lastIndexOf('/') + 1),
            subcategs = $("input[name=subcateg]:checked"),
            difficulties = $("input[name=difficulty]:checked"),
            order = $("select[name=filters_tri_order]"),
            way = $("input[name=filters_tri_way]:checked"),
            subcategsArray = subcategs.get().map(function (item) {
                if(typeof(item.value) === "number") {
                    return Number(item.value);
                }
            }),
            difficultiesArray = difficulties.get().map(function (item) {
                if(typeof(item.value) === "number") {
                    return Number(item.value);
                }
            });

        $.post(
            "/model/ajax/doTri.php",
            {
                "categ": categ,
                "order": order.val(),
                "way": way.val(),
                "subcategs": JSON.stringify(subcategsArray),
                "difficulties": JSON.stringify(difficultiesArray)
            },
            function (rep) {
                let main_affic = $("#main_affic");
                main_affic.empty();
                main_affic.append(rep);
            },
            "html"
        );
    });

    //Select/unselect all categ
    $("#filters_select_categs").on("click", function() {
        let _this = $(this),
            categs = $("input[name=subcateg]");
        if (_this.data("state") === 1) {
            categs.prop("checked", false);
            _this.data("state",0);
        }
        else if (_this.data("state") === 0) {
            categs.prop("checked", true);
            _this.data("state",1);
        }
    });
    //Select/unselect all difficulties
    $("#filters_select_difficulties").on("click", function() {
        let _this = $(this),
            difficulties = $("input[name=difficulty]");
        if (_this.data("state") === 1) {
            difficulties.prop("checked", false);
            _this.data("state",0);
        }
        else if (_this.data("state") === 0) {
            difficulties.prop("checked", true);
            _this.data("state",1);
        }
    });
}