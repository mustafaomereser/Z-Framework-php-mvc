<?php

use Core\View;
?>
Home
<br>
(test parameter = <?= $test ?>)
<br>
<?= View::view('home.include', $view_parameters) ?>