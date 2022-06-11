<?php

use Core\Facedas\Config;
?>
<form method="POST" action="/1">
    <?= csrf() ?>
    <?= inputMethod('PATCH') ?>

    Data's value: <?= Config::get('test.value') ?? 'Nothing.' ?>
    <br>
    Click `Update` button, update ID data.
    <br>
    <button type="submit">Update</button>
</form>