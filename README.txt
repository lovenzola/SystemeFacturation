Système de Facturation — Super Marché
=====================================

Travaux Pratiques de Programmation Web (PHP procédural).
Persistance des données par fichiers JSON, lecture de codes-barres
via la bibliothèque ZXing (côté navigateur).


1. Pré-requis
-------------
- PHP 8.0 ou supérieur (testé avec PHP 8.2)
- Un navigateur récent (Chrome, Firefox, Edge) avec accès caméra
- Aucun système de gestion de base de données n'est requis


2. Démarrage rapide en local
----------------------------
Depuis le dossier "facturation/", lancer le serveur PHP intégré :

    php -S localhost:8000

Puis ouvrir dans le navigateur :

    http://localhost:8000/index.php


3. Compte par défaut (Super Administrateur)
-------------------------------------------
Identifiant   : superadmin
Mot de passe  : Admin@2026

Ce compte permet de créer ensuite les comptes Manager et Caissier
depuis le menu "Comptes".


4. Arborescence
---------------
facturation/
  index.php                  Page d'accueil après connexion
  config/config.php          Paramètres (TVA, chemins, devise)
  auth/                      Connexion / déconnexion / sessions
  modules/produits/          Enregistrement et consultation des produits
  modules/facturation/       Caisse, calcul et affichage des factures
  modules/admin/             Gestion des comptes utilisateurs
  rapports/                  Rapports journalier et mensuel
  data/                      Fichiers JSON de persistance
  includes/                  Fonctions PHP réutilisables (header, fonctions...)
  assets/css/style.css       Feuille de style
  assets/js/scanner.js       Initialisation du scanner ZXing


5. Rôles et accès
-----------------
- Caissier            : facturation uniquement
- Manager             : facturation + produits + rapports
- Super Administrateur: tout, plus la gestion des comptes


6. Notes
--------
- L'accès à la caméra n'est autorisé par les navigateurs que sur
  http://localhost ou en HTTPS.
- En cas de saisie invalide d'un produit, les champs déjà remplis
  sont conservés.
- Le stock est décrémenté automatiquement lors de la validation
  d'une facture. Une alerte est affichée si la quantité demandée
  dépasse le stock disponible.
