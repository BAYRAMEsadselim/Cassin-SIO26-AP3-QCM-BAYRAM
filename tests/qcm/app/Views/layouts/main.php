<?php
use App\Security\Auth;
use App\Security\Authorization;
use App\Security\Csrf;
use App\Security\Permission;

$user = Auth::user();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
    <title>Plateforme QCM</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header>
    <strong>Plateforme QCM</strong>
    <?php if ($user): ?>
        <nav>
            <?php if (Authorization::peut(Permission::GERER_COMPTES)): ?>
                <a href="/admin/utilisateurs">Comptes</a>
                <a href="/admin/formateurs">Formateurs</a>
            <?php endif; ?>
            <?php if (Authorization::peut(Permission::GERER_MATIERES)): ?>
                <a href="/admin/matieres">Matières</a>
            <?php endif; ?>
            <?php if (Authorization::peut(Permission::GERER_CLASSES)): ?>
                <a href="/admin/classes">Classes</a>
            <?php endif; ?>
            <?php if (Authorization::peut(Permission::GERER_MOTS_CLES)): ?>
                <a href="/admin/mots-cles">Mots-clés</a>
            <?php endif; ?>
            <?php if (Authorization::peut(Permission::GERER_QCM)): ?>
                <a href="/formateur/qcm">Mes QCM</a>
            <?php endif; ?>
            <?php if (Authorization::peut(Permission::PASSER_QCM)): ?>
                <a href="/etudiant/qcm">Mes QCM</a>
            <?php endif; ?>
        </nav>
        <form method="post" action="/logout" class="inline">
            <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
            <?= e($user['prenom'] . ' ' . $user['nom']) ?> (<?= e($user['role']) ?>)
            <button type="submit">Déconnexion</button>
        </form>
    <?php endif; ?>
</header>
<main>
    <?php if ($flash): ?>
        <p class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></p>
    <?php endif; ?>
    <?= $content ?>
</main>
<script type="module" src="/assets/js/main.js"></script>
</body>
</html>
