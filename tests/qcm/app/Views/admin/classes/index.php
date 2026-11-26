<h1>Classes</h1>
<p><a class="bouton" href="/admin/classes/creer">Nouvelle classe</a></p>
<table>
    <thead><tr><th>Année</th><th>Classe</th><th>Étudiants</th><th>Matières</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($classes as $c): ?>
        <tr>
            <td><?= (int) $c['annee'] ?></td>
            <td><?= e($c['nom']) ?></td>
            <td><?= (int) $c['nb_etudiants'] ?></td>
            <td><?= (int) $c['nb_matieres'] ?></td>
            <td>
                <a href="/admin/classes/<?= (int) $c['id'] ?>">Gérer</a>
                <a href="/admin/classes/<?= (int) $c['id'] ?>/modifier">Modifier</a>
                <form method="post" action="/admin/classes/<?= (int) $c['id'] ?>/supprimer" class="inline"
                      onsubmit="return confirm('Supprimer cette classe ?');">
                    <input type="hidden" name="_csrf" value="<?= e(App\Security\Csrf::token()) ?>">
                    <button type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$classes): ?>
        <tr><td colspan="5">Aucune classe.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
