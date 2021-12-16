
    <a class="card fiche_produit" style="width: 18rem;" href="index.php?controller=produit&produit=<?=$produit->GetReference()?>">
        <img src="/assets/<?=$produit->GetImagePath()?>" class="card-img-top" alt="<?=$produit->GetReference()?>">
        <div class="card-body">
            <h5 class="card-title"><?=$produit->GetLibelle()?></h5>
            <p class="card-text">Difficulté <?=$produit->GetDifficulte()->GetLibelle()?></p>
        </div>
        <div class="card-body">
            <h1 class="card-text prix_produit"><?=number_format($produit->GetPrix(),2)?> €</h1>
        </div>
    </a>
