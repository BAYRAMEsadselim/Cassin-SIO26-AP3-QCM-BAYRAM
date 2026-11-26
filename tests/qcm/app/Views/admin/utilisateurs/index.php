<h1>Comptes</h1>
<p><a class="bouton" href="/admin/utilisateurs/creer">Nouveau compte</a></p>
<table>
    <thead><tr><th>Nom</th><th>Prénom</th><th>E-mail</th><th>Rôle</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($utilisateurs as $u): ?>
        <tr>
            <td><?= e($u['nom']) ?></td>
            <td><?= e($u['prenom']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['role']) ?></td>
            <td>
                <a href="/admin/utilisateurs/<?= (int) $u['id'] ?>/modifier">Modifier</a>
                <?php if ((int) $u['id'] !== $moiId): ?>
                    <form method="post" action="/admin/utilisateurs/<?= (int) $u['id'] ?>/supprimer" class="inline"
                          onsubmit="return confirm('Supprimer ce compte ?');">
                        <input type="hidden" name="_csrf" value="<?= e(App\Security\Csrf::token()) ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
