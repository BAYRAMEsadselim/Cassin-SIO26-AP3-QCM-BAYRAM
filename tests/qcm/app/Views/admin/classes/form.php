<?php $action = $classe ? '/admin/classes/' . (int) $classe['id'] . '/modifier' : '/admin/classes'; ?>
<h1><?= $classe ? 'Modifier la classe' : 'Nouvelle classe' ?></h1>
<?php if ($erreur): ?>
    <p class="flash erreur"><?= e($erreur) ?></p>
<?php endif; ?>
<form method="post" action="<?= e($action) ?>">
    <input type="hidden" name="_csrf" value="<?= e(App\Security\Csrf::token()) ?>">
    <label>Nom de la classe
        <input type="text" name="nom" maxlength="80" required value="<?= e($valeurs['nom'] ?? '') ?>">
    </label>
    <label>Année
        <input type="number" name="annee" min="2000" max="2100" required value="<?= e($valeurs['annee'] ?? '') ?>">
    </label>
    <button type="submit">Enregistrer</button>
    <a href="/admin/classes">Annuler</a>
</form>
