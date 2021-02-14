<html>
  <head>
    <link rel="stylesheet" href="style.css" type="text/css" />
    <meta charset="utf-8">
   <title>Outil de recherche Uniprot</title>
   <?php require("lib.php"); ?>
  </head>
  <body>
    <h1>Outil de recherche Uniprot</h1> 
    <?php website_header(); ?>
    <p>Bienvenue sur le site de consultation de la base de données 
    <a href="https://www.uniprot.org/">Uniprot</a> section protéine.</p>
    <h2>Utilisation basique de l'outil</h2>
    <p>
      Si vous connaissez déjà le numéro de fiche que vous souhaitez consulter 
      rendez-vous directement sur l'outil de 
      <a href="view_entry.php">recherche par entrée</a>.
      Vous aurez accès à toutes les informations concernant cette fiche.
      Notez qu'un numéro de fiche est de la forme P00533 par exemple.
    </p>
    <p>
      En revanche si vous n'avez pas de numéro de fiche, vous pouvez en trouver 
      à l'aide de l'outil de  
      <a href="filter_entry.php">recherche par filtrage</a>.
      Cet outil vous permet de sélectionner un ensemble de fiches à partir de 
      certains mots-clés pour les catégories suivantes : nom de gène, nom de 
      protéine et commentaire.
    </p>
    <h2>Données disponibles</h2>
    <p>
      Ce site met à votre disposition 
      <?php
        // Requête pour le fun ...
        require("config.php");
        $connexion = oci_connect($USER, $PASSWD, 'dbinfo');
        $txtReq = " SELECT COUNT(*) "
        ." FROM entries ";
        $ordre = oci_parse($connexion, $txtReq);
        oci_execute($ordre);
        $row = oci_fetch_array($ordre, OCI_BOTH);
        echo $row[0];
        oci_free_statement($ordre);
      ?> 
      fiches extraites de Uniprot. 
      Les données ont été parsées dans une base de données Oracle 
      depuis des 
      <a href="https://www.s-c-b.eu/uploads/l3bioinfo/UniProtDataTest1.xml">
      fichiers au format XML</a> avec Python.
    </p>
    <h2>Fonctionnalités implémentées</h2>
    <ul>
      <li>Entête d'accès rapide aux pages du site</li>
      <li>Accès rapide aux différentes rubriques des résultats</li>
      <li>Persistance des recherches dans les champs texte</li>
      <li>Possibilité de sélectionner une fiche parmi celles disponibles</li>
      <li>Recherche insensible à la casse pour les champs texte</li>
      <li>Proposition de plusieurs fiches en cas de recherche imprécise</li>
      <li>Consultation d'une fiche depuis l'outil de filtrage</li>
      <li>Rendu et interface graphiques agréables</li>
    </ul> 
    <hr>
    <h2>Détails sur le projet</h2>
    <p>
      Projet de l'option 
      <a href="https://www.s-c-b.eu/index.php/teaching/l3bioinfo">L3 Bio-Info</a> 
      Université Paris-Saclay Année 2021.<br> 
      Source du projet disponible sur 
      <a href="https://github.com/orthose/uniprot-project">ce dépôt privé</a> GitHub.<br>
      Étudiants : Maxime Vincent & Jeyanthan Markandu
    </p>
  </body>
</html>










