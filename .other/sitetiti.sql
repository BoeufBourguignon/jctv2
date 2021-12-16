-- Base de données : `sitetiti`

CREATE DATABASE IF NOT EXISTS `JeanCasseTete`;
USE `JeanCasseTete`;



DROP TABLE IF EXISTS `panier`; -- Pas utilisé
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
    `idCateg` int(11) NOT NULL AUTO_INCREMENT,
    `refCateg` varchar(20) NOT NULL,
    `libCateg` varchar(50) NOT NULL,
    `idParent` int(11) NULL,
    CONSTRAINT PRIMARY KEY (`idCateg`)
) AUTO_INCREMENT=1;

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
    `idProduit` int(11) NOT NULL AUTO_INCREMENT,
    `refProduit` varchar(20) NOT NULL,
    `imgPath` varchar(100) DEFAULT NULL,
    `libProduit` varchar(50) NOT NULL,
    `descProduit` varchar(2000) DEFAULT NULL,
    `idCateg` int(11) DEFAULT NULL,
    `prix` decimal(5,2) DEFAULT NULL,
    `difficulte` int(11) DEFAULT NULL,
    `qteStock` int(11) DEFAULT 0,
    `seuilAlerte` int(11) DEFAULT NULL,
    CONSTRAINT PRIMARY KEY (`idProduit`),
    CONSTRAINT `fk_produit_categ` FOREIGN KEY (`idCateg`) REFERENCES categorie(`idCateg`),
    CONSTRAINT `fk_produit_difficulte` FOREIGN KEY (`difficulte`) REFERENCES difficulte(`idDifficulte`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `panier` (
    `idClient` int(11) NOT NULL,
    `idProduit` int(11) NOT NULL,
    CONSTRAINT PRIMARY KEY (`idClient`,`idProduit`),
    CONSTRAINT `fk_panier_produit` FOREIGN KEY (`idProduit`) REFERENCES produit(`idProduit`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `ligneCommande` (
    `idCommande` int(11) NOT NULL,
    `idProduit` int(11) NOT NULL,
    CONSTRAINT PRIMARY KEY (`idCommande`, `idProduit`),
    CONSTRAINT `fk_ligneCommande_commande` FOREIGN KEY (`idCommande`) REFERENCES commande(`idCommande`),
    CONSTRAINT `fk_ligneCommande_produit` FOREIGN KEY (`idProduit`) REFERENCES produit(`idProduit`)
) AUTO_INCREMENT=1;



DROP VIEW IF EXISTS V_Produits;



CREATE VIEW IF NOT EXISTS V_Produits
AS
SELECT idProduit, refProduit, p.idCateg as idCateg, null as idSousCateg, imgPath, libProduit, descProduit, prix, difficulte
FROM produit p
    JOIN categorie c on p.idCateg = c.idCateg
WHERE c.idParent IS NULL
UNION
SELECT idProduit, refProduit, idParent as idCateg, p.idCateg as idSousCateg, imgPath, libProduit, descProduit, prix, difficulte
FROM produit p
    JOIN categorie c on p.idCateg = c.idCateg
WHERE c.idParent IS NOT NULL
;



INSERT INTO `categorie` (`idCateg`, `refCateg`, `libCateg`) VALUES
    (1, 'bois', 'Casse tête en bois'),
    (2, 'metal', 'Casse tête en métal'),
    (3, 'geometrique', 'Casse tête géométrique'),
    (4, 'rubiks-cube', 'Rubik’s Cube');

INSERT INTO `categorie` (`refCateg`, `libCateg`, `idParent`) VALUES
    ('classique', 'Classique', 1),
    ('boite', 'Boites secrètes', 1),
    ('bouteille', 'Porte-bouteille', 1),
    ('hanayama', 'Hanayama', 2),
    ('cadenas', 'Cadenas', 2),
    ('cryptex', 'Cryptex', 2),
    ('tangram', 'Tangram', 3),
    ('puzzle', 'Puzzle', 3);

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

INSERT INTO `produit` (`idProduit`, `refProduit`, `imgPath`, `libProduit`, `descProduit`, `idCateg`, `prix`, `difficulte`) VALUES
    (1, 'RC2', 'img/produits/rc/2.png', 'Rubik’s Cube 2x2x2', '§sLe Rubik’s Cube est un casse-tête composé de 8 petits cubes de couleur, chaque face comportant 4 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.§pParfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.§e', 4, '6.00', 2),
    (2, 'RC3', 'img/produits/rc/3.png', 'Rubik’s Cube 3x3x3', '§sLe Rubik’s Cube est un casse-tête composé de 26 petits cubes de couleur, chaque face comportant 9 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.§pParfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.§e</p>', 4, '12.00', 3),
    (3, 'RC4', 'img/produits/rc/4.png', 'Rubik’s Cube 4x4x4', '§sLe Rubik’s Cube est un casse-tête composé de 60 petits cubes de couleur, chaque face comportant 16 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.§pParfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.§e</p>', 4, '20.00', 4),
    (4, 'RC5', 'img/produits/rc/5.png', 'Rubik’s Cube 5x5x5', '§sLe Rubik’s Cube est un casse-tête composé de 99 petits cubes de couleur, chaque face comportant 25 cubes, fixés à un axe central qui permet leur déplacement, afin de les disposer par couleur sur chaque face du cube.§pParfait pour débuter, comprendre le fonctionnement des Rubik’s cube et se perfectionner. Combinant les mathématiques, l’art et la science, le Rubik’s Cube emblématique stimule votre cerveau et vous met au défi.§e</p>', 4, '24.00', 5),
    (5, 'b-etoile', 'img/produits/bois/classique/etoile.png', 'Etoile de Galilée', '§sDans la somptueuse galaxie des Casse-têtes en bois, se niche une étoile singulière qui brille par la logique de sa solution. On ne l’appelle pas étoile de Galilée en référence à sa complexité, mais plutôt en hommage à la subtilité géométrique dont il faut faire preuve pour espérer la dompter.§pSix blocs identiques en bois massif formeront, avec un peu de patience, beaucoup de logique et un minimum de dextérité, deux parties dissemblables qu’il faudra réassembler pour reconstituer l’astre galiléen.§e', 5, '8.00', 2),
    (6, 'b-boule', 'img/produits/bois/classique/boule.png', 'Boule infernale', '§sVoici un incontournable du monde des casse-têtes : la boule en bois. Vous cherchez un casse-tête amusant et aux finitions de qualité ? Cet objet est conçu en bois de bambou lisse au toucher, pour vous garantir la plus grande satisfaction possible. Ce casse-tête en bois, suffisamment complexe mais très accessible, est parfait pour les enfants ! Il vous faudra séparer les 12 pièces du casse-tête, puis reformer la boule.§pAucune forme géométrique n''est plus simple qu''une boule. Mais lorsque les créateurs de casse-tête s''en mêlent, rien n''est plus incertain... Commandez votre boule en bois, et entrez dans l''univers des jeux de réflexion par la grande porte !§e', 5, '7.50', 3),
    (7, 'b-diamant', 'img/produits/bois/classique/diamant.png', 'Cube diamant', '§sUn casse-tête difficile à l’esthétique particulièrement soignée grâce à des bois de plusieurs couleurs.§pIl vous faudra de la patience et de la logique. Mais n''oubliez pas, pour résoudre un casse-tête, vous n''avez pas besoin de forcer et il y a toujours une solution !§pLe casse-tête "Diamant" est composé de 12 pièces. Il est fabriqué dans un bois issu de forêts gérées durablement.§e', 5, '8.50', 2),
    (8, 'bt-pandore', 'img/produits/bois/boite/pandore.png', 'Boite de pandore', '§sCet article est une boîte magique, qui est idéal pour cacher des choses que vous ne voulez pas être facilement vu par les curieux d''autres§pIl est encore plus sécurisé à être verrouillé par trois mécanismes de verrouillage séparées et véritable tiroir à l''intérieur pour vous de garder vos affaires en sécurité§sIdéal pour cacher des choses que vous ne voulez pas être facilement vu par les curieux d''autres§e', 6, '8.50', 1);



CREATE USER IF NOT EXISTS 'AppJCT'@'localhost' IDENTIFIED BY 'sk4#Srvmpcrci';
GRANT SELECT, INSERT, DELETE ON jeancassetete.* TO 'AppJCT'@'localhost';