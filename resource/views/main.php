<?php

use Core\Facedas\Config;
use Core\View;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Config::get('app.title') . (isset($title) ? " | $title" : null) ?></title>
</head>

<body>
    <?= View::view('inc.alerts') ?>
    <!--body-->
</body>

</html>