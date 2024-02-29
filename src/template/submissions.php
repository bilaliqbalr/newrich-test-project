
<div class="row mb-5">
    <div class="col">
        <h1 class="h3"><?= $form['name'] ?> Submissions</h1>
    </div>
    <div class="col text-end mt-2">
        <a href="<?= App::url("new?id=$form[id]") ?>" class="btn btn-sm btn-success">Create New</a>
        <a href="<?= App::url('') ?>" class="btn btn-sm btn-secondary">Back</a>
    </div>
</div>

<table class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Submitted At</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($submissions) > 0) : ?>
        <?php foreach ($submissions as $submission) : ?>
            <tr>
                <td><?= $submission['id'] ?></td>
                <td><?= $submission['submitted_at'] ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-id="<?= $submission['id'] ?>" data-bs-target="#submissionModal">
                        View
                    </button>
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

<div class="modal fade" id="submissionModal" tabindex="-1" aria-labelledby="submissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="submissionModalLabel">Submitted Data</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="submission-details"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    var submissions = <?= json_encode($submissions) ?>;
</script>
