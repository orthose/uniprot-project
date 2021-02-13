<html>
  <head> 
    <link rel="stylesheet" href="style.css" type="text/css" />
    <title> View Entry </title> 
  </head>
  <body>
    
    <?php
    $connexion = oci_connect('c##jmarkan_a', 'jmarkan_a', 'dbinfo');
    if(array_key_exists('accession', $_GET)){
        $ac = $_REQUEST['accession'];
        if(checkAccesion($ac,$connexion)){
           print("<h1> Résultat de la recherche : </h1>");
            info_Seq($ac,$connexion);
            info_Prot($ac,$connexion);
            info_Gene($ac,$connexion);
            info_Kw_Comment($ac,$connexion);
            info_termGo($ac,$connexion);
            oci_close($connexion);
        }
        else{
            print("<h1> Mauvais numéro d'accession");
        }
    }
   

    function checkAccesion($accession,$connexion){


        $txtReq = "select entries.accession"
                ." from  entries"
                ." where entries.accession= :acces ";


        $ordre = oci_parse($connexion, $txtReq);

        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);
        

        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            if(strcmp($row[0],$accession)==0){
                return TRUE;
            }
           
        }
        
        oci_free_statement($ordre);
        return FALSE;
     
    }    
    function info_Seq($accession,$connexion){


        $txtReq = "select proteins.seq, proteins.seqLength, proteins.seqMass, entries.specie"
                ." from  proteins,entries"
                ." where entries.accession= :acces and proteins.accession = entries.accession";


        $ordre = oci_parse($connexion, $txtReq);

        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);
        print("<h2> Information sur la séquence :</h2><br>");
        print("<table class='seq' width=70% border='1'><tr><th>Sequence</th><th>Longueur</th><th>Masse</th><th>reférence NCBI</th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
            //clob to string  : load() / read(2000)

            $lien = "<a href=https://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=".$row[3] ."> lien </a>";
            print("<tr><td>". $row[0] -> load() ."</td><td>". $row[1]  ."</td><td>". $row[2] ."</td><td> ". $lien ."</td></tr>");
             
        }
        print("</table><br>");
        oci_free_statement($ordre);
    }

    function info_Prot($accession,$connexion){
        //noms des protéines avec leurs types et sortes,


        $txtReq = "select protein_names.prot_name, protein_names.name_type,protein_names.name_kind" 
                ." from protein_names , prot_name_2_prot"
                ." where prot_name_2_prot.accession = :acces
                and prot_name_2_prot.prot_name_id = protein_names.prot_name_id";


        $ordre = oci_parse($connexion, $txtReq);

        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Information sur la protein :</h2><br>");

        print("<table class='prot' width=70%  border='1'><tr><th>Noms des proteins </th><th> Type de nom </th><th> Genre de nom</th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {

            print("<tr><td>". $row[0] ."</td><td>". $row[1]  ."</td><td>". $row[2] ."</td></tr>");
             
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }

     function info_Gene($accession,$connexion){
        //noms des gènes et leurs types


        $txtReq = "select gene_names.gene_name, gene_names.name_type"
                ." from  entry_2_gene_name, gene_names"
                ." where entry_2_gene_name.accession =:acces
                 and entry_2_gene_name.gene_name_id = gene_names.gene_name_id";


        $ordre = oci_parse($connexion, $txtReq);

        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Information sur le gene :</h2><br>");

        print("<table  class='gene' width=70%  border='1'><tr><th>Nom des gènes</th><th> Type de nom </th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {

            print("<tr><td>". $row[0] ."</td><td>". $row[1]  ."</td></tr>");
             
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }

     function info_Kw_Comment($accession,$connexion){
        //noms des gènes et leurs types


        $txtReq ="select keywords.kw_label , comments.comment_id, comments.type_c ,comments.txt_c"
                ." from entries_2_keywords, keywords , comments"
                ." where entries_2_keywords.accession= :acces
                and entries_2_keywords.kw_id = keywords.kw_id
                and comments.accession = entries_2_keywords.accession";


        $ordre = oci_parse($connexion, $txtReq);

        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Mot clés et commentaire:</h2><br>");

        print("<table class='kw_comment' width=100%  border='1'><tr><th>Label du mot clé</th><th> Id du commentaire </th><th> Type de commentaire </th><th> Commentaire </th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {

            print("<tr><td>". $row[0] ."</td><td>". $row[1]  ."</td><td>" . $row[2]   ."</td><td>".   $row[3]    ."</td></tr>");
             
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }

     function info_termGo($accession,$connexion){


        $txtReq = "select dbref.db_ref" 
            ." from dbref"
            ." where dbref.accession= :acces" 
            ." and dbref.db_type = 'GO'";


        $ordre = oci_parse($connexion, $txtReq);

        oci_bind_by_name($ordre, ":acces", $accession);

        oci_execute($ordre);

        print("<h2> Information relatives aux termes GO  :</h2><br>");

        print("<table class='kw_comment' width=50%  border='1'><tr><th>reférence GO</th><th> Lien  </th><tr>");

       
        while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {

            $lienEbi = "<a href=https://www.ebi.ac.uk/QuickGO/term/".$row[0] ."> https://www.ebi.ac.uk/QuickGO/term/".$row[0] ."</a>"; 
            print("<tr><td>". $row[0] ."</td><td>". $lienEbi ."</td></tr>");
             
        }
        print("</table><br>");
        oci_free_statement($ordre);
     }
     


 
    ?>
    <a href=index.html> RETOUR </a>
</body>
</html>
