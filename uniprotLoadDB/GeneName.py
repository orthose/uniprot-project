# coding: utf8
'''
Classe GeneName : Noms de genes Uniprot
   Attributs :
      - name : nom du gène
      - kwLabel : keyword label
@author: Sarah Cohen Boulakia
'''
import cx_Oracle
class GeneName:
    
    # Parametre de classe utilise pour dire si les GeneName doivent etre inseres 
    # en base quand insertDB est appele
    DEBUG_INSERT_DB = True
    
    
    def __init__(self, name, typeN):
        self._name = name
        self._typeN= typeN
    
    '''
    Si le nom de gene (couple nom/type) n'existe pas déjà, ajout en base
    @param curDb: Curseur sur la base de donnees oracle 
    @return identifiant du gene en base de donnees
    '''
    def insertDB (self, curDB):
        gene_name_id=-1
        
        if GeneName.DEBUG_INSERT_DB:
            curDB.prepare ("SELECT gene_name_id " \
                                    + " FROM gene_names " \
                                    + " WHERE gene_name=:gene_name " \
                                            + " AND name_type=:name_type")
            curDB.execute (None, {'gene_name': self._name, 'name_type': self._typeN})
            raw = curDB.fetchone ()
            if raw != None:
                gene_name_id = raw[0]
            else:
                idG =curDB.var(cx_Oracle.NUMBER)
                curDB.prepare("INSERT INTO gene_names " \
                                + "(gene_name_id, gene_name, name_type) " \
                                + " values " \
                                + " (seq_gene_names.NEXTVAL, :gene_name, " \
                                + " :name_type) " \
                                + " RETURNING gene_name_id INTO :id")
                curDB.execute (None, {'gene_name': self._name, 'name_type': self._typeN, 'id': idG})
                gene_name_id = idG.getvalue()[0]
        return gene_name_id
