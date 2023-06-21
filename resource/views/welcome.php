<?php

use zFramework\Core\Facades\Lang;
?>
<!DOCTYPE html>
<html lang="<?= Lang::$locale ?>" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>zFramework</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-lg-5 my-2">
        <div class="clearfix">
            <div class="float-start">
                <a href="https://github.com/mustafaomereser/Z-Framework-php-mvc" target="_blank">Github & Docs</a>
            </div>
            <div class="float-end">
                <div class="btn-group">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 100px">
                        <?= _l('lang.languages') ?>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach (Lang::list() as $lang) : ?>
                            <li><a class="dropdown-item <?= Lang::currentLocale() == $lang ? 'active' : null ?>" href="<?= route('language', ['lang' => $lang]) ?>"><?= $lang ?></a></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="my-5">
            <div class="text-center mb-4">
                <h1><?= _l('lang.welcome') ?></h1>
            </div>

            <!-- <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12 mb-3">
                    <div class="card">
                        <div class="card-body">

                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12 mb-3">
                    <div class="card">
                        <div class="card-body">

                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12 mb-3">
                    <div class="card">
                        <div class="card-body">

                        </div>
                    </div>
                </div>
            </div> -->
        </div>

        <div class="text-lg-end text-center">
            <small>
                <b>zFramework</b> v<?= FRAMEWORK_VERSION ?> | <b>PHP</b> v<?= PHP_VERSION ?> | <b>APP</b> v<?= config('app.version') ?>
            </small>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>