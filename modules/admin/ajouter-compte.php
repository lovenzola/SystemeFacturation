<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';

exiger_role(array('super_admin'));

$erreurs = array();
$saisie = array(
    'identifiant' => $_POST['identifiant'] ?? '',
    'nom_complet' => $_POST['nom_complet'] ?? '',
    'role'        => $_POST['role']        ?? 'caissier',
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = trim($saisie['identifiant']);
    $nom  = trim($saisie['nom_complet']);
    $role = $saisie['role'];
    $mdp  = $_POST['mot_de_passe']         ?? '';
    $mdp2 = $_POST['mot_de_passe_confirm'] ?? '';

    if ($id === '')                                            $erreurs[] = 'Identifiant obligatoire.';
    if ($nom === '')                                           $erreurs[] = 'Nom complet obligatoire.';
    if (!in_array($role, array('caissier', 'manager'), true))  $erreurs[] = 'Rôle invalide (caissier ou manager uniquement).';
    if (strlen($mdp) < 6)                                      $erreurs[] = 'Le mot de passe doit faire au moins 6 caractères.';
    if ($mdp !== $mdp2)                                        $erreurs[] = 'Les deux mots de passe ne correspondent pas.';
    if (trouver_utilisateur($id) !== null)                     $erreurs[] = 'Cet identifiant est déjà utilisé.';

    if (empty($erreurs)) {
        ajouter_utilisateur($id, $mdp, $role, $nom);
        header('Location: ' . url_base() . '/modules/admin/gestion-comptes.php?msg=ajout_ok');
        exit;
    }
}

$titre_page = 'Ajouter un compte';
require __DIR__ . '/../../includes/header.php';
?>
<section class="bloc">
    <h1>Ajouter un compte</h1>

    <?php if (!empty($erreurs)): ?>
        <div class="message-erreur">
            <ul><?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" action="" class="formulaire">
        <p>
            <label>Identifiant</label>
            <input type="text" name="identifiant" value="<?= htmlspecialchars($saisie['identifiant']) ?>" required>
        </p>
        <p>
            <label>Nom complet</label>
            <input type="text" name="nom_complet" value="<?= htmlspecialchars($saisie['nom_complet']) ?>" required>
        </p>
        <p>
            <label>Rôle</label>
            <select name="role">
                <option value="caissier" <?= $saisie['role']==='caissier'?'selected':'' ?>>Caissier</option>
                <option value="manager"  <?= $saisie['role']==='manager' ?'selected':'' ?>>Manager</option>
            </select>
        </p>
        <p>
            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" required>
        </p>
        <p>
            <label>Confirmer le mot de passe</label>
            <input type="password" name="mot_de_passe_confirm" required>
        </p>
        <p>
            <button type="submit">Créer le compte</button>
            <a href="<?= $base ?>/modules/admin/gestion-comptes.php" class="lien-annuler">Annuler</a>
        </p>
    </form>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
