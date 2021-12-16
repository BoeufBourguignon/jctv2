<div class="fiche_produit" data-categ="<?=$produit->GetCateg()?>" data-difficulte="<?=$produit->GetDifficulte()->GetLibelle()?>">
    <img alt="<?=$produit->GetReference()?>" src="assets/<?=$produit->GetImagePath()?>">
    <h1 class="lib_produit"><?=$produit->GetLibelle()?></h1>
    <p class="difficulte">Difficulté: <?=$produit->GetDifficulte()->GetLibelle()?></p>
    <h2 class="prix_produit"><?=number_format($produit->GetPrix(),2)?> €</h2>
</div>