<h1>Mots-clés thématiques</h1>
<p><a class="bouton" href="/admin/mots-cles/creer">Nouveau mot-clé</a></p>
<table>
    <thead><tr><th>Mot-clé</th><th>Questions</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($motsCles as $k): ?>
        <?php $verrouille = (int) $k['nb_questions'] > 0; ?>
        <tr>
            <td><?= e($k['libelle']) ?></td>
            <td><?= (int) $k['nb_questions'] ?></td>
            <td><?= $verrouille ? 'Utilisé (verrouillé)' : 'Libre' ?></td>
            <td>
                <?php if (!$verrouille): ?>
                    <a href="/admin/mots-cles/<?= (int) $k['id'] ?>/modifier">Modifier</a>
                    <form method="post" action="/admin/mots-cles/<?= (int) $k['id'] ?>/supprimer" class="inline"
                          onsubmit="return confirm('Supprimer ce mot-clé ?');">
                        <input type="hidden" name="_csrf" value="<?= e(App\Security\Csrf::token()) ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$motsCles): ?>
        <tr><td colspan="4">Aucun mot-clé.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
