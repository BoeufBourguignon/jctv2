<?php
/**
 * @var Produit $produit
 */
$qteRestante = $produit->GetQteStock() - $produit->GetQteCommandee();
?>

<a class="card fiche_produit <?php if($qteRestante <= 0) { echo "border-danger"; } ?>" style="width: 18rem;"
   href="/produit/<?php echo $produit->GetRefProduit()?>">
    <img src="/assets/<?php echo $produit->GetImgPath()?>" class="card-img-top" alt="<?php echo $produit->GetRefProduit()?>">
    <div class="card-body">
        <h5 class="card-title"><?php echo $produit->GetLibProduit()?></h5>
        <p class="card-text">Difficulté <?php echo $produit->GetDifficulte()->GetLibelle()?></p>
    </div>
    <div class="card-body">
        <?php
        if($qteRestante <= 0) {
            ?>
            <p class="text-danger">Rupture de stock</p>
            <?php
        } else {
            ?>
            <h1 class="card-text prix_produit"><?php echo $produit->GetPrixFormatted()?> €</h1>
            <?php
        }
        ?>
    </div>
</a>
