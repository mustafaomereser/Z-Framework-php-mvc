<?php

use Core\Csrf;
?>

<form>
    <?= Csrf::csrf(); ?>
    <?= showMethod('PUT') ?>
    <button type="submit">Gönder</button>
</form>