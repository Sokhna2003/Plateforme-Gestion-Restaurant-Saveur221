ALTER TABLE produits ADD COLUMN date_ajout DATETIME NULL AFTER image;
UPDATE produits SET date_ajout = NOW() WHERE date_ajout IS NULL;