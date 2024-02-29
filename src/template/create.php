<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<div class="row mb-5">
    <div class="col">
        <h1><?= $form['name'] ?></h1>
    </div>
    <div class="col text-end mt-2">
        <a href="<?= App::url('') ?>" class="btn btn-sm btn-secondary">Back</a>
    </div>
</div>

<?= $formObj->render(App::url('submit')) ?>
