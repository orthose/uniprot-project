<html>
  <head>
    <meta charset="utf-8">
    <title>Recherche par filtrage des entrées Uniprot</title>
    <link rel="stylesheet" href="style.css" type="text/css"/>
    <?php require("lib.php"); ?>
  </head>
<body>
  <h1>Recherche par filtrage</h1>
  <p>
    Bienvenue dans l'outil de recherche par filtrage
    de la base de données Uniprot.
  </p>
  <form method="get" action="filter_entry.php">
    <label>Nom du gène</label>
    <input type="text" name="gene" value=<?=value_text("gene")?>><br>
    <label>Nom de protéine</label>
    <input type="text" name="protein" value=<?=value_text("protein")?>><br>
    <label>Commentaire associé</label>
    <input type="text" name="comment" value=<?=value_text("comment")?>><br>
    <input type="submit" value="Rechercher">
  </form>
  <a href="view_entry.php">Recherche par Entrée</a><br>
  <a href=index.html>RETOUR</a>
  <table>
    <?php
    
    // Exécute la requête et affiche les cases
    // du tableau de résultat de la recherche
    function request($ordre) {
      oci_execute($ordre);
      $count = 0;
      while (($row = oci_fetch_array($ordre, OCI_BOTH)) != false) {
        // Bouton cliquable pour accéder à la visualisation de l'entrée
        echo "<tr><td><button type='submit' name='accession' value='"
          .$row[0]."'>"
          .$row[0]."</button></td><td>"
          .$row[1]."</td></tr>";
        $count++;
      } 
      if ($count == 0) {
        echo "<tr><td colspan='2'>Aucune entrée trouvée</td><td></td></tr>";
      }
      oci_free_statement($ordre);
    }
    
    // Vérification que les champs ont été remplis
    $gene_isset = isset($_REQUEST['gene']) && !empty($_REQUEST['gene']);
    $protein_isset = isset($_REQUEST['protein']) && !empty($_REQUEST['protein']);
    $comment_isset = isset($_REQUEST['comment']) && !empty($_REQUEST['comment']);
    
    require("config.php");
    $connexion = oci_connect($USER, $PASSWD, 'dbinfo');
    
    // Les entrées sont des boutons cliquables
    // qui mènent à la page de visualisation des entrées
    echo "<form method='get' action='view_entry.php'>";
    
    if ($gene_isset) {
      // Entête du tableau de résultat
      echo "<tr><th>Entrée</th><th>Nom Gène</th></tr>";
      
      // Requête SQL en texte
      $txtReq = " SELECT entry_2_gene_name.accession, gene_names.gene_name "
        ." FROM gene_names, entry_2_gene_name "
        // Recherche insensible à la casse
        ." WHERE REGEXP_LIKE (gene_names.gene_name, :gene, 'i') "
        ." AND gene_names.gene_name_id = entry_2_gene_name.gene_name_id ";
        
      // Curseur vers la base
      $ordre = oci_parse($connexion, $txtReq);
      // Binding des variables de la requête avec variables PHP 
      oci_bind_by_name($ordre, ":gene", $_REQUEST['gene']);
      // Exécution de la requête 
      request($ordre);
    }
    
    if ($protein_isset) {
      echo "<tr><th>Entrée</th><th>Nom Protéine</th></tr>";
        
      $txtReq = " SELECT prot_name_2_prot.accession, protein_names.prot_name "
        ." FROM protein_names, prot_name_2_prot "
        ." WHERE REGEXP_LIKE (protein_names.prot_name, :protein, 'i') "
        ." AND protein_names.prot_name_id = prot_name_2_prot.prot_name_id ";
      
      $ordre = oci_parse($connexion, $txtReq); 
      oci_bind_by_name($ordre, ":protein", $_REQUEST['protein']);     
      request($ordre);
    }
    
    if ($comment_isset) {
      echo "<tr><th>Entrée</th><th>Commentaire</th></tr>";
        
      $txtReq = " SELECT DISTINCT accession, txt_c "
        ." FROM comments "
        ." WHERE REGEXP_LIKE (txt_c, :com, 'i') ";
      
      $ordre = oci_parse($connexion, $txtReq); 
      oci_bind_by_name($ordre, ":com", $_REQUEST['comment']);     
      request($ordre);
    }
    
    echo "</form>";
    oci_close($connexion);
    
    ?>
  </table>
</body>
</html>