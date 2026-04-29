
function initScanner(opts) {
    var lecteur = null;
    var actif = false;
    var minuteurAlerte = null;
    var debut = 0;

    opts.boutonStart.addEventListener('click', function () {
        if (actif) return;
        if (typeof ZXingBrowser === 'undefined') {
            afficher('Bibliothèque de scan introuvable.', 'erreur');
            return;
        }

        lecteur = new ZXingBrowser.BrowserMultiFormatReader();
        opts.video.hidden = false;
        opts.boutonStop.hidden = false;
        opts.boutonStart.hidden = true;
        debut = Date.now();
        afficher('Caméra activée — approchez le code-barres de l\'objectif.', 'info');

        // Au bout de 3 secondes sans lecture, on prévient l'utilisateur
        minuteurAlerte = setTimeout(function () {
            if (actif) {
                afficher(
                    'Aucun code détecté après 3 secondes. Vérifiez l\'éclairage, '
                    + 'la distance (10–20 cm) et la netteté du code.',
                    'alerte'
                );
            }
        }, 3000);

        lecteur.decodeFromVideoDevice(undefined, opts.video, function (resultat, erreur) {
            if (resultat) {
                clearTimeout(minuteurAlerte);
                var code = resultat.getText();
                afficher('Code lu : ' + code, 'ok');
                if (typeof opts.onCode === 'function') {
                    opts.onCode(code);
                }
                arreter();
                return;
            }
            // Les erreurs frame-par-frame de type NotFoundException sont normales
            // (chaque image sans code en déclenche une), on les ignore. Les vraies
            // erreurs matérielles arrivent dans le .catch ci-dessous.
        }).catch(function (e) {
            clearTimeout(minuteurAlerte);
            var msg = (e && e.message) ? e.message : String(e);
            afficher('Erreur d\'accès à la caméra : ' + msg, 'erreur');
            arreter();
        });

        actif = true;
    });

    opts.boutonStop.addEventListener('click', function () {
        var duree = Math.round((Date.now() - debut) / 1000);
        arreter();
        afficher('Scan arrêté après ' + duree + ' s. Aucun code lu.', 'alerte');
    });

    function arreter() {
        clearTimeout(minuteurAlerte);
        if (lecteur && typeof lecteur.reset === 'function') {
            try { lecteur.reset(); } catch (e) {}
        }
        if (opts.video.srcObject) {
            opts.video.srcObject.getTracks().forEach(function (t) { t.stop(); });
            opts.video.srcObject = null;
        }
        opts.video.hidden = true;
        opts.boutonStop.hidden = true;
        opts.boutonStart.hidden = false;
        actif = false;
    }

    function afficher(texte, type) {
        opts.resultat.textContent = texte;
        opts.resultat.className = 'message-' + type;
    }
}
