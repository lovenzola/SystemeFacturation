<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

function url_base() {
    // On déduit le préfixe d'URL du projet en comparant le chemin
    // physique du script en cours et la racine du projet.
    $script_fs  = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
    $racine_fs  = str_replace('\\', '/', CHEMIN_RACINE);
    $script_url = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);

    if ($racine_fs !== '' && strpos($script_fs, $racine_fs) === 0) {
        $relatif = substr($script_fs, strlen($racine_fs));
        if ($relatif !== '' && substr($script_url, -strlen($relatif)) === $relatif) {
            return rtrim(substr($script_url, 0, -strlen($relatif)), '/');
        }
    }
    return '';
}

function utilisateur_connecte() {
    return isset($_SESSION['utilisateur']) ? $_SESSION['utilisateur'] : null;
}

function exiger_connexion() {
    if (utilisateur_connecte() === null) {
        header('Location: ' . url_base() . '/auth/login.php');
        exit;
    }
}

function exiger_role($roles_autorises) {
    exiger_connexion();
    $u = utilisateur_connecte();
    if (!in_array($u['role'], $roles_autorises, true)) {
        header('Location: ' . url_base() . '/index.php?erreur=acces_refuse');
        exit;
    }
}

function role_courant() {
    $u = utilisateur_connecte();
    return $u ? $u['role'] : null;
}

function libelle_role($role) {
    switch ($role) {
        case 'caissier':    return 'Caissier';
        case 'manager':     return 'Manager';
        case 'super_admin': return 'Super Administrateur';
        default:            return $role;
    }
}
