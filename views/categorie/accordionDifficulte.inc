<?php
/**
 * @var $difficulties array
 * @var $triDifficulte
 */
$isDifficulteCollapsed = !($triDifficulte !== null);
?>
<div class="accordion-item">
    <h2 class="accordion-header" id="filters_heading_difficulty">
        <button class="accordion-button <?php if ($isDifficulteCollapsed) echo "collapsed" ?>" type="button"
                data-bs-toggle="collapse" data-bs-target="#filters_collapse_difficulty"
                aria-expanded="<?php echo $isDifficulteCollapsed ? "false" : "true" ?>"
                aria-controls="filters_collapse_difficulty">
            Difficulté
        </button>
    </h2>
    <div id="filters_collapse_difficulty"
         class="accordion-collapse collapse <?php if (!$isDifficulteCollapsed) echo "show" ?>"
         aria-labelledby="filters_heading_difficulty">
        <div class="accordion-body">
            <?php
            /**
             * @var $difficulty Difficulty
             */
            foreach ($difficulties as $difficulty) {
                ?>
                <div class="form-check">
                    <input name="difficulty[]" class="form-check-input" type="checkbox"
                           value="<?php echo $difficulty->GetId() ?>" id="difficulte-<?php echo $difficulty->GetId() ?>"
                           <?php if(!$isDifficulteCollapsed && in_array($difficulty->GetId(), $triDifficulte)) echo "checked" ?>>
                    <label class="form-check-label" for="difficulte-<?php echo $difficulty->GetId() ?>">
                        <?php echo $difficulty->GetLibelle() ?>
                    </label>
                </div>
                <?php
            }
            ?>
            <p class="filters_select_all text-blue" id="filters_select_difficulties"
               data-state="0">Tout sélectionner / déselectionner</p>
        </div>
    </div>
</div>