<?php
try {
    $dsn = 'mysql:host=localhost;port=3307;dbname=jeancassetete;charset=utf8';
    $pdo = new PDO($dsn, 'root', '');

    //Vide les tables concernées
    $pdo->exec("delete from commande");
    $pdo->exec("delete from lignecommande");
    $pdo->exec("delete from suivietatcommande");

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
            $pdo->exec("insert into lignecommande (idCommande, refProduit) select " . $idCommande . ", refProduit from produit order by rand() limit " . $nbProduits);
        }
    }
    //Pour chaque commande
    $reqCommandes = $pdo->query("select idCommande from commande");
    while($idCommande = $reqCommandes->fetch(PDO::FETCH_COLUMN)) {
        $etatCommande = random_int(1, 5);
        for($i = 1; $i <= $etatCommande; $i++) {
            $reqAddEtat = $pdo->prepare("insert into suivietatcommande (idCommande, idEtatCommande, date) values (:idC, :idE, :date)");
            $reqAddEtat->execute([
                ":idC" => $idCommande,
                ":idE" => $i,
                ":date" => date("Y-m-d", strtotime("today - " . 5 - $i . " weeks"))
            ]);
        }
    }
} catch(Exception $e) {
    print("ERREUR Ln. " . $e->getLine() . " :" . $e->getMessage());
}
