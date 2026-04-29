<?php
// Paramètres globaux de l'application

define('CHEMIN_RACINE', dirname(__DIR__));
define('CHEMIN_DATA', CHEMIN_RACINE . '/data');

define('FICHIER_PRODUITS', CHEMIN_DATA . '/produits.json');
define('FICHIER_FACTURES', CHEMIN_DATA . '/factures.json');
define('FICHIER_UTILISATEURS', CHEMIN_DATA . '/utilisateurs.json');

define('TAUX_TVA', 0.18);
define('DEVISE', 'CDF');

define('NOM_MAGASIN', 'Super Marché Bonheur');
