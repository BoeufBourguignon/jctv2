<?php
class Utils
{
    public static function redirect(string $url)
    {
        header("Location: ".$url);
        exit;
    }

    public static function callModalAlert(array $args = null)
    {
        $title =
            isset($args["title"]) && !empty($args["title"])
                ?   $args["title"]
                :   "Erreur"
        ;
        $content =
            isset($args["content"]) && !empty($args["content"])
                ?   $args["content"]
                :   "Une erreur s'est produite"
        ;
        $hasHeaderClose =
            !(isset($args["hasHeaderClose"]) && $args["hasHeaderClose"] == false)
        ;
        $hasCloseBtn =
            !(isset($args["hasCloseBtn"]) && $args["hasCloseBtn"] == false)
        ;
        $hasOkBtn =
            isset($args["hasOkBtn"]) && $args["hasOkBtn"] == true
        ;

        $html =
                "<div class=\"modal\" id=\"alertModal\" tabindex=\"-1\">\n".
                "  <div class=\"modal-dialog\">\n".
                "    <div class=\"modal-content\">\n".
                "      <div class=\"modal-header\">\n".
                "        <h5 class=\"modal-title\">".$title."</h5>\n";
        if ($hasHeaderClose) {
            $html .=
                "        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n";
        }
        $html .=
                "      </div>\n".
                "      <div class=\"modal-body\">\n".
                "        <p>".$content."</p>\n".
                "        <p>Veuillez contacter le bourgmestre du village et le prévenir de cette erreur afin qu'il puisse la corriger</p>\n".
                "      </div>\n";
        if ($hasCloseBtn || $hasOkBtn)
        {
            $html .=
                "      <div class=\"modal-footer\">\n";
            if ($hasCloseBtn) {
                $html .=
                    "        <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Fermer</button>\n";
            }
            if ($hasOkBtn) {
                $html .=
                    "        <button type=\"button\" class=\"btn btn-primary\">Ok</button>\n";
            }
            $html .=
                "      </div>\n";
        }
        $html .=
                "    </div>\n".
                "  </div>\n".
                "  <script>\n".
                "    $(function() {\n".
                "      let alertModalEl = document.getElementById(\"alertModal\");\n".
                "      let alertModal = new bootstrap.Modal(alertModalEl);\n".
                "      alertModalEl.addEventListener(\"hidden.bs.modal\", function() {\n".
                "        $(\"#alertModal\").remove();\n".
                "      });\n".
                "      alertModal.show();\n".
                "    });\n".
                "  </script>\n".
                "</div>\n";

        echo $html;
    }

    public static function nettoyerStr(string $str):string {
        return htmlspecialchars(stripslashes(trim($str)));
    }
}
