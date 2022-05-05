<?php
/**
 * @var $triOrder
 * @var $triWay
 */
$isTriCollapsed = !($triOrder !== false || $triWay !== false);
?>
<div class="accordion-item">
    <h2 class="accordion-header" id="filters_heading_tri">
        <button class="accordion-button <?php if($isTriCollapsed) echo "collapsed" ?>" type="button"
                data-bs-toggle="collapse" data-bs-target="#filters_collapse_tri"
                aria-expanded="<?php echo $isTriCollapsed ? "false" : "true" ?>" aria-controls="filters_collapse_tri">
            Trier par
        </button>
    </h2>
    <div id="filters_collapse_tri" class="accordion-collapse collapse <?php if(!$isTriCollapsed) echo "show" ?>"
         aria-labelledby="filters_heading_tri">
        <div class="accordion-body">
            <select name="filters_tri_order" class="form-select" aria-label="Default select example">
                <option value="prix" selected>Prix</option>
                <option value="idDifficulte">Difficulté</option>
            </select>
            <div class="form-check">
                <input value="ASC" class="form-check-input" type="radio" name="filters_tri_way" id="order_asc" checked>
                <label class="form-check-label" for="order_asc">
                    Du - au +
                </label>
            </div>
            <div class="form-check">
                <input value="DESC" class="form-check-input" type="radio" name="filters_tri_way" id="order_desc">
                <label class="form-check-label" for="order_desc">
                    Du + au -
                </label>
            </div>
        </div>
    </div>
</div>