-- Base de données : `sitetiti`

CREATE DATABASE IF NOT EXISTS `JeanCasseTete`;
USE `JeanCasseTete`;



DROP TABLE IF EXISTS `ligneCommande`; -- Pas utilisé
DROP TABLE IF EXISTS `commande`; -- Utilisé dans ligneCommande
DROP TABLE IF EXISTS `etatCommade`; -- Utilisé dans commande
DROP TABLE IF EXISTS `produit`; -- Utilisé dans ligneCommande
DROP TABLE IF EXISTS `difficulte`; -- Utilisée dans produit
DROP TABLE IF EXISTS `categorie`; -- Utilisé dans produit
DROP TABLE IF EXISTS `client`; -- Utilisé dans commande
DROP TABLE IF EXISTS `role`; -- Utilisé dans Client
DROP TABLE IF EXISTS `etatCommande`;



CREATE TABLE IF NOT EXISTS `categorie` (
    `refCateg` varchar(20) NOT NULL,
    `libCateg` varchar(50) NOT NULL,
    `refParent` int(11) NULL,
    CONSTRAINT PRIMARY KEY (`refCateg`),
    CONSTRAINT `fk_categorie_parent` FOREIGN KEY (`refCateg`) REFERENCES categorie(`refCateg`)
);

CREATE TABLE IF NOT EXISTS `role` (
    `idRole` int(11) NOT NULL AUTO_INCREMENT,
    `libRole` varchar(20) DEFAULT NULL,
    CONSTRAINT PRIMARY KEY (`idRole`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `client` (
    `idClient` int(11) NOT NULL AUTO_INCREMENT,
    `loginClient` varchar(20) DEFAULT NULL,
    `passwordClient` varchar(100) DEFAULT NULL,
    `mailClient` varchar(40) DEFAULT NULL,
    `idRoleClient` int(11) DEFAULT NULL,
    CONSTRAINT PRIMARY KEY (`idClient`),
    CONSTRAINT `fk_client_role` FOREIGN KEY (`idRoleClient`) REFERENCES role(`idRole`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `difficulte` (
    `idDifficulte` int(11) NOT NULL AUTO_INCREMENT,
    `libDifficulte` varchar(50) NOT NULL,
    CONSTRAINT PRIMARY KEY (`idDifficulte`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `etatCommande` (
    `idEtatCommande` INT(11) NOT NULL AUTO_INCREMENT,
    `libelleEtatCommande` VARCHAR(30) NOT NULL,
    CONSTRAINT PRIMARY KEY (`idEtatCommande`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `commande` (
    `idCommande` int(11) NOT NULL AUTO_INCREMENT,
    `idClient` int(11) NOT NULL,
    `idEtatCommand` int(11) NOT NULL,
    CONSTRAINT PRIMARY KEY (`idCommande`),
    CONSTRAINT `fk_commande_client` FOREIGN KEY (`idClient`) REFERENCES client(`idClient`),
    CONSTRAINT `fk_commande_etatCommande` FOREIGN KEY (`idEtatCommand`) REFERENCES etatCommande(`idEtatCommande`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `produit` (
    `refProduit` varchar(20) NOT NULL,
    `imgPath` varchar(100) DEFAULT NULL,
    `libProduit` varchar(50) NOT NULL,
    `descProduit` varchar(2000) DEFAULT NULL,
    `refCateg` int(11) DEFAULT NULL,
    `prix` decimal(5,2) DEFAULT NULL,
    `idDifficulte` int(11) DEFAULT NULL,
    `qteStock` int(11) DEFAULT 0,
    `seuilAlerte` int(11) DEFAULT NULL,
    CONSTRAINT PRIMARY KEY (`refProduit`),
    CONSTRAINT `fk_produit_categ` FOREIGN KEY (`refCateg`) REFERENCES categorie(`refCateg`),
    CONSTRAINT `fk_produit_difficulte` FOREIGN KEY (`idDifficulte`) REFERENCES difficulte(`idDifficulte`)
);

CREATE TABLE IF NOT EXISTS `ligneCommande` (
    `idCommande` int(11) NOT NULL,
    `refProduit` int(11) NOT NULL,
    CONSTRAINT PRIMARY KEY (`idCommande`, `refProduit`),
    CONSTRAINT `fk_ligneCommande_commande` FOREIGN KEY (`idCommande`) REFERENCES commande(`idCommande`),
    CONSTRAINT `fk_ligneCommande_produit` FOREIGN KEY (`refProduit`) REFERENCES produit(`refProduit`)
) AUTO_INCREMENT=1;



DROP VIEW IF EXISTS V_Produits;



CREATE VIEW IF NOT EXISTS V_Produits
AS
SELECT refProduit, p.refCateg as refCateg, null as refSousCateg, imgPath, libProduit, descProduit, prix, idDifficulte, seuilAlerte, qteStock
FROM produit p
    JOIN categorie c on p.refCateg = c.refCateg
WHERE c.refParent IS NULL
UNION
SELECT refProduit, refParent as refCateg, p.refCateg as refSousCateg, imgPath, libProduit, descProduit, prix, idDifficulte, seuilAlerte, qteStock
FROM produit p
    JOIN categorie c on p.refCateg = c.refCateg
WHERE c.refParent IS NOT NULL
;



INSERT INTO `categorie` (`refCateg`, `libCateg`) VALUES
    ('bois', 'Casse tête en bois'),
    ('metal', 'Casse tête en métal'),
    ('rubiks-cube', 'Rubik’s Cube');

INSERT INTO `categorie` (`refCateg`, `libCateg`, `refParent`) VALUES
    ('classique', 'Classique', 'bois'),
    ('boite', 'Boites secrètes', 'bois'),
    ('hanayama', 'Hanayama', 'metal'),
    ('cadenas', 'Cadenas', 'metal'),
    ('cryptex', 'Cryptex', 'metal');

INSERT INTO `role` (`idRole`, `libRole`) VALUES
     (1, 'Admin'),
     (2, 'Client');

INSERT INTO `client` (`loginClient`, `passwordClient`, `mailClient`, `idRoleClient`) VALUES
    ('Thibaud', '$2y$10$j9DkKe/R2UfNObhhv2D0ZO.evHWlSDLrAtIQveojcawO7FEu0lJEa', 'thibaud.leclere@gmail.com', 2);

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

INSERT INTO `produit` (`refProduit`, `imgPath`, `libProduit`, `descProduit`, `refCateg`, `prix`, `difficulte`) VALUES
    ('RC2', 'img/produits/rc/2.png', 'Rubik’s Cube 2x2x2',
        'Le Rubik’s Cube est un casse-tête composé de 8 petits cubes de couleur, chaque face comportant 4 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.
        §pParfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.'
        , 'rubiks-cube', '6.00', 2),
    ('RC3', 'img/produits/rc/3.png', 'Rubik’s Cube 3x3x3',
        'Le Rubik’s Cube est un casse-tête composé de 26 petits cubes de couleur, chaque face comportant 9 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.
        §pParfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.'
        , 'rubiks-cube', '12.00', 3),
    ('RC4', 'img/produits/rc/4.png', 'Rubik’s Cube 4x4x4',
        'Le Rubik’s Cube est un casse-tête composé de 60 petits cubes de couleur, chaque face comportant 16 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.
        §pParfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.'
        , 'rubiks-cube', '20.00', 4),
    ('RC5', 'img/produits/rc/5.png', 'Rubik’s Cube 5x5x5',
        'Le Rubik’s Cube est un casse-tête composé de 99 petits cubes de couleur, chaque face comportant 25 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.
        §pParfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.'
        , 'rubiks-cube', '24.00', 5),
    ('b-etoile', 'img/produits/bois/classique/etoile.png', 'Etoile de Galilée',
        'Dans la somptueuse galaxie des Casse-têtes en bois, se niche une étoile singulière qui brille par la logique de sa solution. On ne l’appelle pas étoile de Galilée en référence à sa complexité, mais plutôt en hommage à la subtilité géométrique dont il faut faire preuve pour espérer la dompter.
        §pSix blocs identiques en bois massif formeront, avec un peu de patience, beaucoup de logique et un minimum de dextérité, deux parties dissemblables qu’il faudra réassembler pour reconstituer l’astre galiléen.'
        , 'classique', '8.00', 2),
    ('b-boule', 'img/produits/bois/classique/boule.png', 'Boule infernale',
        'Voici un incontournable du monde des casse-têtes : la boule en bois. Vous cherchez un casse-tête amusant et aux finitions de qualité ? Cet objet est conçu en bois de bambou lisse au toucher, pour vous garantir la plus grande satisfaction possible. Ce casse-tête en bois, suffisamment complexe mais très accessible, est parfait pour les enfants ! Il vous faudra séparer les 12 pièces du casse-tête, puis reformer la boule.
        §pAucune forme géométrique n''est plus simple qu''une boule. Mais lorsque les créateurs de casse-tête s''en mêlent, rien n''est plus incertain... Commandez votre boule en bois, et entrez dans l''univers des jeux de réflexion par la grande porte !'
        , 'classique', '7.50', 3),
    ('b-diamant', 'img/produits/bois/classique/diamant.png', 'Cube diamant',
        'Un casse-tête difficile à l’esthétique particulièrement soignée grâce à des bois de plusieurs couleurs.
        §pIl vous faudra de la patience et de la logique. Mais n''oubliez pas, pour résoudre un casse-tête, vous n''avez pas besoin de forcer et il y a toujours une solution !§pLe casse-tête "Diamant" est composé de 12 pièces. Il est fabriqué dans un bois issu de forêts gérées durablement.'
        , 'classique', '8.50', 2),
    ('bt-pandore', 'img/produits/bois/boite/pandore.png', 'Boite de pandore',
        'Cet article est une boîte magique, qui est idéal pour cacher des choses que vous ne voulez pas être facilement vu par les curieux d''autres
        §pIl est encore plus sécurisé à être verrouillé par trois mécanismes de verrouillage séparées et véritable tiroir à l''intérieur pour vous de garder vos affaires en sécurité
        §pIdéal pour cacher des choses que vous ne voulez pas être facilement vu par les curieux d''autres'
        , 'boite', '8.50', 1),
    ('bt-yosegi', 'img/produits/bois/boite/yosegi.png', 'Yosegi box',
         'Véritable boîte de puzzle artisanale fabriquée au Japon. Tous les panneaux se déplacent en glissant. S’ouvre au 21e mouvement.
         §pLa surface est recouverte de feuilles de mosaïque de bois de toutes les couleurs.'
        , 'boite', '40', 4),
    ('h-ring', 'img/produits/metal/hanayama/ring.png', 'Cast ring',
     'Ce puzzle est basé sur un original appelé le "Puzz-Ring". Popularisé dans l''Europe du XVe siècle, ce style de bague était en fait utilisé pour les bagues de fiançailles et de mariage officielles.
     §pLe réformateur chrétien, Martin Luther, était même connu pour en porter un. Il est également répandu pour prouver l''adultère de son porteur… quand il se désagrège en morceaux'
        , 'hanayama', '16', 4),
    ('h-key', 'img/produits/metal/hanayama/key.png', 'Cast key',
     'Le Cast key est l''un des puzzles les plus vendus des Hanayama. Nous avons créé une nouvelle version de ce puzzle mettant l''accent sur le style et la précision, en tant que PARTIE II. Conçu par Otake et Wong.
     §pL''objectif est de séparer les deux clés puis de les remettre dans leur état initial. Cela a l''air simple, mais en fait, il s''agit d''une petite astuce. Les débutants doivent donc faire attention à ne pas les séparer de force.'
        , 'hanayama', '14', 3),
    ('h-chain', 'img/produits/metal/hanayama/chain.png', 'Cast chain',
     'Il y a toute une sagesse enveloppée dans ce chef-d''œuvre d''Oskar. Les trois pièces peuvent être séparées puis assemblées à nouveau dans leur forme originale. La particularité de ce puzzle est qu''il peut être résolu de trois manières différentes, selon laquelle des trois pièces est choisie comme pièce centrale.
     §pRésoudre ce casse-tête nécessite un travail particulièrement subtil des pièces, le genre qui laisse souvent la victime aux prises avec la frustration.'
        , 'hanayama', '14', 5),
    ('c-trick5', 'img/produits/metal/cadenas/trick5.png', 'Trick lock 5',
     'Le Push Trick Lock 5 est un cadenas Casse-tête à 7 leviers de difficulté modérée. Toutefois, sa taille de 10 cm et son poids de 600 grammes sauront opposer une résistance sérieuse à tes tentatives désespérées de crochetage.
     §pEn regardant la bête de plus près, tu constateras qu’il y a trois clés pour deux serrures… Lesquelles te faut-il utiliser ? Dans quel ordre ? De quelle façon ? Et d’ailleurs ces serrures et ces clés ne sont-elles pas des leurres ?'
        , 'cadenas', '22.50', 4),
    ('c-superlock', 'img/produits/metal/cadenas/superlock.png', 'Super Lock',
     'Ce Super Lock impressionne tout d’abord par son aspect massif, son poids et ses dimensions… 17 cm de haut et 1,4kg de plaques d’acier et de laiton…
     §pVoilà de quoi chagriner le cambrioleur le plus endurci.'
        , 'cadenas', '500', 4);


# CREATE USER IF NOT EXISTS 'AppJCT'@'localhost' IDENTIFIED BY 'sk4#Srvmpcrci';
# GRANT SELECT, INSERT, DELETE ON jeancassetete.* TO 'AppJCT'@'localhost';