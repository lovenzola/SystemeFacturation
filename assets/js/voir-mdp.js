// Affiche/masque les mots de passe via une petite icône d'œil

(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var champs = document.querySelectorAll('input[type="password"]');
        for (var i = 0; i < champs.length; i++) {
            ajouter_toggle(champs[i]);
        }
    });

    function ajouter_toggle(champ) {
        if (champ.dataset.oeilApplique === '1') return;
        champ.dataset.oeilApplique = '1';

        var conteneur = document.createElement('span');
        conteneur.className = 'champ-mdp';
        champ.parentNode.insertBefore(conteneur, champ);
        conteneur.appendChild(champ);

        var bouton = document.createElement('button');
        bouton.type = 'button';
        bouton.className = 'voir-mdp';
        bouton.setAttribute('aria-label', 'Afficher le mot de passe');
        bouton.innerHTML = svg_oeil(true);

        bouton.addEventListener('click', function () {
            if (champ.type === 'password') {
                champ.type = 'text';
                bouton.innerHTML = svg_oeil(false);
                bouton.setAttribute('aria-label', 'Masquer le mot de passe');
            } else {
                champ.type = 'password';
                bouton.innerHTML = svg_oeil(true);
                bouton.setAttribute('aria-label', 'Afficher le mot de passe');
            }
        });

        conteneur.appendChild(bouton);
    }

    function svg_oeil(ouvert) {
        var commun = '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>' +
                     '<circle cx="12" cy="12" r="3"/>';
        var barre  = ouvert ? '' : '<line x1="3" y1="3" x2="21" y2="21"/>';
        return '<svg viewBox="0 0 24 24" width="18" height="18" ' +
               'fill="none" stroke="currentColor" stroke-width="1.7" ' +
               'stroke-linecap="round" stroke-linejoin="round">' +
               commun + barre + '</svg>';
    }
})();
