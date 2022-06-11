<?php

use Core\Facedas\Alerts;
?>

<ul>
    <?php foreach (Alerts::get() as $alert) : ?>
        <li>
            <?= $alert[0] ?>: <?= $alert[1] ?>
        </li>
    <?php endforeach; ?>
</ul>