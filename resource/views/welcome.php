<?php

use Core\Facedas\Lang;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>

    <style>
        body {
            background-color: #1B2430;
        }

        .container {
            margin: 15% 20%;
        }

        .box {
            width: 100%;
            font-size: 19px;
            color: #ddd;
            background-color: rgba(81, 85, 126, .7);
            border-radius: 5px;
            padding: 10px 15px;
        }

        .text {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            color: #D6D5A8;
        }

        a {
            color: #D6D5A8;
        }

        .active {
            color: #816797;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="text" style="overflow: hidden;">
            <div style="float: left;">
                Z Framework
            </div>
            <div style="float: right;">
                <a href="https://github.com/mustafaomereser/Z-Framework-php-mvc" target="_blank">Github & Docs</a>
            </div>
        </h2>
        <div class="box">
            <div style="text-align: center;">
                <?= Lang::get('lang.welcome') ?>
            </div>
            <div>
                <?= Lang::get('lang.languages') ?>: (<?= Lang::get('lang.current') ?>: <?= Lang::currentLocale() ?>)
                <ul>
                    <?php foreach (Lang::list() as $lang) : ?>
                        <li>
                            <a href="/language/<?= $lang ?>" class="<?= Lang::currentLocale() == $lang ? 'active' : 'text' ?>"><?= $lang ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

</body>

</html>