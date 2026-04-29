<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

exiger_role(array('manager', 'super_admin'));
$produits = lire_produits();

$titre_page = 'Catalogue des produits';
require __DIR__ . '/../../includes/header.php';
?>
<section class="bloc">
    <h1>Catalogue des produits</h1>
    <p><a href="<?= $base ?>/modules/produits/enregistrer.php">+ Enregistrer un nouveau produit</a></p>

    <?php if (count($produits) === 0): ?>
        <p>Aucun produit enregistré pour le moment.</p>
    <?php else: ?>
        <table class="tableau">
            <thead>
                <tr>
                    <th>Code-barres</th>
                    <th>Nom</th>
                    <th>Prix HT</th>
                    <th>Stock</th>
                    <th>Expiration</th>
                    <th>Enregistré le</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($produits as $p): ?>
                <tr<?= ($p['quantite_stock'] <= 5) ? ' class="stock-faible"' : '' ?>>
                    <td><?= htmlspecialchars($p['code_barre']) ?></td>
                    <td><?= htmlspecialchars($p['nom']) ?></td>
                    <td><?= number_format($p['prix_unitaire_ht'], 0, ',', ' ') ?> CDF</td>
                    <td><?= (int) $p['quantite_stock'] ?></td>
                    <td><?= htmlspecialchars($p['date_expiration']) ?></td>
                    <td><?= htmlspecialchars($p['date_enregistrement']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
