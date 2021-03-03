PROMPT REQUEST 1: Noms recommandés protéines commentaires 'cardiac';
SELECT prot_name_2_prot.accession, protein_names.prot_name
FROM protein_names, prot_name_2_prot, comments
WHERE
  -- REGEXP_LIKE (comments.txt_c, 'cardiac', 'i')
  comments.txt_c LIKE '%cardiac%'
  AND comments.accession = prot_name_2_prot.accession
  AND prot_name_2_prot.prot_name_id = protein_names.prot_name_id
  AND protein_names.name_kind = 'recommendedName';

PROMPT REQUEST 2: Noms recommandés protéines mots-clé 'Long QT syndrome';
SELECT prot_name_2_prot.accession, protein_names.prot_name 
FROM protein_names, prot_name_2_prot, keywords, entries_2_keywords
WHERE 
  -- REGEXP_LIKE (keywords.kw_label, 'Long QT syndrome', 'i')
  keywords.kw_label like '%Long QT syndrome%' 
  AND keywords.kw_id = entries_2_keywords.kw_id 
  AND entries_2_keywords.accession = prot_name_2_prot.accession
  AND prot_name_2_prot.prot_name_id = protein_names.prot_name_id
  AND protein_names.name_kind = 'recommendedName';

PROMPT REQUEST 3: Entrées dont séquence la plus grande;
SELECT entries.*
FROM entries, proteins 
WHERE 
  proteins.seqLength 
  = (SELECT MAX(seqLength) FROM proteins)
  AND proteins.accession = entries.accession;

PROMPT REQUEST 4: Entrées plus de 2 gènes;
SELECT accession, COUNT(gene_name_id)
FROM entry_2_gene_name
GROUP BY accession
HAVING COUNT(gene_name_id) > 2;

PROMPT REQUEST 5: Entrées dont nom protéine 'channel';
SELECT 
  prot_name_2_prot.accession,
  protein_names.prot_name,
  protein_names.name_kind
FROM protein_names, prot_name_2_prot
WHERE
  -- REGEXP_LIKE (prot_name, 'channel', 'i')
  prot_name LIKE '%channel%'
  AND protein_names.prot_name_id = prot_name_2_prot.prot_name_id;

PROMPT REQUEST 6: Entrées dont nom protéine 'Long QT syndrome' et 'Short QT syndrome';
SELECT DISTINCT
  prot_name_2_prot.accession,
  protein_names.prot_name
FROM 
  keywords, entries_2_keywords,
  protein_names, prot_name_2_prot
WHERE
  -- (REGEXP_LIKE (keywords.kw_label, 'Long QT syndrome', 'i')
  -- OR REGEXP_LIKE (keywords.kw_label, 'Short QT syndrome', 'i'))
  (keywords.kw_label LIKE '%Long QT syndrome%'
  OR keywords.kw_label LIKE '%Short QT syndrome%')
  AND keywords.kw_id = entries_2_keywords.kw_id
  AND entries_2_keywords.accession = prot_name_2_prot.accession
  AND prot_name_2_prot.prot_name_id = protein_names.prot_name_id
  AND protein_names.name_kind = 'recommendedName';
-- Autre possibilité on enlève le DISTINCT et puis
-- GROUP BY (prot_name_2_prot.accession, protein_names.prot_name)

PROMPT REQUEST 7: Termes GO 'Long QT syndrome' communs 2 entrées;
SELECT dbref.db_ref
FROM dbref, keywords, entries_2_keywords
WHERE
  -- REGEXP_LIKE (keywords.kw_label, 'Long QT syndrome', 'i')
  keywords.kw_label LIKE '%Long QT syndrome%'
  AND keywords.kw_id = entries_2_keywords.kw_id
  AND entries_2_keywords.accession = dbref.accession
  AND dbref.db_type = 'GO'
GROUP BY dbref.db_ref
HAVING COUNT(dbref.accession) >= 2;

-- Requete soutenance

SELECT Prot_name_2_prot.accession, Protein_names.prot_name
FROM Protein_names, Prot_name_2_prot
WHERE 
    Protein_names.name_kind = 'recommendedName'
    AND Protein_names.prot_name_id = Prot_name_2_prot.prot_name_id
    AND (Prot_name_2_prot.accession
IN 
((SELECT Entries_2_keywords.accession
FROM Keywords, Entries_2_keywords
WHERE
    Keywords.kw_label LIKE '%Nucleotide-binding%'
    AND Keywords.kw_id = Entries_2_keywords.kw_id)
INTERSECT
(SELECT Entries_2_keywords.accession
FROM Keywords, Entries_2_keywords
WHERE
    Keywords.kw_label LIKE '%Reference proteome%'
    AND Keywords.kw_id = Entries_2_keywords.kw_id)))

(SELECT Prot_name_2_prot.accession, Protein_names.prot_name
FROM 
    Keywords, Entries_2_keywords, 
    Protein_names, Prot_name_2_prot
WHERE
    Keywords.kw_label LIKE '%Nucleotide-binding%'
    AND Keywords.kw_id = Entries_2_keywords.kw_id
    AND Entries_2_keywords.accession = Prot_name_2_prot.accession
    AND Protein_names.prot_name_id = Prot_name_2_prot.prot_name_id
    AND Protein_names.name_kind = 'recommendedName')
INTERSECT
(SELECT Prot_name_2_prot.accession, Protein_names.prot_name
FROM 
    Keywords, Entries_2_keywords,
    Protein_names, Prot_name_2_prot
WHERE
    Keywords.kw_label LIKE '%Reference proteome%'
    AND Keywords.kw_id = Entries_2_keywords.kw_id
    AND Entries_2_keywords.accession = Prot_name_2_prot.accession
    AND Protein_names.prot_name_id = Prot_name_2_prot.prot_name_id
    AND Protein_names.name_kind = 'recommendedName')




























