<?php
require_once __DIR__ . '/auth/session.php';
exiger_connexion();

$u = utilisateur_connecte();
$message = '';
if (isset($_GET['erreur']) && $_GET['erreur'] === 'acces_refuse') {
    $message = 'Vous n\'avez pas les droits pour accéder à cette page.';
}

$titre_page = 'Accueil';
require __DIR__ . '/includes/header.php';
?>
<section class="accueil">
    <h1>Bonjour, <?= htmlspecialchars($u['nom_complet']) ?></h1>
    <p>Vous êtes connecté en tant que <strong><?= libelle_role($u['role']) ?></strong>.</p>

    <?php if ($message !== ''): ?>
        <p class="message-erreur"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <ul class="liste-actions">
        <?php if (in_array($u['role'], array('caissier', 'manager', 'super_admin'), true)): ?>
            <li><a href="<?= $base ?>/modules/facturation/nouvelle-facture.php">Démarrer une nouvelle facture</a></li>
        <?php endif; ?>
        <?php if (in_array($u['role'], array('manager', 'super_admin'), true)): ?>
            <li><a href="<?= $base ?>/modules/produits/enregistrer.php">Enregistrer un nouveau produit</a></li>
            <li><a href="<?= $base ?>/modules/produits/liste.php">Consulter le catalogue</a></li>
            <li><a href="<?= $base ?>/rapports/rapport-journalier.php">Rapport du jour</a></li>
            <li><a href="<?= $base ?>/rapports/rapport-mensuel.php">Rapport du mois</a></li>
        <?php endif; ?>
        <?php if ($u['role'] === 'super_admin'): ?>
            <li><a href="<?= $base ?>/modules/admin/gestion-comptes.php">Gérer les comptes utilisateurs</a></li>
        <?php endif; ?>
    </ul>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
