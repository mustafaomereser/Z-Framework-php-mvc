<?php

use Core\Csrf;
?>

<form>
    <?= Csrf::csrf(); ?>
    <?= inputMethod('PUT') ?>
    <button type="submit">Gönder</button>
</form>