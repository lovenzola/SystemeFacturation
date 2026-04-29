<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
exiger_connexion();

header('Content-Type: application/json; charset=utf-8');

$code = isset($_GET['code']) ? trim($_GET['code']) : '';
if ($code === '') {
    echo json_encode(array('trouve' => false, 'erreur' => 'Code-barres manquant.'));
    exit;
}

$produit = trouver_produit($code);
if ($produit === null) {
    echo json_encode(array('trouve' => false, 'code_barre' => $code));
} else {
    echo json_encode(array('trouve' => true, 'produit' => $produit));
}
