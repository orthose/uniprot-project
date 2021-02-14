<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css" type="text/css" />
    <title>Recherche des Entrées Uniprot</title> 
    <?php require("lib.php"); ?>
  </head>
  <body>
    <h1>Recherche par entrée</h1>
        <div class="menu">
            <ul>
                <li> <a href="index.html"> Home </a></li>
                <li> <a href="#"> Recherche par entrée </a></li>
                <li><a href="filter_entry.php"> Recherche par filtre </a></li>
            </ul>
        </div>
        
    <hr>
   <h3> Bienvenue dans l'outil de recherche par entrée de la base de données Uniprot. </h3>
          
    <form method="post" action="view_entry.php">
      
    <p> Entrer un numéro d'accession valide : </p>
    
      <input type="text" name="accession" value=<?=value_text("accession")?>> 
      <input type="submit" name="submit" value="Rechercher">
    </form>
  
    <p> Ou </p>
    <form method="post" action="view_entry.php">
        <p >Selectionner un numéro d'accession : </p>
        <?php 
         //methode permettant d'afficher tous les numéros d'accessions dans un une balise <select>
        getAccession()
         ?>
        <input type="submit" name="submit" value="Rechercher">
    </form>
   <hr>
    <?php

    //fichier config.php à compléter avec votre login court et mot de passe 
    require("config.php");
    $connexion = oci_connect($USER, $PASSWD, 'dbinfo');
    
    
    if(array_key_exists('accession', $_REQUEST)){
        
        $array_ac = checkAccesion($_REQUEST['accession'], $connexion);
        /*checkAccession est censé renvoyer un tableau à un élément
         contenant l'accession demandé par l'utilisateur 
        */
        if (count($array_ac) == 1){
            $ac = $array_ac[0];
            print("<h2> Résultat de la recherche pour le numéro d'accession " .  $ac   .": </h2>");

            //renvoyé un petit "menu" pour acceder facilement aux section trouvé
            print("<h2>Resultat par catégorie :</h2>
            <a href='#seq'>Sequence</a><br>
            <a href='#prot'>Protein</a><br>
            <a href='#gene'>Gene</a><br>
            <a href='#cle'>Mot clés</a><br>
            <a href='#comm'>Commentaire</a><br>
            <a href='#GO'>Go</a><br>");
               
            info_Seq($ac,$connexion);
            info_Prot($ac,$connexion);
            info_Gene($ac,$connexion);
            info_keyword($ac,$connexion);
            info_comment($ac,$connexion);
            info_termGo($ac,$connexion);
            oci_close($connexion);
        }
        /*
        cas ou le tableau renvoyé contient plus d'un numéro d'accession
        */
        else if (count($array_ac) > 1) {
          print("<h2>Plusieurs entrées ont été trouvées : </h2>");
          print("<form method='POST' action='view_entry.php'><table border=1>");
          print("<tr><th>Entrées</th></tr>");
          foreach ($array_ac as $index => $ac) {
            print(
              "<tr><td><button type='submit' name='accession' value='"
              .$ac."'>".$ac."</button></tr></td>"
            );
          }
          print("</table></form>");
        }
        // cas où il y aucun numéro d'accession similaire à celui demandé
        else{
            print("<h1> Mauvais numéro d'accession</h1>");
        }
    }

    //recuperer les numéros d'accession pour les affichers dans un select
    function getAccession(){
        
     
        require("config.php");
        $connexion = oci_connect($USER, $PASSWD, 'dbinfo');
        $txtReq = "select accession"." from  entries";


        $ordre = oci_parse($connexion, $txtReq);

        oci_execute($ordre);

        print("<select name='accession'>");
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            
          print("<option   value=".$row[0].">". $row[0] ."</option>") ;
             
        }
        print("</select>");
        oci_free_statement($ordre);
        oci_close($connexion);

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

        /* Pour un accession donné ,on renvoye l'ensemble des informations liés à la séquence ,masse,longueur ,
         et espèce de la protein lié à cette accession
        */
        $txtReq = "select proteins.seq, proteins.seqLength, proteins.seqMass, entries.specie"
                ." from  proteins,entries"
                ." where entries.accession= :acces and proteins.accession = entries.accession";

        $ordre = oci_parse($connexion, $txtReq);
        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);
        print("<h2> Informations sur la séquence :</h2><br>");
        //on crée un tableau contenant les longueurs nécessaire
        print("<table id='seq' width=70% border='1'><tr><th>Sequence</th><th>Longueur</th><th>Masse</th><th>reférence NCBI</th><tr>");
       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            /* clob to string  : load()  
            Pour afficher le contenu d'une variable de type CLOB en php
            une methode consiste à utiliser la methode load()
            */
            $lien = "<a href=https://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=".$row[3] ."> lien </a>";
            print("<tr><td>". $row[0] -> load() ."</td><td>". $row[1]  ."</td><td>". $row[2] ."</td><td> ". $lien ."</td></tr>");
        }
        print("</table><br>");
        oci_free_statement($ordre);
    }

    //noms des protéines avec leurs types et sortes,
    function info_Prot($accession,$connexion){


        /*
         On selectionne le nom, le type de nom et le genre de nom d'une protein liés à une
         accession donné
        */
        $txtReq = "select protein_names.prot_name, protein_names.name_type,protein_names.name_kind" 
                ." from protein_names , prot_name_2_prot"
                ." where prot_name_2_prot.accession = :acces
                and prot_name_2_prot.prot_name_id = protein_names.prot_name_id";


        $ordre = oci_parse($connexion, $txtReq);
        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Informations sur la protéine :</h2><br>");
        print("<table id='prot' width=70%  border='1'><tr><th>Noms des proteins </th><th> Type de nom </th><th> Genre de nom</th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            print("<tr><td>". $row[0] ."</td><td>". $row[1]  ."</td><td>". $row[2] ."</td></tr>");   
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }
     
     //noms des gènes et leurs types
     function info_Gene($accession,$connexion){  
        /*
        On renvoye le nom et le type du nom d'un gene pour un numéro d'accession donné
        */
        $txtReq = "select gene_names.gene_name, gene_names.name_type"
                ." from  entry_2_gene_name, gene_names"
                ." where entry_2_gene_name.accession =:acces
                 and entry_2_gene_name.gene_name_id = gene_names.gene_name_id";


        $ordre = oci_parse($connexion, $txtReq);
        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Informations sur le gène :</h2><br>");
        print("<table  id='gene' width=70%  border='1'><tr><th>Nom des gènes</th><th> Type de nom </th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            print("<tr><td>". $row[0] ."</td><td>". $row[1]  ."</td></tr>");
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }
     
     function info_keyword($accession,$connexion){
       
     // on renvoye le ou les mots clés  et leurs id liés au numéro d'accession
        $txtReq ="select keywords.kw_id , keywords.kw_label"
                ." from  keywords,entries_2_keywords"
                ." where entries_2_keywords.accession = :acces
                and entries_2_keywords.kw_id = keywords.kw_id ";


        $ordre = oci_parse($connexion, $txtReq);
        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Mots-clés:</h2><br>");
        print("<table id='cle' width=70%  border='1'><tr><th> Id du mot clé </th><th> Mot clé </th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            print("<tr><td>". $row[0] ."</td><td>". $row[1]  ."</td></tr>");   
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }
     
     function info_comment($accession,$connexion){

             // commentaire(s) lié(s) au numéro d'accessio
        $txtReq ="select comments.comment_id, comments.type_c ,comments.txt_c"
                ." from  comments"
                ." where comments.accession = :acces";

        $ordre = oci_parse($connexion, $txtReq);

        oci_bind_by_name($ordre, ":acces", $accession);
        oci_execute($ordre);

        print("<h2> Commentaires :</h2><br>");
        print("<table id='comm' width=70%  border='1'><tr><th> Id du commentaire </th><th> Type de commentaire </th><th> Commentaire </th><tr>");
       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            print("<tr><td>". $row[0] ."</td><td>". $row[1]  ."</td><td>" . $row[2] ."</td></tr>");     
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }

     function info_termGo($accession,$connexion){ 

             // information relative aux termes  GO pour un numéro d'accession donné
        $txtReq = "select dbref.db_ref" 
            ." from dbref"
            ." where dbref.accession= :acces" 
            ." and dbref.db_type = 'GO'";  

        $ordre = oci_parse($connexion, $txtReq);
        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Informations relatives aux termes GO  :</h2><br>");
        print("<table id='GO' width=70%  border='1'><tr><th>reférence GO</th><th> Lien  </th><tr>");

       
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
