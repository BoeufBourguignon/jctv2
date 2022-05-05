<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jean CasseTête - Vente de casse-têtes en ligne</title>
    <link rel="icon" href="/assets/img/logov2.png">
    <!--STYLES-->
    <link rel="stylesheet" href="/assets/custom/styles/main.css">
    <!--OTHER JS-->
    <script src="/assets/others/popperJS-2/popperJS.js"></script>
    <script src="/assets/others/jquery-3.6.0-dist/jquery-3.6.0.js"></script>
    <script src="/assets/others/bootstrap-5.1.0-dist/js/bootstrap.js"></script>
    <script src="https://kit.fontawesome.com/22ce38b26b.js"></script>
</head>
<body>
<?php
//Navbar
require_once(VUES . "/header/afficHeader.phtml");
/**
 * @var $view
 * @var $params
 */
echo "<div id='main_wrapper'>";
require_once(VUES . "/" . $view);
echo "</div>";
?>
</body>
</html>