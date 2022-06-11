<?php

use Core\Facedas\Config;
use Core\Facedas\Lang;

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

<?= Lang::get('lang.test', ['id' => 'id', 'test' => 'TEST']) ?>