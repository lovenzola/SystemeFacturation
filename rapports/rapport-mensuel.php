<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/fonctions-factures.php';

exiger_role(array('manager', 'super_admin'));

$mois = $_GET['mois'] ?? date('Y-m');
$factures = lire_factures();

$par_jour = array();
$tot_ht = 0; $tot_tva = 0; $tot_ttc = 0; $nb = 0;
foreach ($factures as $f) {
    if (strpos($f['date'], $mois) === 0) {
        $j = $f['date'];
        if (!isset($par_jour[$j])) {
            $par_jour[$j] = array('nb'=>0,'ht'=>0,'tva'=>0,'ttc'=>0);
        }
        $par_jour[$j]['nb']++;
        $par_jour[$j]['ht']  += $f['total_ht'];
        $par_jour[$j]['tva'] += $f['tva'];
        $par_jour[$j]['ttc'] += $f['total_ttc'];
        $tot_ht  += $f['total_ht'];
        $tot_tva += $f['tva'];
        $tot_ttc += $f['total_ttc'];
        $nb++;
    }
}
ksort($par_jour);

$titre_page = 'Rapport mensuel';
require __DIR__ . '/../includes/header.php';
?>
<section class="bloc">
    <h1>Rapport mensuel</h1>

    <form method="get" class="ligne-saisie">
        <label>Mois
            <input type="month" name="mois" value="<?= htmlspecialchars($mois) ?>">
        </label>
        <button type="submit">Afficher</button>
        <a href="<?= $base ?>/rapports/rapport-journalier.php" class="lien-annuler">Voir le rapport journalier</a>
    </form>

    <p>Nombre total de factures sur le mois : <strong><?= $nb ?></strong></p>

    <?php if (count($par_jour) === 0): ?>
        <p>Aucune facture pour ce mois.</p>
    <?php else: ?>
        <table class="tableau">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Nombre</th>
                    <th>Total HT</th>
                    <th>TVA</th>
                    <th>Net</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($par_jour as $jour => $t): ?>
                <tr>
                    <td><?= htmlspecialchars($jour) ?></td>
                    <td><?= $t['nb'] ?></td>
                    <td><?= formater_montant($t['ht']) ?></td>
                    <td><?= formater_montant($t['tva']) ?></td>
                    <td><?= formater_montant($t['ttc']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Totaux</th>
                    <td><?= formater_montant($tot_ht) ?></td>
                    <td><?= formater_montant($tot_tva) ?></td>
                    <td><strong><?= formater_montant($tot_ttc) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
