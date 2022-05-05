<?php
/**
 * @var $sousCategs array
 * @var $triSousCategs
 */
$isSousCategsCollapsed = !($triSousCategs !== null);
?>
<div class="accordion-item">
    <h2 class="accordion-header" id="filters_heading_categ">
        <button class="accordion-button <?php if ($isSousCategsCollapsed) echo "collapsed" ?>" type="button"
                data-bs-toggle="collapse" data-bs-target="#filters_collapse_categ"
                aria-expanded="<?php echo $isSousCategsCollapsed ? "false" : "true" ?>"
                aria-controls="filters_collapse_categ">
            Catégorie
        </button>
    </h2>
    <div id="filters_collapse_categ"
         class="accordion-collapse collapse <?php if (!$isSousCategsCollapsed) echo "show" ?>"
         aria-labelledby="filters_heading_categ">
        <div class="accordion-body">
            <?php
            /**
             * @var $sousCateg Categorie
             */
            foreach ($sousCategs as $sousCateg) {
                ?>
                <div class="form-check">
                    <input name="subcateg[]" class="form-check-input" type="checkbox"
                           value="<?php echo $sousCateg->GetRef() ?>" id="subcateg-<?php echo $sousCateg->GetRef() ?>"
                           <?php if(!$isSousCategsCollapsed && in_array($sousCateg->GetRef(), $triSousCategs)) echo "checked" ?>>
                    <label class="form-check-label" for="subcateg-<?php echo $sousCateg->GetRef() ?>">
                        <?php echo $sousCateg->GetLibelle() ?>
                    </label>
                </div>
                <?php
            }
            ?>
            <p class="filters_select_all text-blue" id="filters_select_categs" data-state="0">Tout sélectionner /
                déselectionner</p>
        </div>
    </div>
</div>