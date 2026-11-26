<?php
$action = $utilisateur ? '/admin/utilisateurs/' . (int) $utilisateur['id'] . '/modifier' : '/admin/utilisateurs';
?>
<h1><?= $utilisateur ? 'Modifier le compte' : 'Nouveau compte' ?></h1>

<?php foreach ($erreurs as $erreur): ?>
    <p class="flash erreur"><?= e($erreur) ?></p>
<?php endforeach; ?>

<form method="post" action="<?= e($action) ?>">
    <input type="hidden" name="_csrf" value="<?= e(App\Security\Csrf::token()) ?>">

    <label>Nom
        <input type="text" name="nom" maxlength="80" required value="<?= e($valeurs['nom'] ?? '') ?>">
    </label>
    <label>Prénom
        <input type="text" name="prenom" maxlength="80" required value="<?= e($valeurs['prenom'] ?? '') ?>">
    </label>
    <label>E-mail
        <input type="email" name="email" maxlength="190" required value="<?= e($valeurs['email'] ?? '') ?>">
    </label>
    <label>Rôle
        <select name="role" <?= $estMoi ? 'disabled' : '' ?>>
            <?php foreach (App\Models\Utilisateur::ROLES as $r): ?>
                <option value="<?= e($r) ?>" <?= ($valeurs['role'] ?? '') === $r ? 'selected' : '' ?>><?= e($r) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($estMoi): ?><small>Vous ne pouvez pas changer votre propre rôle.</small><?php endif; ?>
    </label>
    <label>Mot de passe <?= $utilisateur ? '(laisser vide pour ne pas le changer)' : '' ?>
        <input type="password" name="password" minlength="8" autocomplete="new-password" <?= $utilisateur ? '' : 'required' ?>>
    </label>

    <button type="submit">Enregistrer</button>
    <a href="/admin/utilisateurs">Annuler</a>
</form>
