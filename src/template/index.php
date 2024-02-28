
<h1 class="text-center mb-5">Forms</h1>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($forms) > 0) : ?>
        <?php foreach ($forms as $form) : ?>
            <tr>
                <td><?= $form['id'] ?></td>
                <td><?= $form['name'] ?></td>
                <td><?= $form['created_at'] ?></td>
                <td><?= $form['updated_at'] ?></td>
                <td>
                    <a href="/submissions?id=<?= $form['id'] ?>">All Submission</a>
                    <a href="/new?id=<?= $form['id'] ?>">New</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td class="text-center" colspan="5">No form found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
