<h1>Connexion</h1>
<?php if ($erreur): ?>
    <p class="flash erreur"><?= e($erreur) ?></p>
<?php endif; ?>
<form method="post" action="/login">
    <input type="hidden" name="_csrf" value="<?= e(App\Security\Csrf::token()) ?>">
    <label>E-mail <input type="email" name="email" required autofocus></label>
    <label>Mot de passe <input type="password" name="password" required></label>
    <button type="submit">Se connecter</button>
</form>
