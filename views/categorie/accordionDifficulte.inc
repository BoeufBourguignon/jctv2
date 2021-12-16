<?php
/**
 * @var $difficulties array
 */
?>
<div class="accordion-item">
    <h2 class="accordion-header" id="filters_heading_difficulty">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filters_collapse_difficulty" aria-expanded="false" aria-controls="filters_collapse_difficulty">
            Difficulté
        </button>
    </h2>
    <div id="filters_collapse_difficulty" class="accordion-collapse collapse" aria-labelledby="filters_heading_difficulty">
        <div class="accordion-body">
            <?php
            /**
             * @var $difficulty Difficulty
             */
            foreach($difficulties as $difficulty)
            {
                $html =
                    "<div class=\"form-check\">".
                    "  <input name=\"difficulty\" class=\"form-check-input\" type=\"checkbox\" value=\"".$difficulty->GetId()."\" id=\"difficulte-".$difficulty->GetId()."\">".
                    "  <label class=\"form-check-label\" for=\"difficulte-".$difficulty->GetId()."\">".
                    $difficulty->GetLibelle().
                    "  </label>".
                    "</div>";
                echo $html;
            }
            ?>
            <p class="filters_select_all text-jct-orange" id="filters_select_difficulties" data-state="0">Tout sélectionner / déselectionner</p>
        </div>
    </div>
</div>