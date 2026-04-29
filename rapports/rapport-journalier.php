<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/fonctions-factures.php';

exiger_role(array('manager', 'super_admin'));

$jour = $_GET['jour'] ?? date('Y-m-d');
$factures = lire_factures();

$lignes = array();
$tot_ht = 0; $tot_tva = 0; $tot_ttc = 0;
foreach ($factures as $f) {
    if ($f['date'] === $jour) {
        $lignes[] = $f;
        $tot_ht  += $f['total_ht'];
        $tot_tva += $f['tva'];
        $tot_ttc += $f['total_ttc'];
    }
}

$titre_page = 'Rapport journalier';
require __DIR__ . '/../includes/header.php';
?>
<section class="bloc">
    <h1>Rapport journalier</h1>

    <form method="get" class="ligne-saisie">
        <label>Jour
            <input type="date" name="jour" value="<?= htmlspecialchars($jour) ?>">
        </label>
        <button type="submit">Afficher</button>
        <a href="<?= $base ?>/rapports/rapport-mensuel.php" class="lien-annuler">Voir le rapport mensuel</a>
    </form>

    <p>Nombre de factures : <strong><?= count($lignes) ?></strong></p>

    <?php if (count($lignes) === 0): ?>
        <p>Aucune facture pour cette journée.</p>
    <?php else: ?>
        <table class="tableau">
            <thead>
                <tr>
                    <th>N° facture</th>
                    <th>Heure</th>
                    <th>Caissier</th>
                    <th>Articles</th>
                    <th>Total HT</th>
                    <th>TVA</th>
                    <th>Net</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($lignes as $f): ?>
                <tr>
                    <td>
                        <a href="<?= $base ?>/modules/facturation/afficher-facture.php?id=<?= urlencode($f['id_facture']) ?>">
                            <?= htmlspecialchars($f['id_facture']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($f['heure']) ?></td>
                    <td><?= htmlspecialchars($f['caissier']) ?></td>
                    <td><?= count($f['articles']) ?></td>
                    <td><?= formater_montant($f['total_ht']) ?></td>
                    <td><?= formater_montant($f['tva']) ?></td>
                    <td><?= formater_montant($f['total_ttc']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">Totaux</th>
                    <td><?= formater_montant($tot_ht) ?></td>
                    <td><?= formater_montant($tot_tva) ?></td>
                    <td><strong><?= formater_montant($tot_ttc) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
