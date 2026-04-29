<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

exiger_role(array('manager', 'super_admin'));

$erreurs = array();
$ok = '';
$saisie = array(
    'code_barre'      => $_POST['code_barre']      ?? ($_GET['code'] ?? ''),
    'nom'             => $_POST['nom']             ?? '',
    'prix'            => $_POST['prix']            ?? '',
    'date_expiration' => $_POST['date_expiration'] ?? '',
    'stock'           => $_POST['stock']           ?? '',
);

$produit_existant = null;
if ($saisie['code_barre'] !== '' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $produit_existant = trouver_produit($saisie['code_barre']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($saisie['code_barre']);
    $nom  = trim($saisie['nom']);
    $prix = trim($saisie['prix']);
    $exp  = trim($saisie['date_expiration']);
    $qte  = trim($saisie['stock']);

    if ($code === '')                          $erreurs[] = 'Le code-barres est obligatoire.';
    if ($nom === '')                           $erreurs[] = 'Le nom du produit est obligatoire.';
    if (!is_numeric($prix) || (float)$prix<=0) $erreurs[] = 'Le prix doit être un nombre positif.';
    if (!ctype_digit($qte) || (int)$qte < 0)   $erreurs[] = 'La quantité doit être un entier positif.';
    if (!valider_date($exp))                   $erreurs[] = 'Date d\'expiration invalide (format MM-JJ-AAAA).';

    if (empty($erreurs)) {
        if (trouver_produit($code) !== null) {
            $erreurs[] = 'Ce code-barres est déjà enregistré dans le catalogue.';
        } else {
            enregistrer_produit($code, $nom, $prix, convertir_date_iso($exp), $qte);
            $ok = 'Produit enregistré avec succès.';
            $saisie = array('code_barre'=>'','nom'=>'','prix'=>'','date_expiration'=>'','stock'=>'');
        }
    }
}

$titre_page = 'Enregistrer un produit';
require __DIR__ . '/../../includes/header.php';
?>
<section class="bloc">
    <h1>Enregistrer un produit</h1>

    <div class="zone-scanner">
        <p>Activez la caméra pour lire un code-barres, ou saisissez-le manuellement.</p>
        <button type="button" id="btn-scan">Activer la caméra</button>
        <button type="button" id="btn-stop" class="secondaire" hidden>Arrêter</button>
        <video id="video" hidden playsinline></video>
        <p id="resultat-scan" class="info-scan"></p>
    </div>

    <?php if ($produit_existant !== null): ?>
        <div class="alerte-info">
            <p>Ce code-barres est déjà connu :</p>
            <ul>
                <li><strong>Nom :</strong> <?= htmlspecialchars($produit_existant['nom']) ?></li>
                <li><strong>Prix HT :</strong> <?= htmlspecialchars($produit_existant['prix_unitaire_ht']) ?> CDF</li>
                <li><strong>Stock :</strong> <?= (int) $produit_existant['quantite_stock'] ?></li>
                <li><strong>Expiration :</strong> <?= htmlspecialchars($produit_existant['date_expiration']) ?></li>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($erreurs)): ?>
        <div class="message-erreur">
            <ul><?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>
    <?php if ($ok !== ''): ?>
        <p class="message-ok"><?= htmlspecialchars($ok) ?></p>
    <?php endif; ?>

    <form method="post" action="" class="formulaire">
        <p>
            <label for="code_barre">Code-barres</label>
            <input type="text" id="code_barre" name="code_barre"
                   value="<?= htmlspecialchars($saisie['code_barre']) ?>" required>
        </p>
        <p>
            <label for="nom">Nom du produit</label>
            <input type="text" id="nom" name="nom"
                   value="<?= htmlspecialchars($saisie['nom']) ?>" required>
        </p>
        <p>
            <label for="prix">Prix unitaire HT (CDF)</label>
            <input type="number" id="prix" name="prix" step="0.01" min="0"
                   value="<?= htmlspecialchars($saisie['prix']) ?>" required>
        </p>
        <p>
            <label for="date_expiration">Date d'expiration (MM-JJ-AAAA)</label>
            <input type="text" id="date_expiration" name="date_expiration"
                   placeholder="12-31-2026"
                   value="<?= htmlspecialchars($saisie['date_expiration']) ?>" required>
        </p>
        <p>
            <label for="stock">Quantité initiale en stock</label>
            <input type="number" id="stock" name="stock" min="0" step="1"
                   value="<?= htmlspecialchars($saisie['stock']) ?>" required>
        </p>
        <p>
            <button type="submit">Enregistrer le produit</button>
        </p>
    </form>
</section>

<script src="https://unpkg.com/@zxing/browser@0.1.5/umd/zxing-browser.min.js"></script>
<script src="<?= $base ?>/assets/js/scanner.js"></script>
<script>
    initScanner({
        boutonStart: document.getElementById('btn-scan'),
        boutonStop:  document.getElementById('btn-stop'),
        video:       document.getElementById('video'),
        resultat:    document.getElementById('resultat-scan'),
        onCode: function(code) {
            document.getElementById('code_barre').value = code;
        }
    });
</script>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
