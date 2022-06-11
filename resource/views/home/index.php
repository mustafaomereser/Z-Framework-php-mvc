<?php

use Core\Facedas\Config;
use Core\Facedas\Lang;

?>
<style>
    fieldset {
        margin-bottom: 25px;
    }

    legend {
        font-weight: bold;
    }
</style>

<fieldset>
    <legend>Patch Example</legend>
    <form method="POST" action="/1">
        <?= csrf() ?>
        <?= inputMethod('PATCH') ?>

        Data's value: <?= Config::get('test.value') ?? 'Nothing.' ?>
        <br>
        Click `Increse` button for increse data.
        <br>
        <button type="submit">Increse</button>
    </form>
</fieldset>

<fieldset>
    <legend>Lang Example</legend>
    <?= Lang::get('lang.test', ['id' => 'id', 'test' => 'TEST']) ?>
</fieldset>

<fieldset>
    <legend>File Upload Example</legend>
    <form method="POST" enctype="multipart/form-data">
        <?= csrf() ?>
        <input type="file" name="file" id="file">
        <button type="submit">upload</button>
    </form>
</fieldset>