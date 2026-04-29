<?php
require_once __DIR__ . '/../config/config.php';

function lire_utilisateurs() {
    if (!file_exists(FICHIER_UTILISATEURS)) {
        return array();
    }
    $contenu = file_get_contents(FICHIER_UTILISATEURS);
    $data = json_decode($contenu, true);
    return is_array($data) ? $data : array();
}

function ecrire_utilisateurs($utilisateurs) {
    $json = json_encode($utilisateurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents(FICHIER_UTILISATEURS, $json, LOCK_EX);
}

function trouver_utilisateur($identifiant) {
    $utilisateurs = lire_utilisateurs();
    foreach ($utilisateurs as $u) {
        if ($u['identifiant'] === $identifiant) {
            return $u;
        }
    }
    return null;
}

function authentifier($identifiant, $mot_de_passe) {
    $u = trouver_utilisateur($identifiant);
    if ($u === null || empty($u['actif'])) {
        return false;
    }
    if (!password_verify($mot_de_passe, $u['mot_de_passe'])) {
        return false;
    }
    return $u;
}

function ajouter_utilisateur($identifiant, $mot_de_passe, $role, $nom_complet) {
    $utilisateurs = lire_utilisateurs();
    foreach ($utilisateurs as $u) {
        if ($u['identifiant'] === $identifiant) {
            return false;
        }
    }
    $utilisateurs[] = array(
        'identifiant'     => $identifiant,
        'mot_de_passe'    => password_hash($mot_de_passe, PASSWORD_DEFAULT),
        'role'            => $role,
        'nom_complet'     => $nom_complet,
        'date_creation'   => date('Y-m-d'),
        'actif'           => true,
    );
    ecrire_utilisateurs($utilisateurs);
    return true;
}

function supprimer_utilisateur($identifiant) {
    $utilisateurs = lire_utilisateurs();
    $nouveau = array();
    $trouve = false;
    foreach ($utilisateurs as $u) {
        if ($u['identifiant'] === $identifiant) {
            $trouve = true;
            continue;
        }
        $nouveau[] = $u;
    }
    if ($trouve) {
        ecrire_utilisateurs($nouveau);
    }
    return $trouve;
}
