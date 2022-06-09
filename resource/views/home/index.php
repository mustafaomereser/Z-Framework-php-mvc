<?php

use Core\Csrf;
?>

<form method="POST">
    <?= Csrf::csrf(); ?>
    <?= inputMethod('PATCH') ?>
    <button type="submit">Gönder</button>
</form>