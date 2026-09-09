-- =====================================================================
-- Migration : corbeille (soft delete) — categories + produits
-- Ajoute une colonne supprime_le : NULL = actif, date = déplacé en corbeille
-- =====================================================================

USE restaurant_saveur221;

ALTER TABLE categories
    ADD COLUMN supprime_le DATETIME NULL AFTER date_ajout;

ALTER TABLE produits
    ADD COLUMN supprime_le DATETIME NULL AFTER image;