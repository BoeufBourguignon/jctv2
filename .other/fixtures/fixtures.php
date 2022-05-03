<?php
try {
    $dsn = 'mysql:host=localhost;port=3307;dbname=jeancassetete;charset=utf8';
    $pdo = new PDO($dsn, 'root', '');

    //Vide les tables concernées
    $pdo->exec("delete from commande");
    $pdo->exec("alter table commande AUTO_INCREMENT = 1");
    $pdo->exec("delete from lignecommande");
    $pdo->exec("alter table lignecommande AUTO_INCREMENT = 1");
    $pdo->exec("delete from suivietatcommande");
    $pdo->exec("alter table suivietatcommande AUTO_INCREMENT = 1");

    //Récupère les id de tous les clients
    $reqClients = $pdo->query("select idClient from client");
    $clients = $reqClients->fetchAll(PDO::FETCH_COLUMN);

    //Pour chaque client
    foreach($clients as $idClient) {
        //Nombre de commandes à créer
        $nbCommandes = random_int(1, 3);
        for($i = 0; $i < $nbCommandes; $i++) {
            //On crée la commande
            $reqNewCommande = $pdo->prepare("insert into commande (idClient) values (:idC)");
            $reqNewCommande->execute([":idC" => $idClient]);
            $idCommande = $pdo->lastInsertId();
            //On rajoute des produits à la commande
            $nbProduits = random_int(1, 5);
            $pdo->exec("insert into lignecommande (idCommande, refProduit, qte) select " . $idCommande . ", refProduit, RAND()*8+1 from produit order by rand() limit " . $nbProduits);
        }
    }
    //Pour chaque commande
    $reqCommandes = $pdo->query("select idCommande from commande");
    while($idCommande = $reqCommandes->fetch(PDO::FETCH_COLUMN)) {
        $etatCommande = random_int(1, 5);
        for($i = 1; $i <= $etatCommande; $i++) {
            $date = random_int(strtotime("now - 24 months"), strtotime("now"));
            $reqAddEtat = $pdo->prepare("insert into suivietatcommande (idCommande, idEtatCommande, date) values (:idC, :idE, :date)");
            $reqAddEtat->execute([
                ":idC" => $idCommande,
                ":idE" => $i,
                ":date" => date("Y-m-d", strtotime("- " . 5 - $i . " weeks", $date))
            ]);
        }
    }

    //On met aussi des qteStock et seuilAlerte aléatoires pour chaque produit
    $reqProduits = $pdo->query("select refProduit from produit");
    while($produit = $reqProduits->fetch(PDO::FETCH_COLUMN)) {
        $qteStock = random_int(1,20) * 5;
        $seuilAlerte = random_int(1,20) * 5;
        $reqSeuilQte = $pdo->prepare("update produit set qteStock = :qte, seuilAlerte = :seuil where refProduit = :ref");
        $reqSeuilQte->execute([":qte" => $qteStock, ":seuil" => $seuilAlerte, ":ref" => $produit]);
    }

} catch(Exception $e) {
    print("ERREUR Ln. " . $e->getLine() . " :" . $e->getMessage());
}
