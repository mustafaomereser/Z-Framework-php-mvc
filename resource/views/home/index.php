<?php

use Core\Facedas\Config;
use Core\Facedas\Lang;

?>
<form method="POST" action="/1">
    <?= csrf() ?>
    <?= inputMethod('PATCH') ?>

    Data's value: <?= Config::get('test.value') ?? 'Nothing.' ?>
    <br>
    Click `Increse` button for increse data.
    <br>
    <button type="submit">Increse</button>
</form>

<br>
<?= Lang::get('lang.test', ['id' => 'id', 'test' => 'TEST']) ?>

<br>
<br>
<div>
    file upload example
    <form method="POST" enctype="multipart/form-data">
        <?= csrf() ?>
        <input type="file" name="file" id="file">
        <button type="submit">upload</button>
    </form>
</div>