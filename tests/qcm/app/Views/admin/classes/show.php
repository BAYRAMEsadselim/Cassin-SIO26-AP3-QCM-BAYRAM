<?php $id = (int) $classe['id']; $csrf = App\Security\Csrf::token(); ?>
<h1><?= e($classe['nom']) ?> <small>(<?= (int) $classe['annee'] ?>)</small></h1>
<p><a href="/admin/classes">&larr; Retour aux classes</a></p>

<h2>Étudiants</h2>
<table>
    <thead><tr><th>Nom</th><th>Prénom</th><th>E-mail</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($etudiants as $et): ?>
        <tr>
            <td><?= e($et['nom']) ?></td>
            <td><?= e($et['prenom']) ?></td>
            <td><?= e($et['email']) ?></td>
            <td>
                <form method="post" action="/admin/classes/<?= $id ?>/etudiants/<?= (int) $et['id'] ?>/retirer" class="inline">
                    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                    <button type="submit">Retirer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$etudiants): ?>
        <tr><td colspan="4">Aucun étudiant dans cette classe.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<?php if ($disponibles): ?>
    <form method="post" action="/admin/classes/<?= $id ?>/etudiants">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <label>Ajouter un étudiant
            <select name="etudiant_id" required>
                <option value="">Choisir...</option>
                <?php foreach ($disponibles as $d): ?>
                    <option value="<?= (int) $d['id'] ?>"><?= e($d['nom'] . ' ' . $d['prenom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Ajouter</button>
    </form>
<?php else: ?>
    <p>Aucun étudiant disponible pour <?= (int) $classe['annee'] ?> (créez des comptes « etudiant » dans <a href="/admin/utilisateurs">Comptes</a>).</p>
<?php endif; ?>

<h2>Matières et formateurs</h2>
<table>
    <thead><tr><th>Matière</th><th>Formateur</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($matieres as $m): ?>
        <tr>
            <td><?= e($m['matiere']) ?></td>
            <td><?= e($m['prenom'] . ' ' . $m['nom']) ?></td>
            <td>
                <form method="post" action="/admin/classes/<?= $id ?>/matieres/<?= (int) $m['id'] ?>/retirer" class="inline"
                      onsubmit="return confirm('Retirer cette matière de la classe ?');">
                    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                    <button type="submit">Retirer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$matieres): ?>
        <tr><td colspan="3">Aucune matière dans cette classe.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<?php if ($affectations): ?>
    <form method="post" action="/admin/classes/<?= $id ?>/matieres">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <label>Ajouter une matière (avec son formateur)
            <select name="affectation" required>
                <option value="">Choisir...</option>
                <?php foreach ($affectations as $a): ?>
                    <option value="<?= (int) $a['formateur_id'] ?>:<?= (int) $a['matiere_id'] ?>">
                        <?= e($a['matiere'] . ' - ' . $a['prenom'] . ' ' . $a['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Ajouter</button>
    </form>
<?php else: ?>
    <p>Aucune matière disponible. Affectez d'abord des matières à un formateur dans <a href="/admin/formateurs">Formateurs</a>.</p>
<?php endif; ?>
