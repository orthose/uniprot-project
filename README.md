# Introduction
Projet de construction et d'utilisation d'une base de données 
biologiques issue de Uniprot (base de données Web).

# L'équipe des étudiants

* Maxime Vincent
* Jeyanthan Markandu

Université Paris-Saclay

Année Universitaire 2021

# Responsable du cours

Sarah Cohen-Boulakia

Option Bio-Informatique

# Utilisation de Oracle

* Se connecter à la base Oracle
`$ cd sql; rlwrap sqlplus c##logincourt_a/passwd_a@dbinfo`

* Pour créer le schéma des tables
`SQL> @createTables`

* Entre 2 exécutions du programme Python
`SQL> @emptyTables`

* Pour détruire les tables
`SQL> @dropTables`

* Pour vérifier le contenu d'une table
`SQL> select * from nom_table;`

* Pour connaître le nom des tables
`SQL> select table_name from user_tables;`

* Pour voir si les tables sont remplies
`SQL> @test`

# Remplissage des tables

* Paramétrer le fichier config.txt
`cd resources; mv config_template.txt config.txt`

* Exécuter le main du programme
`python mainUniprot.py`

# Utilisation de l'interface web

* Copie dans le dossier du serveur
`cp -r html ~/public_html`
`mv ~/public_html/html ~/public_html/uniprot`

* Configuration de Oracle : Éditez le fichier config.php

* Connexion à la page web
`https://tp-ssh1.dep-informatique.u-psud.fr/~logincourt/uniprot`

# Organisation du code

* Le dossier uniprotLoadDB/ contient les classes Python correspondant
aux tables du schéma de base de données. Elles sont utilisées pour
le remplissage des tables à partir des fichiers XML.

* Le dossier sql/ contient les scripts SQL de création, suppression
et vidage des tables. Ainsi que ceux de requête et de test.

* Le dossier html contient le code source HTML/PHP du site web.
Le point d'entrée du site web est la page index.html.

# Organisation du travail

* Travail complémentaire des étudiants. 
Répartition homogène du travail.

* Aucune difficulté majeure rencontrée.
Le code tourne bien.

* Bogue anecdotique : Le binding par :comment
dans le code PHP n'est pas accepté par Oracle.
J'ai dû renommer en :com.
