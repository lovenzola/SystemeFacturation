<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';

exiger_role(array('super_admin'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url_base() . '/modules/admin/gestion-comptes.php');
    exit;
}

$id = trim($_POST['identifiant'] ?? '');
$courant = utilisateur_connecte();

if ($id === '' || $id === $courant['identifiant']) {
    header('Location: ' . url_base() . '/modules/admin/gestion-comptes.php');
    exit;
}

$cible = trouver_utilisateur($id);
if ($cible !== null && $cible['role'] !== 'super_admin') {
    supprimer_utilisateur($id);
}

header('Location: ' . url_base() . '/modules/admin/gestion-comptes.php?msg=suppr_ok');
exit;
