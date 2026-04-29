<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';
exiger_connexion();

$id = $_GET['id'] ?? '';
$facture = $id !== '' ? trouver_facture($id) : null;

$titre_page = $facture ? ('Facture ' . $facture['id_facture']) : 'Facture introuvable';
require __DIR__ . '/../../includes/header.php';
?>

<?php if ($facture === null): ?>
    <section class="bloc">
        <h1>Facture introuvable</h1>
        <p>Aucune facture ne correspond à l'identifiant fourni.</p>
        <p><a href="<?= $base ?>/modules/facturation/nouvelle-facture.php">Retour à la caisse</a></p>
    </section>
<?php else: ?>
    <section class="bloc facture-impression">
        <header class="entete-facture">
            <h1><?= htmlspecialchars(NOM_MAGASIN) ?></h1>
            <p>Facture <strong><?= htmlspecialchars($facture['id_facture']) ?></strong></p>
            <p><?= htmlspecialchars($facture['date']) ?> à <?= htmlspecialchars($facture['heure']) ?> — caissier : <?= htmlspecialchars($facture['caissier']) ?></p>
        </header>

        <table class="tableau">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Prix unit. HT</th>
                    <th>Qté</th>
                    <th>Sous-total HT</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($facture['articles'] as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['nom']) ?></td>
                    <td><?= formater_montant($a['prix_unitaire_ht']) ?></td>
                    <td><?= (int) $a['quantite'] ?></td>
                    <td><?= formater_montant($a['sous_total_ht']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><th colspan="3">Total HT</th>      <td><?= formater_montant($facture['total_ht'])  ?></td></tr>
                <tr><th colspan="3">TVA (18%)</th>     <td><?= formater_montant($facture['tva'])       ?></td></tr>
                <tr><th colspan="3">Net à payer</th>   <td><strong><?= formater_montant($facture['total_ttc']) ?></strong></td></tr>
            </tfoot>
        </table>

        <div class="actions-facture no-print">
            <button type="button" onclick="window.print()">Imprimer</button>
            <a class="bouton-lien" href="<?= $base ?>/modules/facturation/nouvelle-facture.php">Nouvelle facture</a>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
