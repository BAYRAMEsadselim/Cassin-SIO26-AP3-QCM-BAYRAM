<h1>Matières</h1>
<p><a class="bouton" href="/admin/matieres/creer">Nouvelle matière</a></p>
<table>
    <thead><tr><th>Libellé</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($matieres as $m): ?>
        <tr>
            <td><?= e($m['libelle']) ?></td>
            <td><?= $m['verrouillee'] ? 'Affectée (verrouillée)' : 'Libre' ?></td>
            <td>
                <?php if (!$m['verrouillee']): ?>
                    <a href="/admin/matieres/<?= (int) $m['id'] ?>/modifier">Modifier</a>
                    <form method="post" action="/admin/matieres/<?= (int) $m['id'] ?>/supprimer" class="inline"
                          onsubmit="return confirm('Supprimer cette matière ?');">
                        <input type="hidden" name="_csrf" value="<?= e(App\Security\Csrf::token()) ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$matieres): ?>
        <tr><td colspan="3">Aucune matière.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
