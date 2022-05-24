<?php
/**
 * @var Produit $produit
 */
?>

<a class="card fiche_produit" style="width: 18rem;" href="/produit/<?php echo $produit->GetRefProduit()?>">
    <img src="/assets/<?php echo $produit->GetImgPath()?>" class="card-img-top" alt="<?php echo $produit->GetRefProduit()?>">
    <div class="card-body">
        <h5 class="card-title"><?php echo $produit->GetLibProduit()?></h5>
        <p class="card-text">Difficulté <?php echo $produit->GetDifficulte()->GetLibelle()?></p>
    </div>
    <div class="card-body">
        <h1 class="card-text prix_produit"><?php echo $produit->GetPrixFormatted()?> €</h1>
    </div>
</a>
