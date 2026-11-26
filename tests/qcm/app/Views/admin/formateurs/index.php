<h1>Formateurs et leurs matières</h1>
<p>Les formateurs sont créés dans <a href="/admin/utilisateurs">Comptes</a> (rôle « formateur »).</p>
<table>
    <thead><tr><th>Formateur</th><th>E-mail</th><th>Matières</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($formateurs as $f): ?>
        <tr>
            <td><?= e($f['prenom'] . ' ' . $f['nom']) ?></td>
            <td><?= e($f['email']) ?></td>
            <td><?= e($f['matieres'] ?? '') ?: 'Aucune' ?></td>
            <td><a href="/admin/formateurs/<?= (int) $f['id'] ?>/matieres">Gérer les matières</a></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$formateurs): ?>
        <tr><td colspan="4">Aucun formateur.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
