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
`SQL> @quickCheck`

# Remplissage des tables

* Paramétrer le fichier config.txt
`cd resources; mv config_template.txt config.txt`

* Exécuter le main du programme
`python mainUniprot.py`

# Utilisation de l'interface web

* Copie dans le dossier du serveur
`cp -r html ~/public_html`
`mv ~/public_html/html ~/public_html/uniprot`

* Connexion à la page web
`http://tp-ssh1.dep-informatique.u-psud.fr/~logincourt/uniprot`
