# coding: utf8
'''
Classe Keyword : mots cles de la base uniprotLoadDB
   Attributs :
      - kwId : keyword id
      - kwLabel : keyword label
@author: Sarah Cohen Boulakia
'''
import cx_Oracle
class Keyword:

    # Parametre de classe utilise pour dire si les Keyword doivent etre inseres 
    # en base quand insertDB est appele
    DEBUG_INSERT_DB = True
    
    def __init__(self, kwId, kwLabel):
        self._kwId = kwId
        self._kwLabel = kwLabel
    
    '''
    Si le mot cle n'existe pas, ajout en base.
    @param curDb: Curseur sur la base de donnees oracle 
    @return identifiant du mot-clé en base de données
    '''    
    def insertDB (self, curDB):
        keywordId = ""
        
        curDB.prepare ("SELECT kw_id " \
                                + " FROM keywords " \
                                + " WHERE kw_id=:kwId")
        curDB.execute (None, {'kwId': self._kwId })
        raw = curDB.fetchone ()
        # Nécesaire si on essaye d'insérer 2 fois
        # Le même keyword il ne faut pas insérer
        # un kw_id NULL dans entries_2_keywords
        if raw != None:
            keywordId = raw[0]
        else:
            # Insérer le keyword s'il n'existe pas
            # Cf. exemple de la classe Gene si besoin
            if Keyword.DEBUG_INSERT_DB:
                # Création variable Oracle
                idK = curDB.var(cx_Oracle.STRING)
                curDB.prepare("INSERT INTO keywords " \
                                + "(kw_id, kw_label) " \
                                + " values " \
                                + " (:kwId, :kwLabel)" \
                                + " RETURNING kw_id INTO :id") 
                curDB.execute (None, {'kwId': self._kwId, 
                                        'kwLabel': self._kwLabel,
                                        'id': idK})
                keywordId = idK.getvalue()[0]
                
        return keywordId
