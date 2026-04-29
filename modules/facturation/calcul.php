<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';

exiger_connexion();
header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? '';

if (!isset($_SESSION['facture_courante']) || !is_array($_SESSION['facture_courante'])) {
    $_SESSION['facture_courante'] = array();
}

if ($action === 'ajouter') {
    $code = trim($_POST['code_barre'] ?? '');
    $qte  = (int) ($_POST['quantite'] ?? 0);

    if ($code === '' || $qte <= 0) {
        echo json_encode(array('ok' => false, 'erreur' => 'Code ou quantité invalide.'));
        exit;
    }
    $produit = trouver_produit($code);
    if ($produit === null) {
        echo json_encode(array('ok' => false, 'erreur' => 'Produit inconnu. Demandez au manager de l\'enregistrer.'));
        exit;
    }

    // Total déjà sur la facture pour ce code
    $deja = 0;
    foreach ($_SESSION['facture_courante'] as $a) {
        if ($a['code_barre'] === $code) {
            $deja += $a['quantite'];
        }
    }
    if ($deja + $qte > (int) $produit['quantite_stock']) {
        echo json_encode(array(
            'ok' => false,
            'erreur' => 'Quantité demandée supérieure au stock disponible (reste : ' .
                        ((int) $produit['quantite_stock'] - $deja) . ').'
        ));
        exit;
    }

    $_SESSION['facture_courante'][] = array(
        'code_barre'        => $produit['code_barre'],
        'nom'               => $produit['nom'],
        'prix_unitaire_ht'  => (float) $produit['prix_unitaire_ht'],
        'quantite'          => $qte,
        'sous_total_ht'     => (float) $produit['prix_unitaire_ht'] * $qte,
    );

    $totaux = calculer_totaux($_SESSION['facture_courante']);
    echo json_encode(array(
        'ok'       => true,
        'articles' => $_SESSION['facture_courante'],
        'totaux'   => $totaux,
    ));
    exit;
}

if ($action === 'retirer') {
    $i = (int) ($_POST['index'] ?? -1);
    if ($i >= 0 && isset($_SESSION['facture_courante'][$i])) {
        array_splice($_SESSION['facture_courante'], $i, 1);
    }
    $totaux = calculer_totaux($_SESSION['facture_courante']);
    echo json_encode(array(
        'ok'       => true,
        'articles' => $_SESSION['facture_courante'],
        'totaux'   => $totaux,
    ));
    exit;
}

if ($action === 'vider') {
    $_SESSION['facture_courante'] = array();
    echo json_encode(array('ok' => true, 'articles' => array(),
        'totaux' => array('total_ht'=>0,'tva'=>0,'total_ttc'=>0)));
    exit;
}

if ($action === 'valider') {
    if (empty($_SESSION['facture_courante'])) {
        echo json_encode(array('ok' => false, 'erreur' => 'Aucun article sur la facture.'));
        exit;
    }
    $u = utilisateur_connecte();
    $facture = enregistrer_facture($u['identifiant'], $_SESSION['facture_courante']);
    $_SESSION['facture_courante'] = array();
    echo json_encode(array('ok' => true, 'id_facture' => $facture['id_facture']));
    exit;
}

echo json_encode(array('ok' => false, 'erreur' => 'Action inconnue.'));
