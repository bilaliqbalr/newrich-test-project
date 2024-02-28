<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test Project</title>
    <link href="<?= App::asset('bootstrap.min.css') ?>" rel="stylesheet" crossorigin="anonymous">
</head>
<body>

<div class="container my-5">
    <h1>Test Project</h1>
    <div class="col-lg-8 px-0">
        <?= $content ?>
    </div>
</div>

<script src="<?= App::asset('bootstrap.bundle.min.js') ?>" crossorigin="anonymous"></script>
</body>
</html>
