<?php
require_once __DIR__ . '/../config/config.php';

function lire_produits() {
    if (!file_exists(FICHIER_PRODUITS)) {
        return array();
    }
    $data = json_decode(file_get_contents(FICHIER_PRODUITS), true);
    return is_array($data) ? $data : array();
}

function ecrire_produits($produits) {
    $json = json_encode($produits, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents(FICHIER_PRODUITS, $json, LOCK_EX);
}

function trouver_produit($code_barre) {
    $produits = lire_produits();
    foreach ($produits as $p) {
        if ($p['code_barre'] === $code_barre) {
            return $p;
        }
    }
    return null;
}

function enregistrer_produit($code_barre, $nom, $prix, $expiration, $stock) {
    $produits = lire_produits();
    foreach ($produits as $p) {
        if ($p['code_barre'] === $code_barre) {
            return false; // déjà existant
        }
    }
    $produits[] = array(
        'code_barre'         => $code_barre,
        'nom'                => $nom,
        'prix_unitaire_ht'   => (float) $prix,
        'date_expiration'    => $expiration,
        'quantite_stock'     => (int) $stock,
        'date_enregistrement'=> date('Y-m-d'),
    );
    ecrire_produits($produits);
    return true;
}

function decrementer_stock($code_barre, $quantite) {
    $produits = lire_produits();
    foreach ($produits as $i => $p) {
        if ($p['code_barre'] === $code_barre) {
            $produits[$i]['quantite_stock'] = max(0, $p['quantite_stock'] - (int) $quantite);
            ecrire_produits($produits);
            return true;
        }
    }
    return false;
}

function valider_date($date) {
    // Format attendu: MM-JJ-AAAA
    if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
        return false;
    }
    $parts = explode('-', $date);
    return checkdate((int) $parts[0], (int) $parts[1], (int) $parts[2]);
}

function convertir_date_iso($date) {
    // MM-JJ-AAAA -> AAAA-MM-JJ pour stockage
    $parts = explode('-', $date);
    return $parts[2] . '-' . $parts[0] . '-' . $parts[1];
}
