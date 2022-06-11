<?php

use Core\Facedas\Lang;
use Core\Csrf;
?>

<form method="POST" action="/1">
    <?= Csrf::csrf(); ?>
    <?= inputMethod('PATCH') ?>
    <button type="submit">Gönder</button>
</form>

<?= Lang::get('lang.test') ?>;