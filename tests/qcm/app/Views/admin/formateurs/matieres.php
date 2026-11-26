<h1>Matières de <?= e($formateur['prenom'] . ' ' . $formateur['nom']) ?></h1>
<?php if ($erreur): ?>
    <p class="flash erreur"><?= e($erreur) ?></p>
<?php endif; ?>
<form method="post" action="/admin/formateurs/<?= (int) $formateur['id'] ?>/matieres">
    <input type="hidden" name="_csrf" value="<?= e(App\Security\Csrf::token()) ?>">
    <p>Cochez les matières que ce formateur assure :</p>
    <?php foreach ($matieres as $m): ?>
        <label>
            <input type="checkbox" name="matieres[]" value="<?= (int) $m['id'] ?>"
                <?= in_array((int) $m['id'], $cochees, true) ? 'checked' : '' ?>>
            <?= e($m['libelle']) ?>
        </label>
    <?php endforeach; ?>
    <?php if (!$matieres): ?>
        <p>Aucune matière n'existe encore. <a href="/admin/matieres/creer">Créer une matière</a>.</p>
    <?php endif; ?>
    <button type="submit">Enregistrer</button>
    <a href="/admin/formateurs">Annuler</a>
</form>
