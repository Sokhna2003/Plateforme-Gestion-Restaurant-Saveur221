-- =====================================================================
-- Migration : categories — ajout image + date_ajout
-- Nombre de produits = calculé dynamiquement (COUNT produits), pas stocké.
-- Avant : id, nom, description
-- Après : id, nom, description, image, date_ajout
-- =====================================================================

USE restaurant_saveur221;

ALTER TABLE categories
    ADD COLUMN image VARCHAR(255) NULL AFTER description,
    ADD COLUMN date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER image;

-- Données existantes : on note la date d'ajout cochée à maintenant
UPDATE categories SET date_ajout = CURRENT_TIMESTAMP WHERE date_ajout IS NULL;