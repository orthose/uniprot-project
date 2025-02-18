# coding: utf8
'''
Classe Protein : Proteines Uniprot
   Attributs :
      - names : noms de la proteine
      - seqTxt : Texte de la s�quence 
      - seqLength : Longueur de la s�quence
      - seqMass : Masse de la s�quence
@author: Sarah Cohen Boulakia
'''
from uniprotLoadDB.ProtName import ProtName

class Protein:
    
    # Parametre de classe utilise pour dire si les Protein doivent etre inserees 
    # en base quand insertDB est appele
    DEBUG_INSERT_DB = True
    
    def __init__ (self):
        self._names = []
        self._seqTxt = None
        self._seqLength = None
        self._seqMass = None
    
    ''' 
    Definir les attributs de la sequence 
    '''
    def setSequence (self, seqTxt, seqLength, seqMass):   
        self._seqTxt = seqTxt
        self._seqLength = seqLength
        self._seqMass = seqMass

    '''
    Ajouter un nouveau nom de proteine
    '''
    def addName (self, name):
        self._names.append (name)

    '''
    Inserer la proteine en base de donnees, ainsi que ses noms s'ils 
    n'existent pas deja et dans tous les cas le lien vers les noms. 
    @param curDB: Curseur sur la base oracle
    @param accession: num�ro d'accession de l'entr�e uniprotLoadDB associ�e 
    '''
    def insertDB (self, curDB, accession):
        
        if Protein.DEBUG_INSERT_DB:
            # Inserer la proteine et ses caracteristiques
            curDB.prepare("INSERT INTO proteins " \
                                + "(accession, seq, seqLength, seqMass) " \
                                + " values " \
                                + " (:accession, :seq, :seqLength, :seqMass)")
            curDB.execute (None, {'accession': accession,
                                    'seq': self._seqTxt, 
                                    'seqLength': self._seqLength,
                                    'seqMass': self._seqMass})
                
            if ProtName.DEBUG_INSERT_DB:
                for n in self._names:
                    protNameId = n.insertDB (curDB)
                    # inserer le lien entre la proteine et le nom 
                    # de proteine, l'identifiant de nom de proteine a ete 
                    # retourne par n.insertDB (curDB) et est dans protNameId
                    curDB.prepare("INSERT INTO prot_name_2_prot " \
                                + "(accession, prot_name_id) " \
                                + " values " \
                                + " (:accession, :prot_name_id)")
                    curDB.execute (None, {'accession': accession, 
                                            'prot_name_id': protNameId})

