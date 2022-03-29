<?php
/**
 * @var $sousCategs array
 */
?>
<div class="accordion-item">
    <h2 class="accordion-header" id="filters_heading_categ">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filters_collapse_categ" aria-expanded="false" aria-controls="filters_collapse_categ">
            Catégorie
        </button>
    </h2>
    <div id="filters_collapse_categ" class="accordion-collapse collapse" aria-labelledby="filters_heading_categ">
        <div class="accordion-body">
            <?php
            /**
             * @var $sousCateg Categorie
             */
            foreach($sousCategs as $sousCateg)
            {
                $html =
                    "<div class=\"form-check\">".
                    "  <input name=\"subcateg\" class=\"form-check-input\" type=\"checkbox\" value=\"" . $sousCateg->GetRef() . "\" id=\"subcateg-" . $sousCateg->GetRef() . "\">".
                    "  <label class=\"form-check-label\" for=\"subcateg-" . $sousCateg->GetRef() . "\">".
                    $sousCateg->GetLibelle() .
                    "  </label>".
                    "</div>";
                echo $html;
            }
            ?>
            <p class="filters_select_all" id="filters_select_categs" data-state="0">Tout sélectionner / déselectionner</p>
        </div>
    </div>
</div>