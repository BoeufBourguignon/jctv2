<?php
GLOBAL $idCateg;

$sousCategs = CategorieManager::GetSousCategories($idCateg);
$difficulties = DifficultiesManager::GetLesDifficultes();
?>
<header id="categ_header">
    <?php
    if (count($sousCategs)) {
        ?>
        <div id="filter_categ">
            <label for="select_categ">Sous-catégorie</label>
            <select name="categ" id="select_categ">
                <option value="0">Afficher tout</option>
                <?php
                foreach($sousCategs as $sousCateg) {
                    echo "<option value=\"".$sousCateg["idCateg"]."\">".$sousCateg["libCateg"]."</option>";
                }
                ?>
            </select>
        </div>
        <?php
    }
    ?>
    <div id="filter_difficulte">
        <label for="select_difficulte">Difficulte</label>
        <select name="categ" id="select_difficulte">
            <option value="0">Afficher tout</option>
            <?php
            foreach($difficulties as $difficulte) {
                echo "<option value=\"".$difficulte["idDifficulte"]."\">".$difficulte["libDifficulte"]."</option>";
            }
            ?>
        </select>
    </div>
</header>
<div id="main_affic">
    <?php

    ?>
</div>



<style>
    header#categ_header {
    #filter_categ,
    #filter_difficulte {
    #select_categ,
    #select_difficulte {
        width: 140px;
    }
    padding: 8px 16px;
    }
    display:flex;
    border-bottom: solid 1px #262B2F;
    background-color: #f0f0f0;
    }
</style>