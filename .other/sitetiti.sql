-- Base de données : `sitetiti`

CREATE DATABASE IF NOT EXISTS `exams_jct`;
USE `exams_jct`;


DROP TABLE IF EXISTS `ligneCommande`; -- Pas utilisé
DROP TABLE IF EXISTS `suiviEtatCommande`;
DROP TABLE IF EXISTS `commande`; -- Utilisé dans ligneCommande
DROP TABLE IF EXISTS `etatCommande`; -- Utilisé dans commande
DROP TABLE IF EXISTS `produit`; -- Utilisé dans ligneCommande
DROP TABLE IF EXISTS `difficulte`; -- Utilisée dans produit
DROP TABLE IF EXISTS `categorie`; -- Utilisé dans produit
DROP TABLE IF EXISTS `user_connection`;
DROP TABLE IF EXISTS `client`; -- Utilisé dans commande
DROP TABLE IF EXISTS `role`; -- Utilisé dans Client
DROP TABLE IF EXISTS `etatCommande`;



CREATE TABLE IF NOT EXISTS `categorie` (
    `refCateg` varchar(20) NOT NULL PRIMARY KEY,
    `libCateg` varchar(50) NOT NULL,
    `refParent` varchar(20) NULL
) ENGINE=InnoDB;
ALTER TABLE `categorie`
    ADD CONSTRAINT `fk_categorie_parent` FOREIGN KEY (`refParent`) REFERENCES `categorie`(`refCateg`);

CREATE TABLE IF NOT EXISTS `role` (
    `idRole` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `libRole` varchar(20) DEFAULT NULL
) AUTO_INCREMENT=1, ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `client` (
    `idClient` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `loginClient` varchar(20) DEFAULT NULL,
    `passwordClient` varchar(100) DEFAULT NULL,
    `mailClient` varchar(40) DEFAULT NULL,
    `idRoleClient` int(11) DEFAULT NULL
) AUTO_INCREMENT=1, ENGINE=InnoDB;
ALTER TABLE client
    ADD CONSTRAINT `fk_client_role` FOREIGN KEY (idRoleClient) REFERENCES role(idRole);

CREATE TABLE IF NOT EXISTS `user_connection` (
    `idClient` int(11),
    `idConnection` varchar(50),
    `date` date default NOW(),
    PRIMARY KEY (`idClient`, `idConnection`)
) ENGINE=InnoDB;
ALTER TABLE user_connection
    ADD CONSTRAINT `fk_userConnection_client` FOREIGN KEY (idClient) REFERENCES client(idClient);

CREATE TABLE IF NOT EXISTS `difficulte` (
    `idDifficulte` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `libDifficulte` varchar(50) NOT NULL
) AUTO_INCREMENT=1, ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `etatCommande` (
    `idEtatCommande` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `libelleEtatCommande` VARCHAR(30) NOT NULL
) AUTO_INCREMENT=1, ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `commande` (
    `idCommande` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `idClient` int(11) NOT NULL,
    `adresse` varchar(50) NULL,
    `ville` varchar(50) NULL,
    `cp` varchar(5) NULL,
    `destinataire` varchar(50) NULL
) AUTO_INCREMENT=1, ENGINE=InnoDB;
ALTER TABLE commande
    ADD CONSTRAINT `fk_commande_client` FOREIGN KEY (idClient) REFERENCES client(idClient);

CREATE TABLE IF NOT EXISTS `suiviEtatCommande` (
    `idCommande` int(11) NOT NULL,
    `idEtatCommande` int(11) NOT NULL,
    `date` datetime NOT NULL,
    PRIMARY KEY (`idCommande`, `idEtatCommande`)
) ENGINE=InnoDB;
ALTER TABLE `suiviEtatCommande`
    ADD CONSTRAINT `fk_suivietatcommande_commande` FOREIGN KEY (idCommande) REFERENCES commande(idCommande),
    ADD CONSTRAINT `fk_suivietatcommande_etatcommande` FOREIGN KEY (idEtatCommande) REFERENCES etatCommande(idEtatCommande);

CREATE TABLE IF NOT EXISTS `produit` (
    `refProduit` varchar(20) NOT NULL PRIMARY KEY,
    `imgPath` varchar(100) DEFAULT NULL,
    `libProduit` varchar(50) NOT NULL,
    `descProduit` varchar(2000) DEFAULT NULL,
    `refCateg` varchar(20) DEFAULT NULL,
    `prix` decimal(5,2) DEFAULT NULL,
    `idDifficulte` int(11) DEFAULT NULL,
    `qteStock` int(11) DEFAULT 0,
    `seuilAlerte` int(11) DEFAULT NULL
) ENGINE=InnoDB;
ALTER TABLE produit
    ADD CONSTRAINT `fk_produit_categorie` FOREIGN KEY (refCateg) REFERENCES categorie(refCateg),
    ADD CONSTRAINT `fk_produit_difficulte` FOREIGN KEY (idDifficulte) REFERENCES difficulte(idDifficulte);

CREATE TABLE IF NOT EXISTS `ligneCommande` (
    `idCommande` int(11) NOT NULL,
    `refProduit` varchar(20) NOT NULL,
    `qte` int(11) NOT NULL,
    PRIMARY KEY (`idCommande`, `refProduit`)
) AUTO_INCREMENT=1, ENGINE=InnoDB;
ALTER TABLE ligneCommande
    ADD CONSTRAINT `fk_lignecommande_commande` FOREIGN KEY (idCommande) REFERENCES commande(idCommande),
    ADD CONSTRAINT `fk_lignecommande_produit` FOREIGN KEY (refProduit) REFERENCES produit(refProduit);



DROP VIEW IF EXISTS v_produits;
CREATE VIEW v_produits
AS
    SELECT refProduit, p.refCateg as refCateg, null as refSousCateg, imgPath, libProduit, descProduit, prix,
           idDifficulte, seuilAlerte, qteStock
    FROM produit p
        JOIN categorie c on p.refCateg = c.refCateg
    WHERE c.refParent IS NULL
    UNION
    SELECT refProduit, refParent as refCateg, p.refCateg as refSousCateg, imgPath, libProduit, descProduit, prix,
           idDifficulte, seuilAlerte, qteStock
    FROM produit p
        JOIN categorie c on p.refCateg = c.refCateg
    WHERE c.refParent IS NOT NULL
;

DROP VIEW IF EXISTS v_most_bought_products;
CREATE VIEW v_most_bought_products
AS
    SELECT refProduit, sum(qte) as qteTotale
    FROM lignecommande lc
    WHERE 1 != (SELECT max(idEtatCommande) FROM suivietatcommande sec WHERE sec.idCommande = lc.idCommande)
    GROUP BY refProduit
    ORDER BY 2 desc;
;

DROP VIEW IF EXISTS v_panier;
CREATE VIEW v_panier
AS
    SELECT c.idCommande, idClient, date
    FROM commande c
        JOIN suivietatcommande s ON c.idCommande = s.idCommande
    GROUP BY c.idCommande, idClient
    HAVING MAX(idEtatCommande) = 1
;


DROP PROCEDURE IF EXISTS quantiteCommandee;
DELIMITER &&
CREATE PROCEDURE quantiteCommandee (IN inRefProduit varchar(20))
BEGIN
    SELECT IFNULL(sum(qte), 0) as quantiteCommandee
    FROM lignecommande lc
    WHERE refProduit = inRefProduit
      AND EXISTS (SELECT idCommande
                  FROM suivietatcommande s1
                  WHERE lc.idCommande = s1.idCommande
                    AND idEtatCommande != 1);
END ; &&




INSERT INTO `categorie` (`refCateg`, `libCateg`) VALUES
    ('bois', 'Casse tête en bois'),
    ('metal', 'Casse tête en métal'),
    ('rubiks-cube', 'Rubik’s Cube');

INSERT INTO `categorie` (`refCateg`, `libCateg`, `refParent`) VALUES
    ('classique', 'Classique', 'bois'),
    ('boite', 'Boites secrètes', 'bois'),
    ('hanayama', 'Hanayama', 'metal'),
    ('cadenas', 'Cadenas', 'metal');

INSERT INTO `role` (`idRole`, `libRole`) VALUES
     (1, 'Admin'),
     (2, 'Client');

INSERT INTO `client` (`loginClient`, `passwordClient`, `mailClient`, `idRoleClient`) VALUES
    ('Thibaud', '$2y$10$.Rsd6h8hV9kmqF73J4CLhe9FU0sZs.o.kQaGDZ2WCNEwYpU0DaVY2', 'thibaud.leclere@gmail.com', 2),
    ('Baptiste', '$2y$10$.Rsd6h8hV9kmqF73J4CLhe9FU0sZs.o.kQaGDZ2WCNEwYpU0DaVY2', 'bapt.bray@gmail.com', 1),
    ('Gregory', '$2y$10$.Rsd6h8hV9kmqF73J4CLhe9FU0sZs.o.kQaGDZ2WCNEwYpU0DaVY2', 'grego.mache@gmail.com', 1),
    ('Dorian', '$2y$10$.Rsd6h8hV9kmqF73J4CLhe9FU0sZs.o.kQaGDZ2WCNEwYpU0DaVY2', 'dodo.president@gmail.com', 1);

INSERT INTO `difficulte` (`idDifficulte`, `libDifficulte`) VALUES
    (1, '&starf;&star;&star;&star;&star;'),
    (2, '&starf;&starf;&star;&star;&star;'),
    (3, '&starf;&starf;&starf;&star;&star;'),
    (4, '&starf;&starf;&starf;&starf;&star;'),
    (5, '&starf;&starf;&starf;&starf;&starf;');

INSERT INTO `etatCommande` (`libelleEtatCommande`) VALUES
    ('Non validée'),
    ('Préparation'),
    ('Pris en charge'),
    ('En cours d\'acheminement'),
    ('Livré');

INSERT INTO `produit` (`refProduit`, `libProduit`, `descProduit`, `refCateg`, `prix`, `idDifficulte`) VALUES
    ('RC2', 'Rubik’s Cube 2x2x2',
        'Le Rubik’s Cube est un casse-tête composé de 8 petits cubes de couleur, chaque face comportant 4 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.
        Parfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.'
        , 'rubiks-cube', '6.00', 2),
    ('RC3', 'Rubik’s Cube 3x3x3',
        'Le Rubik’s Cube est un casse-tête composé de 26 petits cubes de couleur, chaque face comportant 9 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.
        Parfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.'
        , 'rubiks-cube', '12.00', 3),
    ('RC4', 'Rubik’s Cube 4x4x4',
        'Le Rubik’s Cube est un casse-tête composé de 60 petits cubes de couleur, chaque face comportant 16 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.
        Parfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.'
        , 'rubiks-cube', '20.00', 4),
    ('RC5', 'Rubik’s Cube 5x5x5',
        'Le Rubik’s Cube est un casse-tête composé de 99 petits cubes de couleur, chaque face comportant 25 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.
        Parfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.'
        , 'rubiks-cube', '24.00', 5),
    ('b-etoile', 'Etoile de Galilée',
        'Dans la somptueuse galaxie des Casse-têtes en bois, se niche une étoile singulière qui brille par la logique de sa solution. On ne l’appelle pas étoile de Galilée en référence à sa complexité, mais plutôt en hommage à la subtilité géométrique dont il faut faire preuve pour espérer la dompter.
        Six blocs identiques en bois massif formeront, avec un peu de patience, beaucoup de logique et un minimum de dextérité, deux parties dissemblables qu’il faudra réassembler pour reconstituer l’astre galiléen.'
        , 'classique', '8.00', 2),
    ('b-boule', 'Boule infernale',
        'Voici un incontournable du monde des casse-têtes : la boule en bois. Vous cherchez un casse-tête amusant et aux finitions de qualité ? Cet objet est conçu en bois de bambou lisse au toucher, pour vous garantir la plus grande satisfaction possible. Ce casse-tête en bois, suffisamment complexe mais très accessible, est parfait pour les enfants ! Il vous faudra séparer les 12 pièces du casse-tête, puis reformer la boule.
        Aucune forme géométrique n''est plus simple qu''une boule. Mais lorsque les créateurs de casse-tête s''en mêlent, rien n''est plus incertain... Commandez votre boule en bois, et entrez dans l''univers des jeux de réflexion par la grande porte !'
        , 'classique', '7.50', 3),
    ('b-diamant', 'Cube diamant',
        'Un casse-tête difficile à l’esthétique particulièrement soignée grâce à des bois de plusieurs couleurs.
        Il vous faudra de la patience et de la logique. Mais n''oubliez pas, pour résoudre un casse-tête, vous n''avez pas besoin de forcer et il y a toujours une solution !
        Le casse-tête "Diamant" est composé de 12 pièces. Il est fabriqué dans un bois issu de forêts gérées durablement.'
        , 'classique', '8.50', 2),
    ('bt-pandore', 'Boite de pandore',
        'Cet article est une boîte magique, qui est idéal pour cacher des choses que vous ne voulez pas être facilement vu par les curieux d''autres
        Il est encore plus sécurisé à être verrouillé par trois mécanismes de verrouillage séparées et véritable tiroir à l''intérieur pour vous de garder vos affaires en sécurité
        Idéal pour cacher des choses que vous ne voulez pas être facilement vu par les curieux d''autres'
        , 'boite', '8.50', 1),
    ('bt-yosegi', 'Yosegi box',
         'Véritable boîte de puzzle artisanale fabriquée au Japon. Tous les panneaux se déplacent en glissant. S’ouvre au 21e mouvement.
         La surface est recouverte de feuilles de mosaïque de bois de toutes les couleurs.'
        , 'boite', '40', 4),
    ('h-ring', 'Cast ring',
     'Ce puzzle est basé sur un original appelé le "Puzz-Ring". Popularisé dans l''Europe du XVe siècle, ce style de bague était en fait utilisé pour les bagues de fiançailles et de mariage officielles.
     Le réformateur chrétien, Martin Luther, était même connu pour en porter un. Il est également répandu pour prouver l''adultère de son porteur… quand il se désagrège en morceaux'
        , 'hanayama', '16', 4),
    ('h-key', 'Cast key',
     'Le Cast key est l''un des puzzles les plus vendus des Hanayama. Nous avons créé une nouvelle version de ce puzzle mettant l''accent sur le style et la précision, en tant que PARTIE II. Conçu par Otake et Wong.
     L''objectif est de séparer les deux clés puis de les remettre dans leur état initial. Cela a l''air simple, mais en fait, il s''agit d''une petite astuce. Les débutants doivent donc faire attention à ne pas les séparer de force.'
        , 'hanayama', '14', 3),
    ('h-chain', 'Cast chain',
     'Il y a toute une sagesse enveloppée dans ce chef-d''œuvre d''Oskar. Les trois pièces peuvent être séparées puis assemblées à nouveau dans leur forme originale. La particularité de ce puzzle est qu''il peut être résolu de trois manières différentes, selon laquelle des trois pièces est choisie comme pièce centrale.
     Résoudre ce casse-tête nécessite un travail particulièrement subtil des pièces, le genre qui laisse souvent la victime aux prises avec la frustration.'
        , 'hanayama', '14', 5),
    ('c-trick5', 'Trick lock 5',
     'Le Push Trick Lock 5 est un cadenas Casse-tête à 7 leviers de difficulté modérée. Toutefois, sa taille de 10 cm et son poids de 600 grammes sauront opposer une résistance sérieuse à tes tentatives désespérées de crochetage.
     En regardant la bête de plus près, tu constateras qu’il y a trois clés pour deux serrures… Lesquelles te faut-il utiliser ? Dans quel ordre ? De quelle façon ? Et d’ailleurs ces serrures et ces clés ne sont-elles pas des leurres ?'
        , 'cadenas', '22.50', 4),
    ('c-superlock', 'Super Lock',
     'Ce Super Lock impressionne tout d’abord par son aspect massif, son poids et ses dimensions… 17 cm de haut et 1,4kg de plaques d’acier et de laiton…
     Voilà de quoi chagriner le cambrioleur le plus endurci.'
        , 'cadenas', '500', 4);


CREATE USER IF NOT EXISTS 'AppJCT'@'localhost' IDENTIFIED BY 'sk4#Srvmpcrci';
GRANT SELECT, INSERT, DELETE, UPDATE
    ON categorie
    TO 'AppJCT'@'localhost';
GRANT SELECT, UPDATE
    ON client
    TO 'AppJCT'@'localhost';
GRANT SELECT, INSERT, UPDATE
    ON commande
    TO 'AppJCT'@'localhost';
GRANT SELECT
    ON difficulte
    TO 'AppJCT'@'localhost';
GRANT SELECT
    ON etatCommande
    TO 'AppJCT'@'localhost';
GRANT SELECT, INSERT, DELETE, UPDATE
    ON ligneCommande
    TO 'AppJCT'@'localhost';
GRANT SELECT, INSERT, DELETE, UPDATE
    ON produit
    TO 'AppJCT'@'localhost';
GRANT SELECT
    ON role
    TO 'AppJCT'@'localhost';
GRANT SELECT, INSERT
    ON suiviEtatCommande
    TO 'AppJCT'@'localhost';
GRANT SELECT, INSERT, DELETE
    ON user_connection
    TO 'AppJCT'@'localhost';
GRANT SELECT
    ON v_panier
    TO 'AppJCT'@'localhost';
GRANT SELECT
    ON v_produits
    TO 'AppJCT'@'localhost';
GRANT SELECT
    ON v_most_bought_products
    TO 'AppJCT'@'localhost';
GRANT EXECUTE
    ON PROCEDURE quantiteCommandee
    TO 'AppJCT'@'localhost';
