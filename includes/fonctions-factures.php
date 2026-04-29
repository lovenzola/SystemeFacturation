<?php
require_once __DIR__ . '/../config/config.php';

function lire_factures() {
    if (!file_exists(FICHIER_FACTURES)) {
        return array();
    }
    $data = json_decode(file_get_contents(FICHIER_FACTURES), true);
    return is_array($data) ? $data : array();
}

function ecrire_factures($factures) {
    $json = json_encode($factures, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents(FICHIER_FACTURES, $json, LOCK_EX);
}

function generer_id_facture() {
    $factures = lire_factures();
    $aujourd_hui = date('Ymd');
    $compteur = 1;
    foreach ($factures as $f) {
        if (strpos($f['id_facture'], 'FAC-' . $aujourd_hui) === 0) {
            $compteur++;
        }
    }
    return 'FAC-' . $aujourd_hui . '-' . str_pad($compteur, 3, '0', STR_PAD_LEFT);
}

function calculer_totaux($articles) {
    $total_ht = 0;
    foreach ($articles as $a) {
        $total_ht += $a['sous_total_ht'];
    }
    $tva = round($total_ht * TAUX_TVA);
    $total_ttc = $total_ht + $tva;
    return array(
        'total_ht'  => $total_ht,
        'tva'       => $tva,
        'total_ttc' => $total_ttc,
    );
}

function enregistrer_facture($caissier, $articles) {
    $totaux = calculer_totaux($articles);
    $facture = array(
        'id_facture' => generer_id_facture(),
        'date'       => date('Y-m-d'),
        'heure'      => date('H:i:s'),
        'caissier'   => $caissier,
        'articles'   => $articles,
        'total_ht'   => $totaux['total_ht'],
        'tva'        => $totaux['tva'],
        'total_ttc'  => $totaux['total_ttc'],
    );
    $factures = lire_factures();
    $factures[] = $facture;
    ecrire_factures($factures);

    // Mise à jour du stock
    foreach ($articles as $a) {
        decrementer_stock($a['code_barre'], $a['quantite']);
    }

    return $facture;
}

function trouver_facture($id) {
    foreach (lire_factures() as $f) {
        if ($f['id_facture'] === $id) {
            return $f;
        }
    }
    return null;
}

function formater_montant($montant) {
    return number_format($montant, 0, ',', ' ') . ' ' . DEVISE;
}
