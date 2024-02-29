<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test Project</title>
    <link href="<?= App::asset('bootstrap.min.css') ?>" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 px-2 py-5 m-auto bg-white rounded-3">
                <div class="row justify-content-md-center">
                    <div class="col-11">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= App::asset('jquery-3.7.1.min.js') ?>" crossorigin="anonymous"></script>
    <script src="<?= App::asset('bootstrap.bundle.min.js') ?>" crossorigin="anonymous"></script>
    <script src="<?= App::asset('script.js') ?>" crossorigin="anonymous"></script>
</body>
</html>
