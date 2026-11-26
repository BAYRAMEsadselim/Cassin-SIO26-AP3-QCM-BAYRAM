<?php $action = $motCle ? '/admin/mots-cles/' . (int) $motCle['id'] . '/modifier' : '/admin/mots-cles'; ?>
<h1><?= $motCle ? 'Modifier le mot-clé' : 'Nouveau mot-clé' ?></h1>
<?php if ($erreur): ?>
    <p class="flash erreur"><?= e($erreur) ?></p>
<?php endif; ?>
<form method="post" action="<?= e($action) ?>">
    <input type="hidden" name="_csrf" value="<?= e(App\Security\Csrf::token()) ?>">
    <label>Libellé
        <input type="text" name="libelle" maxlength="80" required
               value="<?= e($_POST['libelle'] ?? ($motCle['libelle'] ?? '')) ?>">
    </label>
    <button type="submit">Enregistrer</button>
    <a href="/admin/mots-cles">Annuler</a>
</form>
