<?php
if (isset($_GET['crypt-key-create'])) {
    \zFramework\Kernel\Terminal::begin(["terminal", "security key --regen"]);
?>
    <meta http-equiv="refresh" content="0; url=/" />
<?php
}
?>
<div class="mb-2">
    You must create a unique crypt key for per project.
</div>
<div>
    <a href="?crypt-key-create=true" class="btn btn-success">Create Crypt Key & Salt</a>
</div>