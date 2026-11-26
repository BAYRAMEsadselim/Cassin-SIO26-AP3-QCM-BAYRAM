<?php $action = $matiere ? '/admin/matieres/' . (int) $matiere['id'] . '/modifier' : '/admin/matieres'; ?>
<h1><?= $matiere ? 'Modifier la matière' : 'Nouvelle matière' ?></h1>
<?php if ($erreur): ?>
    <p class="flash erreur"><?= e($erreur) ?></p>
<?php endif; ?>
<form method="post" action="<?= e($action) ?>">
    <input type="hidden" name="_csrf" value="<?= e(App\Security\Csrf::token()) ?>">
    <label>Libellé
        <input type="text" name="libelle" maxlength="120" required
               value="<?= e($_POST['libelle'] ?? ($matiere['libelle'] ?? '')) ?>">
    </label>
    <button type="submit">Enregistrer</button>
    <a href="/admin/matieres">Annuler</a>
</form>
