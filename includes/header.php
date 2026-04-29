<?php
require_once __DIR__ . '/../auth/session.php';
$u = utilisateur_connecte();
$base = url_base();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($titre_page) ? htmlspecialchars($titre_page) . ' — ' : '' ?><?= htmlspecialchars(NOM_MAGASIN) ?></title>
<link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>
<header class="bandeau">
    <div class="enseigne">
        <a href="<?= $base ?>/index.php"><?= htmlspecialchars(NOM_MAGASIN) ?></a>
        <span class="sous-titre">caisse</span>
    </div>
<?php if ($u): ?>
    <nav class="menu">
        <?php if (in_array($u['role'], array('caissier', 'manager', 'super_admin'), true)): ?>
            <a href="<?= $base ?>/modules/facturation/nouvelle-facture.php">Nouvelle facture</a>
        <?php endif; ?>
        <?php if (in_array($u['role'], array('manager', 'super_admin'), true)): ?>
            <a href="<?= $base ?>/modules/produits/enregistrer.php">Enregistrer produit</a>
            <a href="<?= $base ?>/modules/produits/liste.php">Produits</a>
            <a href="<?= $base ?>/rapports/rapport-journalier.php">Rapports</a>
        <?php endif; ?>
        <?php if ($u['role'] === 'super_admin'): ?>
            <a href="<?= $base ?>/modules/admin/gestion-comptes.php">Comptes</a>
        <?php endif; ?>
    </nav>
    <div class="utilisateur">
        <span><?= htmlspecialchars($u['nom_complet']) ?> <em>(<?= libelle_role($u['role']) ?>)</em></span>
        <a href="<?= $base ?>/auth/logout.php" class="lien-deco">Déconnexion</a>
    </div>
<?php endif; ?>
</header>
<main class="contenu">
