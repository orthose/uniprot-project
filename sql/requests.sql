PROMPT Noms recommandés protéines commentaires 'cardiac';
SELECT prot_name_2_prot.accession, protein_names.prot_name
FROM protein_names, prot_name_2_prot, comments
WHERE
  -- REGEXP_LIKE (comments.txt_c, 'cardiac', 'i')
  comments.txt_c LIKE '%cardiac%'
  AND comments.accession = prot_name_2_prot.accession
  AND prot_name_2_prot.prot_name_id = protein_names.prot_name_id
  AND protein_names.name_kind = 'recommendedName';

PROMPT Noms recommandés protéines mots-clé 'Long QT syndrome';
SELECT prot_name_2_prot.accession, protein_names.prot_name 
FROM protein_names, prot_name_2_prot, keywords, entries_2_keywords
WHERE 
  -- REGEXP_LIKE (keywords.kw_label, 'Long QT syndrome', 'i')
  keywords.kw_label like '%Long QT syndrome%' 
  AND keywords.kw_id = entries_2_keywords.kw_id 
  AND entries_2_keywords.accession = prot_name_2_prot.accession
  AND prot_name_2_prot.prot_name_id = protein_names.prot_name_id
  AND protein_names.name_kind = 'recommendedName';

PROMPT Entrées dont séquence la plus grande;
SELECT entries.*
FROM entries, proteins 
WHERE 
  proteins.seqLength 
  = (SELECT MAX(seqLength) FROM proteins)
  AND proteins.accession = entries.accession;

PROMPT Entrées plus de 2 gènes;
SELECT accession, COUNT(gene_name_id)
FROM entry_2_gene_name
GROUP BY accession
HAVING COUNT(gene_name_id) > 2;
