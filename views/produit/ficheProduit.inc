<?php
/**
 * @var Produit $produit
 */
?>

<a class="card fiche_produit" style="width: 18rem;" href="/produit/<?=$produit->GetRefProduit()?>">
    <img src="/assets/<?=$produit->GetImgPath()?>" class="card-img-top" alt="<?=$produit->GetRefProduit()?>">
    <div class="card-body">
        <h5 class="card-title"><?=$produit->GetLibProduit()?></h5>
        <p class="card-text">Difficulté <?=$produit->GetDifficulte()->GetLibelle()?></p>
    </div>
    <div class="card-body">
        <h1 class="card-text prix_produit"><?=number_format($produit->GetPrix(),2)?> €</h1>
    </div>
</a>
