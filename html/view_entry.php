<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css" type="text/css" />
    <title>Recherche des Entrées Uniprot</title> 
    <?php require("lib.php"); ?>
  </head>
  <body>
    <h1>Recherche par Entrée</h1><hr>
    <form method="get" action="view_entry.php">
      Entrer un numéro d'accession valide
      <input type="text" name="accession" value=<?=value_text("accession")?>> 
      <input type="submit" name="submit" value="Rechercher">
    </form>
    <a href="filter_entry.php">Recherche par filtrage</a><br>
    <a href=index.html>RETOUR</a>
    <?php
    
    require("config.php");
    $connexion = oci_connect($USER, $PASSWD, 'dbinfo');
    
    if(array_key_exists('accession', $_REQUEST)){
        $array_ac = checkAccesion($_REQUEST['accession'], $connexion);
        if (count($array_ac) == 1){
            $ac = $array_ac[0];
            print("<h1> Résultat de la recherche : </h1>");
            info_Seq($ac,$connexion);
            info_Prot($ac,$connexion);
            info_Gene($ac,$connexion);
            info_keyword($ac,$connexion);
            info_comment($ac,$connexion);
            info_termGo($ac,$connexion);
            oci_close($connexion);
        }
        else if (count($array_ac) > 1) {
          print("<p>Plusieurs entrées ont été trouvées.");
          print("<form method='GET' action='view_entry.php'><table>");
          print("<tr><th>Entrées</th></tr>");
          foreach ($array_ac as $index => $ac) {
            print(
              "<tr><td><button type='submit' name='accession' value='"
              .$ac."'>".$ac."</button></tr></td>"
            );
          }
          print("</table></form>");
        }
        else{
            print("<h1> Mauvais numéro d'accession");
        }
    }
     
    // vérification de l'existance du numéros d'accession
    // return: array(accession)
    function checkAccesion($accession, $connexion){

        $txtReq = "select entries.accession"
                ." from  entries"
                // Recherche insensible à la casse
                ." where REGEXP_LIKE (entries.accession, :acces, 'i') ";

        $ordre = oci_parse($connexion, $txtReq);
        oci_bind_by_name($ordre, ":acces", $accession);
        oci_execute($ordre);
        
        $res = array();
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            array_push($res, $row[0]);     
        }
        
        oci_free_statement($ordre);
        return $res;
     
    }  
    
    // information sur la séquence d'une protein  
    function info_Seq($accession,$connexion){

        $txtReq = "select proteins.seq, proteins.seqLength, proteins.seqMass, entries.specie"
                ." from  proteins,entries"
                ." where entries.accession= :acces and proteins.accession = entries.accession";

        $ordre = oci_parse($connexion, $txtReq);
        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);
        print("<h2> Informations sur la séquence :</h2><br>");
        print("<table class='seq' width=70% border='1'><tr><th>Sequence</th><th>Longueur</th><th>Masse</th><th>reférence NCBI</th><tr>");
       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            //clob to string  : load() / read(2000)
            $lien = "<a href=https://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=".$row[3] ."> lien </a>";
            print("<tr><td>". $row[0] -> load() ."</td><td>". $row[1]  ."</td><td>". $row[2] ."</td><td> ". $lien ."</td></tr>");
        }
        print("</table><br>");
        oci_free_statement($ordre);
    }

    //noms des protéines avec leurs types et sortes,
    function info_Prot($accession,$connexion){

        $txtReq = "select protein_names.prot_name, protein_names.name_type,protein_names.name_kind" 
                ." from protein_names , prot_name_2_prot"
                ." where prot_name_2_prot.accession = :acces
                and prot_name_2_prot.prot_name_id = protein_names.prot_name_id";


        $ordre = oci_parse($connexion, $txtReq);
        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Informations sur la protéine :</h2><br>");
        print("<table class='prot' width=70%  border='1'><tr><th>Noms des proteins </th><th> Type de nom </th><th> Genre de nom</th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            print("<tr><td>". $row[0] ."</td><td>". $row[1]  ."</td><td>". $row[2] ."</td></tr>");   
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }
     
     //noms des gènes et leurs types
     function info_Gene($accession,$connexion){  

        $txtReq = "select gene_names.gene_name, gene_names.name_type"
                ." from  entry_2_gene_name, gene_names"
                ." where entry_2_gene_name.accession =:acces
                 and entry_2_gene_name.gene_name_id = gene_names.gene_name_id";


        $ordre = oci_parse($connexion, $txtReq);
        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Informations sur le gène :</h2><br>");
        print("<table  class='gene' width=70%  border='1'><tr><th>Nom des gènes</th><th> Type de nom </th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            print("<tr><td>". $row[0] ."</td><td>". $row[1]  ."</td></tr>");
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }
     
     //mot clé  et leurs id liés au numéro d'accession
     function info_keyword($accession,$connexion){

        $txtReq ="select keywords.kw_id , keywords.kw_label"
                ." from  keywords,entries_2_keywords"
                ." where entries_2_keywords.accession = :acces
                and entries_2_keywords.kw_id = keywords.kw_id ";


        $ordre = oci_parse($connexion, $txtReq);
        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Mots-clés:</h2><br>");
        print("<table class='kw_comment' width=70%  border='1'><tr><th> Id du mot clé </th><th> Mot clé </th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            print("<tr><td>". $row[0] ."</td><td>". $row[1]  ."</td></tr>");   
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }
     
     // commentaire(s) lié(s) au numéro d'accessio
     function info_comment($accession,$connexion){

        $txtReq ="select comments.comment_id, comments.type_c ,comments.txt_c"
                ." from  comments"
                ." where comments.accession = :acces";

        $ordre = oci_parse($connexion, $txtReq);

        oci_bind_by_name($ordre, ":acces", $accession);
        oci_execute($ordre);

        print("<h2> Commentaires :</h2><br>");
        print("<table class='kw_comment' width=70%  border='1'><tr><th> Id du commentaire </th><th> Type de commentaire </th><th> Commentaire </th><tr>");
       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            print("<tr><td>". $row[0] ."</td><td>". $row[1]  ."</td><td>" . $row[2] ."</td></tr>");     
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }

     // information relative aux termes  GO 
     function info_termGo($accession,$connexion){ 

        $txtReq = "select dbref.db_ref" 
            ." from dbref"
            ." where dbref.accession= :acces" 
            ." and dbref.db_type = 'GO'";  

        $ordre = oci_parse($connexion, $txtReq);
        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Informations relatives aux termes GO  :</h2><br>");
        print("<table class='kw_comment' width=70%  border='1'><tr><th>reférence GO</th><th> Lien  </th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            $lienEbi = "<a href=https://www.ebi.ac.uk/QuickGO/term/".$row[0] ."> https://www.ebi.ac.uk/QuickGO/term/".$row[0] ."</a>"; 
            print("<tr><td>". $row[0] ."</td><td>". $lienEbi ."</td></tr>");   
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }

    ?>
</body>
</html>
