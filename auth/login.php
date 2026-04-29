<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../includes/fonctions-auth.php';

$erreur = '';
$identifiant_saisi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant_saisi = trim($_POST['identifiant'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';

    if ($identifiant_saisi === '' || $mdp === '') {
        $erreur = 'Veuillez remplir les deux champs.';
    } else {
        $u = authentifier($identifiant_saisi, $mdp);
        if ($u === false) {
            $erreur = 'Identifiant ou mot de passe incorrect.';
        } else {
            $_SESSION['utilisateur'] = array(
                'identifiant' => $u['identifiant'],
                'role'        => $u['role'],
                'nom_complet' => $u['nom_complet'],
            );
            header('Location: ' . url_base() . '/index.php');
            exit;
        }
    }
}

$titre_page = 'Connexion';
require __DIR__ . '/../includes/header.php';
?>
<section class="boite-connexion">
    <h1>Connexion</h1>
    <?php if ($erreur !== ''): ?>
        <p class="message-erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <p>
            <label for="identifiant">Identifiant</label>
            <input type="text" id="identifiant" name="identifiant"
                   value="<?= htmlspecialchars($identifiant_saisi) ?>" autofocus required>
        </p>
        <p>
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </p>
        <p>
            <button type="submit">Se connecter</button>
        </p>
    </form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
