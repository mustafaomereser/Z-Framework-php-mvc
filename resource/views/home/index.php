<?php

use Core\Facedas\Config;
use Core\Facedas\Lang;

?>
<div>
    <h5>Patch Example</h5>
    <form method="POST" action="/1">
        <?= csrf() ?>
        <?= inputMethod('PATCH') ?>

        Data's value: <?= Config::get('test.value') ?? 'Nothing.' ?>
        <br>
        Click `Increse` button for increse data.
        <br>
        <button type="submit">Increse</button>
    </form>
</div>

<div>
    <h5>Lang Example</h5>
    <?= Lang::get('lang.test', ['id' => 'id', 'test' => 'TEST']) ?>
</div>
<div>
    <h5>File Upload Example</h5>
    <form method="POST" enctype="multipart/form-data">
        <?= csrf() ?>
        <input type="file" name="file" id="file">
        <button type="submit">upload</button>
    </form>
</div>