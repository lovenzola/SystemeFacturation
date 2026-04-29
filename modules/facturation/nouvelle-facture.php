<?php
require_once __DIR__ . '/../../auth/session.php';
exiger_connexion();

// On démarre toujours une nouvelle facture en arrivant ici
$_SESSION['facture_courante'] = array();

$titre_page = 'Nouvelle facture';
require __DIR__ . '/../../includes/header.php';
?>
<section class="bloc">
    <h1>Nouvelle facture</h1>

    <div class="zone-scanner">
        <button type="button" id="btn-scan">Activer la caméra</button>
        <button type="button" id="btn-stop" class="secondaire" hidden>Arrêter</button>
        <video id="video" hidden playsinline></video>
        <p id="resultat-scan" class="info-scan"></p>
    </div>

    <form id="form-ajout" class="ligne-saisie" autocomplete="off">
        <label>Code-barres
            <input type="text" id="code_barre" required>
        </label>
        <label>Quantité
            <input type="number" id="quantite" min="1" value="1" required>
        </label>
        <button type="submit">Ajouter à la facture</button>
    </form>

    <p id="erreur-article" class="message-erreur" hidden></p>

    <table class="tableau" id="table-facture">
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Prix unit. HT</th>
                <th>Qté</th>
                <th>Sous-total HT</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="corps-facture">
            <tr class="vide"><td colspan="5">Aucun article scanné.</td></tr>
        </tbody>
        <tfoot>
            <tr><th colspan="3">Total HT</th><td id="t-ht">0 CDF</td><td></td></tr>
            <tr><th colspan="3">TVA (18%)</th><td id="t-tva">0 CDF</td><td></td></tr>
            <tr><th colspan="3">Net à payer</th><td id="t-ttc"><strong>0 CDF</strong></td><td></td></tr>
        </tfoot>
    </table>

    <div class="actions-facture">
        <button type="button" id="btn-vider" class="secondaire">Vider</button>
        <button type="button" id="btn-valider">Valider et imprimer la facture</button>
    </div>
</section>

<script src="https://unpkg.com/@zxing/browser@0.1.5/umd/zxing-browser.min.js"></script>
<script src="<?= $base ?>/assets/js/scanner.js"></script>
<script>
const URL_CALCUL = '<?= $base ?>/modules/facturation/calcul.php';
const URL_AFFICHE = '<?= $base ?>/modules/facturation/afficher-facture.php';

function formater(m) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(m)) + ' CDF';
}

function rendre(articles, totaux) {
    const corps = document.getElementById('corps-facture');
    corps.innerHTML = '';
    if (!articles || articles.length === 0) {
        corps.innerHTML = '<tr class="vide"><td colspan="5">Aucun article scanné.</td></tr>';
    } else {
        articles.forEach(function(a, i) {
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + a.nom + '</td>' +
                '<td>' + formater(a.prix_unitaire_ht) + '</td>' +
                '<td>' + a.quantite + '</td>' +
                '<td>' + formater(a.sous_total_ht) + '</td>' +
                '<td><button type="button" data-i="' + i + '" class="lien-suppr">retirer</button></td>';
            corps.appendChild(tr);
        });
    }
    document.getElementById('t-ht').textContent  = formater(totaux.total_ht);
    document.getElementById('t-tva').textContent = formater(totaux.tva);
    document.getElementById('t-ttc').innerHTML   = '<strong>' + formater(totaux.total_ttc) + '</strong>';
}

function appel(action, donnees) {
    const fd = new FormData();
    fd.append('action', action);
    if (donnees) {
        for (const k in donnees) fd.append(k, donnees[k]);
    }
    return fetch(URL_CALCUL, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function(r) { return r.json(); });
}

document.getElementById('form-ajout').addEventListener('submit', function(e) {
    e.preventDefault();
    const code = document.getElementById('code_barre').value.trim();
    const qte  = document.getElementById('quantite').value;
    const erreur = document.getElementById('erreur-article');
    erreur.hidden = true;

    appel('ajouter', { code_barre: code, quantite: qte }).then(function(rep) {
        if (!rep.ok) {
            erreur.textContent = rep.erreur;
            erreur.hidden = false;
            return;
        }
        rendre(rep.articles, rep.totaux);
        document.getElementById('code_barre').value = '';
        document.getElementById('quantite').value = '1';
        document.getElementById('code_barre').focus();
    });
});

document.getElementById('corps-facture').addEventListener('click', function(e) {
    if (e.target.classList.contains('lien-suppr')) {
        appel('retirer', { index: e.target.dataset.i }).then(function(rep) {
            rendre(rep.articles, rep.totaux);
        });
    }
});

document.getElementById('btn-vider').addEventListener('click', function() {
    appel('vider').then(function(rep) { rendre(rep.articles, rep.totaux); });
});

document.getElementById('btn-valider').addEventListener('click', function() {
    appel('valider').then(function(rep) {
        if (!rep.ok) {
            const erreur = document.getElementById('erreur-article');
            erreur.textContent = rep.erreur;
            erreur.hidden = false;
            return;
        }
        window.location.href = URL_AFFICHE + '?id=' + encodeURIComponent(rep.id_facture);
    });
});

initScanner({
    boutonStart: document.getElementById('btn-scan'),
    boutonStop:  document.getElementById('btn-stop'),
    video:       document.getElementById('video'),
    resultat:    document.getElementById('resultat-scan'),
    onCode: function(code) {
        document.getElementById('code_barre').value = code;
        document.getElementById('quantite').focus();
    }
});
</script>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
