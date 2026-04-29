<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';

exiger_role(array('super_admin'));

$utilisateurs = lire_utilisateurs();
$message = $_GET['msg'] ?? '';

$titre_page = 'Gestion des comptes';
require __DIR__ . '/../../includes/header.php';
?>
<section class="bloc">
    <h1>Comptes utilisateurs</h1>
    <p><a href="<?= $base ?>/modules/admin/ajouter-compte.php">+ Ajouter un compte</a></p>

    <?php if ($message === 'ajout_ok'): ?>
        <p class="message-ok">Compte ajouté.</p>
    <?php elseif ($message === 'suppr_ok'): ?>
        <p class="message-ok">Compte supprimé.</p>
    <?php endif; ?>

    <table class="tableau">
        <thead>
            <tr>
                <th>Identifiant</th>
                <th>Nom complet</th>
                <th>Rôle</th>
                <th>Créé le</th>
                <th>Actif</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($utilisateurs as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['identifiant']) ?></td>
                <td><?= htmlspecialchars($u['nom_complet']) ?></td>
                <td><?= libelle_role($u['role']) ?></td>
                <td><?= htmlspecialchars($u['date_creation']) ?></td>
                <td><?= !empty($u['actif']) ? 'oui' : 'non' ?></td>
                <td>
                    <?php if ($u['role'] !== 'super_admin'): ?>
                        <form method="post"
                              action="<?= $base ?>/modules/admin/supprimer-compte.php"
                              onsubmit="return confirm('Supprimer ce compte ?');">
                            <input type="hidden" name="identifiant" value="<?= htmlspecialchars($u['identifiant']) ?>">
                            <button type="submit" class="lien-suppr">supprimer</button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
