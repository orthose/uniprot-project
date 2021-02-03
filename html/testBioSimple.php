<html>

    <head> 
        <link rel="stylesheet" href="style.css" type="text/css" />
        Un exemple pour le projet PHP 
    </head>

    <body>

        <?php

            // Récupérer dans des variables locales les paramètres du formulaire
            $ac = $_REQUEST['accession'];

            $connexion = oci_connect('c##pandrie_a', 'pandrie_a', 'dbinfo');
            $txtReq = " select dateCreat, dataset "
            . "from entries e "
            . "where e.accession = :acces ";
            // Pour débugger on affiche le texte de la requête:
            // echo "<i>(debug : ".$txtReq.")</i><br>";

            $ordre = oci_parse($connexion, $txtReq);

            oci_bind_by_name($ordre, ":acces", $ac);

            // Exécution de la requête
            oci_execute($ordre);

            while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
                echo '<br> ' . $row[0] . ' ' . $row[1] ; 
            }

            oci_free_statement($ordre);
            oci_close($connexion);

        ?>

    </body>
</html>
